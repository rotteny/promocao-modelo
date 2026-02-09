<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CupomFiscal extends Model
{
    protected $table = 'cupons_fiscais';

    /**
     * Constantes de status do cupom.
     */
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_VALIDADO = 'validado';       // Validado pela Sefaz, aguardando processamento
    public const STATUS_PROCESSANDO = 'processando'; // Gerando números da sorte
    public const STATUS_CONCLUIDO = 'concluido';     // Números gerados com sucesso
    public const STATUS_ERRO = 'erro';               // Erro no processamento
    public const STATUS_REJEITADO = 'rejeitado';     // Rejeitado pela Sefaz

    protected $fillable = [
        'numero',
        'cnpj_loja',
        'chave_acesso',
        'data_compra',
        'valor_total',
        'status',
        'erro_processamento',
        'participante_id',
    ];

    protected function casts(): array
    {
        return [
            'data_compra' => 'date',
            'valor_total' => 'decimal:2',
        ];
    }

    /**
     * Participante dono do cupom.
     */
    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }

    /**
     * Itens do cupom fiscal.
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemCupom::class, 'cupom_fiscal_id');
    }

    /**
     * Números da sorte gerados a partir deste cupom.
     */
    public function numerosDaSorte(): HasMany
    {
        return $this->hasMany(NumeroDaSorte::class, 'cupom_fiscal_id');
    }

    /**
     * Verifica se o cupom possui algum produto bônus.
     */
    public function temProdutoBonus(): bool
    {
        return $this->itens()
            ->whereHas('produto', fn ($q) => $q->where('bonus', true))
            ->exists();
    }

    /**
     * Retorna o valor total dos itens com produtos bônus.
     */
    public function getValorProdutosBonus(): float
    {
        return (float) $this->itens()
            ->whereHas('produto', fn ($q) => $q->where('bonus', true))
            ->sum('subtotal');
    }

    /**
     * Retorna o label amigável do status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_VALIDADO => 'Na Fila',
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_CONCLUIDO => 'Concluído',
            self::STATUS_ERRO => 'Erro',
            self::STATUS_REJEITADO => 'Rejeitado',
            default => $this->status,
        };
    }

    /**
     * Retorna a classe CSS do badge do status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'bg-secondary',
            self::STATUS_VALIDADO => 'bg-info text-dark',
            self::STATUS_PROCESSANDO => 'bg-warning text-dark',
            self::STATUS_CONCLUIDO => 'bg-success',
            self::STATUS_ERRO => 'bg-danger',
            self::STATUS_REJEITADO => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Retorna o ícone do status.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'bi-clock',
            self::STATUS_VALIDADO => 'bi-hourglass-split',
            self::STATUS_PROCESSANDO => 'bi-arrow-repeat',
            self::STATUS_CONCLUIDO => 'bi-check-circle',
            self::STATUS_ERRO => 'bi-exclamation-triangle',
            self::STATUS_REJEITADO => 'bi-x-circle',
            default => 'bi-question-circle',
        };
    }
}
