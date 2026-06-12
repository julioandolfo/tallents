<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->string('nome');
            $table->string('parentesco'); // FILHO, CONJUGE, PAI, MAE, OUTRO
            $table->date('data_nascimento')->nullable();
            $table->string('cpf')->nullable();
            $table->boolean('dependente_ir')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('formacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->string('nivel'); // FUNDAMENTAL, MEDIO, TECNICO, GRADUACAO, POS, MESTRADO, DOUTORADO, CURSO
            $table->string('curso');
            $table->string('instituicao')->nullable();
            $table->string('situacao')->default('CONCLUIDO'); // CURSANDO, CONCLUIDO, TRANCADO
            $table->year('ano_conclusao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formacoes');
        Schema::dropIfExists('dependentes');
    }
};
