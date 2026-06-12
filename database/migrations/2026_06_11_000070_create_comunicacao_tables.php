<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('conteudo');
            $table->string('categoria')->default('GERAL'); // GERAL, RH, EVENTO, URGENTE
            $table->boolean('destaque')->default(false);
            $table->boolean('publicado')->default(true);
            $table->timestamp('publicado_em')->nullable();
            $table->timestamps();
        });

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('tipo')->default('OUTRO'); // REUNIAO, TREINAMENTO, FOLGA, ANIVERSARIO, OUTRO
            $table->dateTime('inicio');
            $table->dateTime('fim')->nullable();
            $table->string('local')->nullable();
            $table->timestamps();
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('categoria')->default('MANUAL'); // MANUAL, POLITICA, FORMULARIO, OUTRO
            $table->string('arquivo')->nullable(); // caminho no storage
            $table->string('url')->nullable();     // link externo alternativo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('comunicados');
    }
};
