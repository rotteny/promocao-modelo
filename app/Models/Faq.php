<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    protected $fillable = [
        'pergunta',
        'resposta',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    /**
     * Scope para retornar apenas FAQs ativos, ordenados.
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true)->orderBy('ordem')->orderBy('id');
    }
}
