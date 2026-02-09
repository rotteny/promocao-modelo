<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CupomFiscal;
use App\Models\NumeroDaSorte;
use App\Models\Participante;
use App\Models\ProdutoParticipante;
use App\Models\Setting;
use App\Services\PromocaoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly PromocaoService $promocaoService,
    ) {}

    /**
     * Painel administrativo principal.
     */
    public function index()
    {
        $stats = [
            'total_participantes' => Participante::count(),
            'total_cupons' => CupomFiscal::count(),
            'cupons_concluidos' => CupomFiscal::where('status', CupomFiscal::STATUS_CONCLUIDO)->count(),
            'cupons_na_fila' => CupomFiscal::whereIn('status', [
                CupomFiscal::STATUS_VALIDADO,
                CupomFiscal::STATUS_PROCESSANDO,
            ])->count(),
            'cupons_erro' => CupomFiscal::where('status', CupomFiscal::STATUS_ERRO)->count(),
            'cupons_pendentes' => CupomFiscal::where('status', CupomFiscal::STATUS_PENDENTE)->count(),
            'total_numeros' => NumeroDaSorte::count(),
            'total_produtos' => ProdutoParticipante::count(),
        ];

        $filaBloqueada = Setting::getValue('fila_bloqueada') === 'true';
        $cupomErroId = Setting::getValue('fila_cupom_erro_id');
        $cupomComErro = $cupomErroId ? CupomFiscal::with('participante')->find($cupomErroId) : null;

        // Notificações não lidas do admin logado
        $admin = Auth::guard('admin')->user();
        $notificacoes = $admin->unreadNotifications()->latest()->take(10)->get();

        $promocao = $this->promocaoService;

        return view('admin.dashboard', compact('stats', 'filaBloqueada', 'cupomComErro', 'notificacoes', 'promocao'));
    }

    /**
     * Dados para o gráfico de evolução diária de cadastros (últimos 30 dias).
     */
    public function chartCadastrosDiarios(): JsonResponse
    {
        $inicio = Carbon::now()->subDays(20)->startOfDay();

        $cadastros = Participante::where('created_at', '>=', $inicio)
            ->selectRaw("created_at::date as dia, COUNT(*) as total")
            ->groupByRaw("created_at::date")
            ->orderByRaw("created_at::date")
            ->get()
            ->pluck('total', 'dia')
            ->mapWithKeys(fn ($total, $dia) => [(string) $dia => (int) $total]);

        $labels = [];
        $valores = [];

        for ($i = 20; $i >= 0; $i--) {
            $data = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($data)->format('d/m');
            $valores[] = $cadastros->get($data, 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $valores,
        ]);
    }

    /**
     * Dados para o gráfico de cupons cadastrados semanalmente.
     * Considera todos os cupons agrupados pela data de cadastro (created_at).
     */
    public function chartCuponsSemanais(): JsonResponse
    {
        $inicio = Carbon::now()->subWeeks(12)->startOfWeek(Carbon::MONDAY);

        $cupons = CupomFiscal::where('created_at', '>=', $inicio)
            ->selectRaw("DATE_TRUNC('week', created_at)::date as semana, COUNT(*) as total")
            ->groupByRaw("DATE_TRUNC('week', created_at)::date")
            ->orderByRaw("DATE_TRUNC('week', created_at)::date")
            ->get()
            ->pluck('total', 'semana')
            ->mapWithKeys(fn ($total, $semana) => [(string) $semana => (int) $total]);

        $labels = [];
        $valores = [];

        for ($i = 12; $i >= 0; $i--) {
            // Segunda-feira (Carbon::MONDAY) para alinhar com DATE_TRUNC('week') do PostgreSQL (ISO 8601)
            $semana = Carbon::now()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $chave = $semana->format('Y-m-d');
            $labels[] = $semana->format('d/m');
            $valores[] = $cupons->get($chave, 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $valores,
        ]);
    }

    /**
     * Total de números da sorte distribuídos por série.
     */
    public function chartNumerosDistribuidos(): JsonResponse
    {
        $porSerie = NumeroDaSorte::selectRaw('serie, COUNT(*) as total')
            ->groupBy('serie')
            ->orderBy('serie')
            ->get();

        $labels = [];
        $valores = [];

        for ($s = 0; $s < 10; $s++) {
            $labels[] = "Série {$s}";
            $valores[] = $porSerie->firstWhere('serie', $s)?->total ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $valores,
            'total' => NumeroDaSorte::count(),
        ]);
    }
}
