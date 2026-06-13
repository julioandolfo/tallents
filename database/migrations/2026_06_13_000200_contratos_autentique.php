<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Método de assinatura: INTERNO (aceite no portal) ou AUTENTIQUE.
            $table->string('metodo_assinatura')->default('INTERNO')->after('status');
            // Dados da integração Autentique.
            $table->string('autentique_document_id')->nullable()->after('metodo_assinatura');
            $table->string('autentique_signature_url')->nullable()->after('autentique_document_id');
            $table->timestamp('enviado_assinatura_em')->nullable()->after('autentique_signature_url');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['metodo_assinatura', 'autentique_document_id', 'autentique_signature_url', 'enviado_assinatura_em']);
        });
    }
};
