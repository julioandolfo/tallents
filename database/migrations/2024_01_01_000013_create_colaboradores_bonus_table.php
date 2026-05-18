<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores_bonus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('tipo_bonus_id')->constrained('tipos_bonus')->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['colaborador_id', 'tipo_bonus_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores_bonus');
    }
};
