<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NumerosDaSorteExport;
use App\Http\Controllers\Controller;
use App\Models\NumeroDaSorte;
use App\Services\LuckyNumberService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminNumeroDaSorteController extends Controller
{
    /**
     * Lista todos os números da sorte distribuídos com busca, filtro por série e paginação.
     */
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $serie = $request->input('serie');

        $query = NumeroDaSorte::query()
            ->with(['participante', 'cupomFiscal']);

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->whereHas('participante', function ($q2) use ($busca) {
                    $q2->where('name', 'ilike', "%{$busca}%")
                       ->orWhere('cpf', 'ilike', "%{$busca}%");
                });

                // Busca por número (ex: "0042" ou "42")
                if (is_numeric($busca)) {
                    $q->orWhere('numero', (int) $busca);
                }
            });
        }

        if ($serie !== null && $serie !== '') {
            $query->where('serie', (int) $serie);
        }

        $numeros = $query->orderBy('serie')->orderBy('numero')->paginate(30)->withQueryString();

        $totalNumeros = NumeroDaSorte::count();

        // Contagem por série para o resumo
        $serieCounts = [];
        for ($s = 0; $s < LuckyNumberService::TOTAL_SERIES; $s++) {
            $serieCounts[$s] = NumeroDaSorte::where('serie', $s)->count();
        }

        $capacidadeTotal = LuckyNumberService::TOTAL_SERIES * LuckyNumberService::NUMEROS_POR_SERIE;

        return view('admin.numeros-da-sorte', compact(
            'numeros', 'busca', 'serie', 'totalNumeros', 'serieCounts', 'capacidadeTotal'
        ));
    }

    /**
     * Exporta números da sorte para Excel.
     */
    public function exportar(Request $request)
    {
        $busca = $request->input('busca');
        $serie = $request->filled('serie') ? (int) $request->input('serie') : null;
        $nomeArquivo = 'numeros_da_sorte_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new NumerosDaSorteExport($busca, $serie), $nomeArquivo);
    }
}
