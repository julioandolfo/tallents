<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechamentos_pagamento_bonus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fechamento_item_id')->constrained('fechamentos_pagamento_itens')->cascadeOnDelete();
            $table->foreignId('colaborador_bonus_id')->constrained('colaboradores_bonus')->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechamentos_pagamento_bonus');
    }
};
