<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos la columna solo si no existe
            if (!Schema::hasColumn('users', 'neighborhood_id')) {
                $table->foreignId('neighborhood_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'neighborhood_id')) {
                $table->dropForeign(['neighborhood_id']);
                $table->dropColumn('neighborhood_id');
            }
        });
    }
};

