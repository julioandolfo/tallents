<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quadros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });

        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quadro_id')->constrained('quadros')->cascadeOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('status')->default('A_FAZER'); // A_FAZER, FAZENDO, CONCLUIDO
            $table->string('prioridade')->default('MEDIA'); // BAIXA, MEDIA, ALTA
            $table->date('prazo')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
        Schema::dropIfExists('quadros');
    }
};
