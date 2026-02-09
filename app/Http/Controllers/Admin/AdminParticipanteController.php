<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ParticipantesExport;
use App\Http\Controllers\Controller;
use App\Models\Participante;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminParticipanteController extends Controller
{
    /**
     * Lista todos os participantes com busca e paginação.
     */
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $query = Participante::query()
            ->withCount(['cuponsFiscais', 'numerosDaSorte']);

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'ilike', "%{$busca}%")
                  ->orWhere('cpf', 'ilike', "%{$busca}%")
                  ->orWhere('email', 'ilike', "%{$busca}%")
                  ->orWhere('cidade', 'ilike', "%{$busca}%");
            });
        }

        $participantes = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $totalParticipantes = Participante::count();

        return view('admin.participantes', compact('participantes', 'busca', 'totalParticipantes'));
    }

    /**
     * Exibe os detalhes de um participante.
     */
    public function show(Participante $participante)
    {
        $participante->loadCount(['cuponsFiscais', 'numerosDaSorte']);
        $participante->load([
            'cuponsFiscais' => fn ($q) => $q->withCount('numerosDaSorte')->orderByDesc('created_at'),
            'numerosDaSorte' => fn ($q) => $q->with('cupomFiscal')->orderBy('serie')->orderBy('numero'),
        ]);

        return view('admin.participante-detalhe', compact('participante'));
    }

    /**
     * Exporta participantes para Excel.
     */
    public function exportar(Request $request)
    {
        $busca = $request->input('busca');
        $nomeArquivo = 'participantes_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ParticipantesExport($busca), $nomeArquivo);
    }
}
