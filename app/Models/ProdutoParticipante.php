<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdutoParticipante extends Model
{
    protected $table = 'produtos_participantes';

    protected $fillable = [
        'descricao',
        'bonus',
    ];

    protected function casts(): array
    {
        return [
            'bonus' => 'boolean',
        ];
    }

    /**
     * Itens de cupom que referenciam este produto.
     */
    public function itensCupom(): HasMany
    {
        return $this->hasMany(ItemCupom::class, 'produto_participante_id');
    }
}
