<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model de usuário administrativo.
 *
 * @property bool $is_super_admin
 * @property bool $perm_produtos
 * @property bool $perm_faq
 * @property bool $perm_configuracoes
 * @property bool $perm_encerrar_campanha
 * @property bool $ativo
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'perm_produtos',
        'perm_faq',
        'perm_configuracoes',
        'perm_encerrar_campanha',
        'ativo',
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
            'is_super_admin' => 'boolean',
            'perm_produtos' => 'boolean',
            'perm_faq' => 'boolean',
            'perm_configuracoes' => 'boolean',
            'perm_encerrar_campanha' => 'boolean',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Verifica se o admin tem uma permissão específica.
     * Super admins têm todas as permissões.
     */
    public function temPermissao(string $permissao): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return (bool) $this->{$permissao};
    }

    /**
     * Lista de todas as permissões disponíveis com labels.
     */
    public static function permissoesDisponiveis(): array
    {
        return [
            'perm_produtos' => [
                'label' => 'Gerenciar Produtos',
                'descricao' => 'Cadastrar, editar e excluir produtos participantes e bônus',
                'icone' => 'bi-box-seam',
            ],
            'perm_faq' => [
                'label' => 'Gerenciar FAQ',
                'descricao' => 'Cadastrar, editar e excluir perguntas frequentes',
                'icone' => 'bi-question-circle',
            ],
            'perm_configuracoes' => [
                'label' => 'Modificar Configurações',
                'descricao' => 'Alterar parâmetros da promoção, datas, valores e regras',
                'icone' => 'bi-gear',
            ],
            'perm_encerrar_campanha' => [
                'label' => 'Encerrar/Reabrir Campanha',
                'descricao' => 'Encerrar ou reabrir a campanha promocional manualmente',
                'icone' => 'bi-shield-lock',
            ],
        ];
    }
}
