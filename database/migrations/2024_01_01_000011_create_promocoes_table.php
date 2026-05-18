<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['PROMOCAO', 'REAJUSTE', 'REBAIXAMENTO'])->default('REAJUSTE');
            $table->foreignId('cargo_anterior_id')->nullable()->constrained('cargos')->nullOnDelete();
            $table->foreignId('cargo_novo_id')->nullable()->constrained('cargos')->nullOnDelete();
            $table->decimal('salario_anterior', 10, 2);
            $table->decimal('salario_novo', 10, 2);
            $table->date('data_promocao');
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocoes');
    }
};
