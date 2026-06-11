<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('ocorrencia_id')->nullable()->constrained('ocorrencias')->nullOnDelete();
            $table->foreignId('aplicada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->default('ESCRITA'); // VERBAL, ESCRITA, SUSPENSAO
            $table->text('motivo');
            $table->date('data');
            $table->unsignedSmallInteger('dias_suspensao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertencias');
    }
};
