<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numeros_da_sorte', function (Blueprint $table) {
            $table->id();
            $table->integer('numero');
            $table->integer('serie');
            $table->foreignId('participante_id')->constrained('participantes')->onDelete('cascade');
            $table->foreignId('cupom_fiscal_id')->constrained('cupons_fiscais')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['numero', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numeros_da_sorte');
    }
};
