<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->string('tipo')->default('CONTRATO'); // CONTRATO, ESTAGIO, ADITIVO, TERMO
            $table->text('conteudo')->nullable();
            $table->string('arquivo')->nullable();
            $table->string('status')->default('PENDENTE'); // PENDENTE, ASSINADO, CANCELADO
            $table->timestamp('assinado_em')->nullable();
            $table->string('assinatura_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
