<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->decimal('current_amount', 10, 2);
            $table->string('currency', 10)->default('ARS');
            $table->timestamps();

            $table->unique('neighborhood_id');
        });

        Schema::create('fee_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->decimal('previous_amount', 10, 2);
            $table->decimal('new_amount', 10, 2);
            $table->date('effective_from'); // Primer día del mes desde el que aplica
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['neighborhood_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_adjustments');
        Schema::dropIfExists('fee_configs');
    }
};

