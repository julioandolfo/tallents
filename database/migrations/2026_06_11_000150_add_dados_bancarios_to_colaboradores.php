<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colaboradores', function (Blueprint $table) {
            $table->string('banco')->nullable()->after('observacoes');
            $table->string('agencia')->nullable()->after('banco');
            $table->string('conta')->nullable()->after('agencia');
            $table->string('tipo_conta')->nullable()->after('conta'); // CORRENTE, POUPANCA
            $table->string('pix')->nullable()->after('tipo_conta');
            $table->string('cnpj')->nullable()->after('pix'); // p/ prestadores PJ
        });
    }

    public function down(): void
    {
        Schema::table('colaboradores', function (Blueprint $table) {
            $table->dropColumn(['banco', 'agencia', 'conta', 'tipo_conta', 'pix', 'cnpj']);
        });
    }
};
