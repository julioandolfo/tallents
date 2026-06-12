<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('origem')->default('MANUAL'); // MANUAL, ELOGIO, AVALIACAO, META, RESGATE, AJUSTE
            $table->integer('pontos'); // positivo (crédito) ou negativo (resgate/ajuste)
            $table->string('descricao')->nullable();
            $table->timestamps();
        });

        Schema::create('recompensas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->unsignedInteger('custo_pontos');
            $table->integer('estoque')->nullable(); // null = ilimitado
            $table->string('imagem')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });

        Schema::create('resgates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('recompensa_id')->nullable()->constrained('recompensas')->nullOnDelete();
            $table->unsignedInteger('custo_pontos');
            $table->string('status')->default('PENDENTE'); // PENDENTE, APROVADO, ENTREGUE, CANCELADO
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resgates');
        Schema::dropIfExists('recompensas');
        Schema::dropIfExists('pontos_movimentacoes');
    }
};
