<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechamentos_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('mes');
            $table->integer('ano');
            $table->enum('status', ['ABERTO', 'FECHADO'])->default('ABERTO');
            $table->decimal('total_salarios', 12, 2)->default(0);
            $table->decimal('total_horas_extras', 12, 2)->default(0);
            $table->decimal('total_bonus', 12, 2)->default(0);
            $table->decimal('total_geral', 12, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechamentos_pagamento');
    }
};
