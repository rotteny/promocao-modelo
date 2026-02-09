<?php

namespace App\Jobs\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Support\Facades\Log;

class EnsureFilaDesbloqueada
{
    /**
     * Verifica se a fila de processamento está desbloqueada.
     * Se estiver bloqueada, recoloca o job na fila com delay.
     */
    public function handle(object $job, Closure $next): void
    {
        if (Setting::getValue('fila_bloqueada') === 'true') {
            Log::warning("Fila bloqueada. Cupom #{$job->cupom->id} reagendado para 60s.");

            // Recoloca na fila com delay de 60 segundos
            $job->release(60);

            return;
        }

        $next($job);
    }
}
