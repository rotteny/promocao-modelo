<?php

namespace App\Http\Middleware;

use App\Services\PromocaoService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPromocaoAtiva
{
    public function __construct(
        private readonly PromocaoService $promocaoService,
    ) {}

    /**
     * Impede acesso a rotas de participação quando a promoção não está ativa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->promocaoService->isAtiva()) {
            return $next($request);
        }

        // Para requisições AJAX, retorna JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->promocaoService->getMensagemStatus(),
                'status' => $this->promocaoService->getStatus(),
            ], 403);
        }

        return redirect()->route('home')
            ->with('warning', $this->promocaoService->getMensagemStatus());
    }
}
