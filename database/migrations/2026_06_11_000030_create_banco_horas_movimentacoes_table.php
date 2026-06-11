<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banco_horas_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo'); // CREDITO, DEBITO
            $table->decimal('horas', 6, 2);
            $table->string('motivo')->nullable();
            $table->date('data');
            $table->timestamps();

            $table->index(['colaborador_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banco_horas_movimentacoes');
    }
};
