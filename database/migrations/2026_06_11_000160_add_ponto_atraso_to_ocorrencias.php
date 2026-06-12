<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_ocorrencias', function (Blueprint $table) {
            $table->string('categoria')->nullable()->after('tipo'); // ATRASO, FALTA, PONTO, OUTROS
            $table->boolean('permite_tempo_atraso')->default(false)->after('categoria');
            $table->boolean('permite_tipo_ponto')->default(false)->after('permite_tempo_atraso');
        });

        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->time('hora_ocorrencia')->nullable()->after('data_ocorrencia');
            $table->unsignedInteger('tempo_atraso_minutos')->nullable()->after('hora_ocorrencia');
            $table->string('tipo_ponto')->nullable()->after('tempo_atraso_minutos'); // ENTRADA, SAIDA, INTERVALO
        });
    }

    public function down(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropColumn(['hora_ocorrencia', 'tempo_atraso_minutos', 'tipo_ponto']);
        });

        Schema::table('tipos_ocorrencias', function (Blueprint $table) {
            $table->dropColumn(['categoria', 'permite_tempo_atraso', 'permite_tipo_ponto']);
        });
    }
};
