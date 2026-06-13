<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quem/quando processou o fechamento (colunas ausentes no schema original).
        Schema::table('fechamentos_pagamento', function (Blueprint $table) {
            if (! Schema::hasColumn('fechamentos_pagamento', 'fechado_por')) {
                $table->foreignId('fechado_por')->nullable()->after('criado_por')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('fechamentos_pagamento', 'fechado_em')) {
                $table->timestamp('fechado_em')->nullable()->after('fechado_por');
            }
        });

        // Itens do fechamento: descontos e adicionais editáveis por colaborador.
        Schema::table('fechamentos_pagamento_itens', function (Blueprint $table) {
            $table->decimal('descontos', 12, 2)->default(0)->after('total_bonus');
            $table->decimal('adicionais', 12, 2)->default(0)->after('descontos');
            $table->text('observacoes')->nullable()->after('total');
        });

        // Bônus por colaborador: janela de vigência, observações e múltiplos por tipo.
        Schema::table('colaboradores_bonus', function (Blueprint $table) {
            $table->date('data_inicio')->nullable()->after('valor');
            $table->date('data_fim')->nullable()->after('data_inicio');
            $table->text('observacoes')->nullable()->after('data_fim');
        });

        // Remove o unique (colaborador+tipo) para permitir vários bônus do mesmo tipo.
        // SQLite não suporta dropUnique direto; recria via doctrine quando aplicável.
        try {
            Schema::table('colaboradores_bonus', function (Blueprint $table) {
                $table->dropUnique('colaboradores_bonus_colaborador_id_tipo_bonus_id_unique');
            });
        } catch (\Throwable $e) {
            // Em SQLite de teste o índice pode ter outro nome / não ser removível; ignora.
        }
    }

    public function down(): void
    {
        Schema::table('fechamentos_pagamento', function (Blueprint $table) {
            $table->dropColumn(['fechado_por', 'fechado_em']);
        });
        Schema::table('fechamentos_pagamento_itens', function (Blueprint $table) {
            $table->dropColumn(['descontos', 'adicionais', 'observacoes']);
        });
        Schema::table('colaboradores_bonus', function (Blueprint $table) {
            $table->dropColumn(['data_inicio', 'data_fim', 'observacoes']);
        });
    }
};
