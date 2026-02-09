<?php

namespace App\Services;

use App\Models\NumeroDaSorte;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\CampanhaEncerrada;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PromocaoService
{
    /**
     * Status possíveis da campanha.
     */
    public const STATUS_AGUARDANDO = 'aguardando';
    public const STATUS_ATIVA = 'ativa';
    public const STATUS_ENCERRADA_PRAZO = 'encerrada_prazo';
    public const STATUS_ENCERRADA_ESGOTAMENTO = 'encerrada_esgotamento';
    public const STATUS_ENCERRADA_MANUAL = 'encerrada_manual';

    /**
     * Capacidade total de números da sorte.
     */
    public const CAPACIDADE_TOTAL = LuckyNumberService::TOTAL_SERIES * LuckyNumberService::NUMEROS_POR_SERIE;

    /**
     * Retorna a data/hora de início da promoção.
     */
    public function getDataInicio(): Carbon
    {
        $valor = Setting::getValue('data_inicio', '2025-01-01 00:00:00');

        return Carbon::parse($valor);
    }

    /**
     * Retorna a data/hora de fim da promoção.
     */
    public function getDataFim(): Carbon
    {
        $valor = Setting::getValue('data_fim', '2025-12-31 23:59:59');

        return Carbon::parse($valor);
    }

    /**
     * Verifica se a promoção está ativa (aceitando participações).
     */
    public function isAtiva(): bool
    {
        return $this->getStatus() === self::STATUS_ATIVA;
    }

    /**
     * Verifica se a promoção está encerrada (qualquer motivo).
     */
    public function isEncerrada(): bool
    {
        return in_array($this->getStatus(), [
            self::STATUS_ENCERRADA_PRAZO,
            self::STATUS_ENCERRADA_ESGOTAMENTO,
            self::STATUS_ENCERRADA_MANUAL,
        ]);
    }

    /**
     * Verifica se a promoção ainda não começou.
     */
    public function isAguardando(): bool
    {
        return $this->getStatus() === self::STATUS_AGUARDANDO;
    }

    /**
     * Retorna o status atual da campanha.
     */
    public function getStatus(): string
    {
        // Verifica se foi encerrada manualmente ou por esgotamento
        $encerrada = Setting::getValue('campanha_encerrada', 'false');
        if ($encerrada === 'true') {
            $motivo = Setting::getValue('campanha_motivo_encerramento', 'manual');

            return match ($motivo) {
                'esgotamento' => self::STATUS_ENCERRADA_ESGOTAMENTO,
                'manual' => self::STATUS_ENCERRADA_MANUAL,
                default => self::STATUS_ENCERRADA_MANUAL,
            };
        }

        $agora = Carbon::now();
        $inicio = $this->getDataInicio();
        $fim = $this->getDataFim();

        // Antes do início
        if ($agora->lt($inicio)) {
            return self::STATUS_AGUARDANDO;
        }

        // Após o fim
        if ($agora->gt($fim)) {
            return self::STATUS_ENCERRADA_PRAZO;
        }

        // Verifica esgotamento em tempo real
        if ($this->isNumerosEsgotados()) {
            return self::STATUS_ENCERRADA_ESGOTAMENTO;
        }

        return self::STATUS_ATIVA;
    }

    /**
     * Retorna mensagem amigável do status.
     */
    public function getMensagemStatus(): string
    {
        return match ($this->getStatus()) {
            self::STATUS_AGUARDANDO => 'A promoção ainda não começou. Início previsto: ' . $this->getDataInicio()->format('d/m/Y \à\s H:i') . '.',
            self::STATUS_ATIVA => 'A promoção está ativa! Cadastre seus cupons e concorra a prêmios.',
            self::STATUS_ENCERRADA_PRAZO => 'O período da promoção foi encerrado. Aguarde a data do sorteio!',
            self::STATUS_ENCERRADA_ESGOTAMENTO => 'Todos os números da sorte foram distribuídos! A campanha foi encerrada. Aguarde a data do sorteio!',
            self::STATUS_ENCERRADA_MANUAL => 'A promoção foi encerrada. Aguarde a data do sorteio!',
        };
    }

    /**
     * Verifica se todos os números da sorte foram esgotados.
     */
    public function isNumerosEsgotados(): bool
    {
        return NumeroDaSorte::count() >= self::CAPACIDADE_TOTAL;
    }

    /**
     * Retorna quantos números ainda estão disponíveis.
     */
    public function getNumerosDisponiveis(): int
    {
        return max(0, self::CAPACIDADE_TOTAL - NumeroDaSorte::count());
    }

    /**
     * Verifica e registra o esgotamento de números (chamado após processamento).
     * Retorna true se esgotou.
     */
    public function verificarEsgotamento(): bool
    {
        if (! $this->isNumerosEsgotados()) {
            return false;
        }

        // Já estava marcado?
        if (Setting::getValue('campanha_encerrada') === 'true') {
            return true;
        }

        // Marca a campanha como encerrada por esgotamento
        Setting::setValue('campanha_encerrada', 'true', 'Campanha encerrada automaticamente');
        Setting::setValue('campanha_motivo_encerramento', 'esgotamento', 'Motivo do encerramento da campanha');

        Log::critical('CAMPANHA ENCERRADA - Todos os números da sorte foram distribuídos.');

        // Notifica admins
        $admins = User::all();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new CampanhaEncerrada('esgotamento'));
        }

        return true;
    }

    /**
     * Retorna dados do status para API pública (consumo via JavaScript).
     * Não expõe datas de término, quantidade de números ou capacidade.
     */
    public function toArray(): array
    {
        $status = $this->getStatus();

        return [
            'status' => $status,
            'ativa' => $status === self::STATUS_ATIVA,
            'mensagem' => $this->getMensagemStatus(),
            'servidor_agora' => Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * Retorna dados completos do status para o painel administrativo.
     */
    public function toAdminArray(): array
    {
        $status = $this->getStatus();

        return [
            'status' => $status,
            'ativa' => $status === self::STATUS_ATIVA,
            'mensagem' => $this->getMensagemStatus(),
            'data_inicio' => $this->getDataInicio()->toIso8601String(),
            'data_fim' => $this->getDataFim()->toIso8601String(),
            'numeros_distribuidos' => NumeroDaSorte::count(),
            'numeros_disponiveis' => $this->getNumerosDisponiveis(),
            'capacidade_total' => self::CAPACIDADE_TOTAL,
            'servidor_agora' => Carbon::now()->toIso8601String(),
        ];
    }
}
