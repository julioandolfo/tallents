<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('setor_id')->nullable()->constrained('setores')->nullOnDelete();
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->nullOnDelete();
            $table->foreignId('nivel_hierarquico_id')->nullable()->constrained('niveis_hierarquicos')->nullOnDelete();
            $table->unsignedBigInteger('lider_id')->nullable();
            $table->foreign('lider_id')->references('id')->on('colaboradores')->nullOnDelete();
            $table->string('nome');
            $table->string('cpf')->nullable();
            $table->string('rg')->nullable();
            $table->string('pis')->nullable();
            $table->string('ctps')->nullable();
            $table->string('matricula')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('sexo')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('email')->nullable();
            $table->string('email_pessoal')->nullable();
            $table->string('email_login')->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('tipo_contrato')->nullable()->default('CLT');
            $table->string('carga_horaria')->nullable();
            $table->date('data_admissao')->nullable();
            $table->date('data_demissao')->nullable();
            $table->decimal('salario', 10, 2)->default(0);
            $table->string('status')->default('ATIVO');
            $table->string('foto')->nullable();
            $table->string('senha_hash')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};
