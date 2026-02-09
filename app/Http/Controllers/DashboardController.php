<?php

namespace App\Http\Controllers;

use App\Services\PromocaoService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PromocaoService $promocaoService,
    ) {}

    /**
     * Dashboard do participante - "Meus Números da Sorte".
     */
    public function index()
    {
        $participante = Auth::guard('web')->user();

        $numerosDaSorte = $participante->numerosDaSorte()
            ->with('cupomFiscal')
            ->orderBy('serie')
            ->orderBy('numero')
            ->get();

        $cuponsFiscais = $participante->cuponsFiscais()
            ->withCount('numerosDaSorte')
            ->orderByDesc('created_at')
            ->get();

        $promocao = $this->promocaoService;

        return view('dashboard.index', compact('numerosDaSorte', 'cuponsFiscais', 'promocao'));
    }
}
