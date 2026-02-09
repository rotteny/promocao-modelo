<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cupons_fiscais', function (Blueprint $table) {
            $table->string('cnpj_loja', 14)->after('numero');

            // Remove unique simples do numero e cria unique composta
            $table->dropUnique(['numero']);
            $table->unique(['numero', 'cnpj_loja'], 'cupons_fiscais_numero_cnpj_loja_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cupons_fiscais', function (Blueprint $table) {
            $table->dropUnique('cupons_fiscais_numero_cnpj_loja_unique');
            $table->unique('numero');
            $table->dropColumn('cnpj_loja');
        });
    }
};
