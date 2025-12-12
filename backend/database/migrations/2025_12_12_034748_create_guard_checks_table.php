<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guard_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();

            $table->foreignId('property_id')->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->foreignId('guard_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('result', ['PAID', 'UNPAID', 'NON_BENEFICIARY']);
            $table->string('comment')->nullable();

            $table->timestamps();

            $table->index(['neighborhood_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_checks');
    }
};
