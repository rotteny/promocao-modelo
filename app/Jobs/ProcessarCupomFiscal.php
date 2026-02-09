<?php

namespace App\Jobs;

use App\Jobs\Middleware\EnsureFilaDesbloqueada;
use App\Models\CupomFiscal;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ErroProcessamentoCupom;
use App\Services\LuckyNumberService;
use App\Services\PromocaoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessarCupomFiscal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Não tentar novamente automaticamente em caso de falha.
     */
    public int $tries = 1;

    /**
     * Timeout em segundos.
     */
    public int $timeout = 120;

    public function __construct(
        public CupomFiscal $cupom,
    ) {
        // Processa na fila dedicada, em ordem FIFO
        $this->onQueue('numeros-da-sorte');
    }

    /**
     * Middleware do job: verifica se a fila está desbloqueada.
     */
    public function middleware(): array
    {
        return [new EnsureFilaDesbloqueada()];
    }

    /**
     * Processa o cupom: gera os números da sorte.
     */
    public function handle(LuckyNumberService $luckyNumberService, PromocaoService $promocaoService): void
    {
        $cupom = $this->cupom;

        Log::info("Processando cupom #{$cupom->id} - Número: {$cupom->numero}");

        // Marca como "processando"
        $cupom->update(['status' => CupomFiscal::STATUS_PROCESSANDO]);

        // Gera os números da sorte
        $numeros = $luckyNumberService->gerarNumeros($cupom);

        // Marca como concluído
        $cupom->update([
            'status' => CupomFiscal::STATUS_CONCLUIDO,
            'erro_processamento' => null,
        ]);

        Log::info("Cupom #{$cupom->id} processado com sucesso. {$numeros->count()} número(s) gerado(s).");

        // Verifica se os números se esgotaram após este processamento
        $promocaoService->verificarEsgotamento();
    }

    /**
     * Quando o job falha: bloqueia a fila e notifica admins.
     */
    public function failed(\Throwable $exception): void
    {
        $cupom = $this->cupom;

        Log::error("Erro ao processar cupom #{$cupom->id}: {$exception->getMessage()}");

        // Marca o cupom com erro
        $cupom->update([
            'status' => CupomFiscal::STATUS_ERRO,
            'erro_processamento' => $exception->getMessage(),
        ]);

        // Bloqueia a fila para impedir processamento dos próximos
        Setting::setValue(
            'fila_bloqueada',
            'true',
            'Fila de processamento bloqueada por erro'
        );

        Setting::setValue(
            'fila_cupom_erro_id',
            (string) $cupom->id,
            'ID do cupom que causou o bloqueio da fila'
        );

        // Notifica todos os administradores
        $admins = User::all();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ErroProcessamentoCupom($cupom, $exception));
        }

        Log::critical("FILA BLOQUEADA - Cupom #{$cupom->id} falhou. Processamento de novos cupons está parado.");
    }
}
