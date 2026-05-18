<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechamentos_pagamento_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fechamento_id')->constrained('fechamentos_pagamento')->cascadeOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->decimal('salario_base', 10, 2);
            $table->decimal('total_horas_extras', 10, 2)->default(0);
            $table->decimal('total_bonus', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechamentos_pagamento_itens');
    }
};
