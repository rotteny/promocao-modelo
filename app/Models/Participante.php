<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model de participante da promoção.
 */
class Participante extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'participantes';

    protected $fillable = [
        'name',
        'cpf',
        'email',
        'telefone',
        'celular',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cupons fiscais do participante.
     */
    public function cuponsFiscais(): HasMany
    {
        return $this->hasMany(CupomFiscal::class, 'participante_id');
    }

    /**
     * Números da sorte do participante.
     */
    public function numerosDaSorte(): HasMany
    {
        return $this->hasMany(NumeroDaSorte::class, 'participante_id');
    }
}
