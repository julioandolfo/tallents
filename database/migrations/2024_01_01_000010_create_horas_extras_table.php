<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horas_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->date('data');
            $table->decimal('horas', 5, 2);
            $table->decimal('percentual', 5, 2)->default(50);
            $table->decimal('valor', 10, 2)->default(0);
            $table->string('motivo')->nullable();
            $table->string('observacao')->nullable();
            $table->string('status')->default('pendente');
            $table->boolean('aprovado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horas_extras');
    }
};
