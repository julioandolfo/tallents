<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->nullOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->text('requisitos')->nullable();
            $table->string('modalidade')->default('PRESENCIAL'); // PRESENCIAL, REMOTO, HIBRIDO
            $table->string('tipo_contrato')->nullable();
            $table->decimal('salario_min', 12, 2)->nullable();
            $table->decimal('salario_max', 12, 2)->nullable();
            $table->unsignedInteger('quantidade')->default(1);
            $table->string('status')->default('ABERTA'); // ABERTA, PAUSADA, FECHADA
            $table->date('aberta_em')->nullable();
            $table->timestamps();
        });

        Schema::create('candidatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaga_id')->constrained('vagas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('curriculo')->nullable(); // caminho no storage
            $table->string('etapa')->default('TRIAGEM'); // TRIAGEM, ENTREVISTA, TESTE, PROPOSTA, CONTRATADO, REPROVADO
            $table->unsignedTinyInteger('nota')->nullable(); // 0-10
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatos');
        Schema::dropIfExists('vagas');
    }
};
