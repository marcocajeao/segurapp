<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();

            $table->date('period'); // Primer día del mes, ej: 2025-01-01
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE'])->default('PENDING');

            $table->timestamps();

            $table->unique(['property_id', 'period']);
            $table->index(['neighborhood_id', 'period']);
            $table->index(['neighborhood_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_fees');
    }
};

