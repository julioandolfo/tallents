<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('avaliador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ciclo'); // ex: 2024-S1
            $table->string('tipo')->default('GESTOR'); // AUTO, GESTOR, 360
            $table->decimal('nota_geral', 4, 2)->nullable(); // 0-10
            $table->text('pontos_fortes')->nullable();
            $table->text('pontos_melhorar')->nullable();
            $table->string('status')->default('PENDENTE'); // PENDENTE, CONCLUIDA
            $table->date('data')->nullable();
            $table->timestamps();
        });

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->default('GERAL'); // ELOGIO, CONSTRUTIVO, GERAL
            $table->text('mensagem');
            $table->boolean('visivel_colaborador')->default(true);
            $table->date('data')->nullable();
            $table->timestamps();
        });

        Schema::create('pdis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('status')->default('EM_ANDAMENTO'); // EM_ANDAMENTO, CONCLUIDO, CANCELADO
            $table->date('prazo')->nullable();
            $table->timestamps();
        });

        Schema::create('pdi_acoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdi_id')->constrained('pdis')->cascadeOnDelete();
            $table->string('descricao');
            $table->boolean('concluida')->default(false);
            $table->date('prazo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdi_acoes');
        Schema::dropIfExists('pdis');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('avaliacoes');
    }
};
