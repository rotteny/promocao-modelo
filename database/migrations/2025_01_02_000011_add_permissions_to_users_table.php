<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona colunas de permissão ao usuário administrativo.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('password');
            $table->boolean('perm_produtos')->default(false)->after('is_super_admin');
            $table->boolean('perm_faq')->default(false)->after('perm_produtos');
            $table->boolean('perm_configuracoes')->default(false)->after('perm_faq');
            $table->boolean('perm_encerrar_campanha')->default(false)->after('perm_configuracoes');
            $table->boolean('ativo')->default(true)->after('perm_encerrar_campanha');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_super_admin',
                'perm_produtos',
                'perm_faq',
                'perm_configuracoes',
                'perm_encerrar_campanha',
                'ativo',
            ]);
        });
    }
};
