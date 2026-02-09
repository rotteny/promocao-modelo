<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCupom extends Model
{
    protected $table = 'itens_cupom';

    protected $fillable = [
        'cupom_fiscal_id',
        'produto_participante_id',
        'quantidade',
        'valor_unitario',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
            'valor_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Cupom fiscal ao qual este item pertence.
     */
    public function cupomFiscal(): BelongsTo
    {
        return $this->belongsTo(CupomFiscal::class, 'cupom_fiscal_id');
    }

    /**
     * Produto referente a este item.
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(ProdutoParticipante::class, 'produto_participante_id');
    }
}
