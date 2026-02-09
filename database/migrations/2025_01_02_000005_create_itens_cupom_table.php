<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_cupom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupom_fiscal_id')->constrained('cupons_fiscais')->onDelete('cascade');
            $table->foreignId('produto_participante_id')->constrained('produtos_participantes')->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_cupom');
    }
};
