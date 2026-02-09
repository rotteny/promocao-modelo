<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupons_fiscais', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('chave_acesso', 44)->nullable();
            $table->date('data_compra');
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->enum('status', ['pendente', 'validado', 'processando', 'concluido', 'erro', 'rejeitado'])->default('pendente');
            $table->text('erro_processamento')->nullable();
            $table->foreignId('participante_id')->constrained('participantes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupons_fiscais');
    }
};
