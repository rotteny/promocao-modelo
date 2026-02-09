<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Services\PromocaoService;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(
        private readonly PromocaoService $promocaoService,
    ) {}

    /**
     * Landing page da promoção.
     */
    public function home()
    {
        $promocao = $this->promocaoService;

        return view('pages.home', compact('promocao'));
    }

    /**
     * Página de regulamento.
     */
    public function regulamento()
    {
        return view('pages.regulamento');
    }

    /**
     * Página de perguntas frequentes.
     */
    public function faq()
    {
        $faqs = Faq::ativos()->get();

        return view('pages.faq', compact('faqs'));
    }

    /**
     * API pública: retorna o status atual da promoção (para consumo JS).
     */
    public function statusPromocao(): JsonResponse
    {
        return response()->json($this->promocaoService->toArray());
    }
}
