<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'colaborador_id')) {
                $table->foreignId('colaborador_id')
                    ->nullable()
                    ->after('setor_id')
                    ->constrained('colaboradores')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'colaborador_id')) {
                $table->dropConstrainedForeignId('colaborador_id');
            }
        });
    }
};
