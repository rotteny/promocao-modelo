<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CuponsFiscaisExport;
use App\Http\Controllers\Controller;
use App\Models\CupomFiscal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminCupomController extends Controller
{
    /**
     * Lista todos os cupons fiscais com busca, filtro por status e paginação.
     */
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $status = $request->input('status');

        $query = CupomFiscal::query()
            ->with('participante')
            ->withCount('numerosDaSorte');

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('numero', 'ilike', "%{$busca}%")
                  ->orWhereHas('participante', function ($q2) use ($busca) {
                      $q2->where('name', 'ilike', "%{$busca}%")
                         ->orWhere('cpf', 'ilike', "%{$busca}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $cupons = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $totalCupons = CupomFiscal::count();

        // Contagem por status para o resumo
        $statusCounts = [
            'pendente' => CupomFiscal::where('status', CupomFiscal::STATUS_PENDENTE)->count(),
            'validado' => CupomFiscal::where('status', CupomFiscal::STATUS_VALIDADO)->count(),
            'processando' => CupomFiscal::where('status', CupomFiscal::STATUS_PROCESSANDO)->count(),
            'concluido' => CupomFiscal::where('status', CupomFiscal::STATUS_CONCLUIDO)->count(),
            'erro' => CupomFiscal::where('status', CupomFiscal::STATUS_ERRO)->count(),
            'rejeitado' => CupomFiscal::where('status', CupomFiscal::STATUS_REJEITADO)->count(),
        ];

        return view('admin.cupons', compact('cupons', 'busca', 'status', 'totalCupons', 'statusCounts'));
    }

    /**
     * Exibe os detalhes de um cupom fiscal.
     */
    public function show(CupomFiscal $cupom)
    {
        $cupom->load(['participante', 'itens.produto', 'numerosDaSorte']);

        return view('admin.cupom-detalhe', compact('cupom'));
    }

    /**
     * Exporta cupons fiscais para Excel.
     */
    public function exportar(Request $request)
    {
        $busca = $request->input('busca');
        $status = $request->input('status');
        $nomeArquivo = 'cupons_fiscais_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new CuponsFiscaisExport($busca, $status), $nomeArquivo);
    }
}
