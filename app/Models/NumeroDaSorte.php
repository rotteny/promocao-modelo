<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumeroDaSorte extends Model
{
    protected $table = 'numeros_da_sorte';

    protected $fillable = [
        'numero',
        'serie',
        'participante_id',
        'cupom_fiscal_id',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'serie' => 'integer',
        ];
    }

    /**
     * Participante dono deste número.
     */
    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }

    /**
     * Cupom fiscal que gerou este número.
     */
    public function cupomFiscal(): BelongsTo
    {
        return $this->belongsTo(CupomFiscal::class, 'cupom_fiscal_id');
    }

    /**
     * Retorna o número formatado (ex: 0 - 0042).
     */
    public function getNumeroFormatadoAttribute(): string
    {
        return $this->serie . ' - ' . str_pad($this->numero, 4, '0', STR_PAD_LEFT);
    }
}
