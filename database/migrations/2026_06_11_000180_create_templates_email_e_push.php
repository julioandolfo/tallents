<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates_email', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();          // ex.: boas_vindas, promocao
            $table->string('nome');                       // rótulo amigável
            $table->string('assunto');
            $table->text('corpo');                        // HTML com {{ variaveis }}
            $table->text('variaveis')->nullable();        // lista de variáveis disponíveis (doc)
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('configuracoes_push', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('onesignal');
            $table->string('onesignal_app_id')->nullable();
            $table->string('onesignal_api_key')->nullable();
            $table->boolean('ativo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates_email');
        Schema::dropIfExists('configuracoes_push');
    }
};
