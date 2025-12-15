<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();

            // Mes al que corresponde el pago (YYYY-MM-01)
            $table->date('period')->index();

            $table->enum('method', ['MERCADO_PAGO', 'CASH', 'BANK_TRANSFER']);
            $table->enum('status', ['PENDING', 'PENDING_REVIEW', 'APPROVED', 'REJECTED', 'REFUNDED'])->default('PENDING');

            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at')->nullable();

            $table->string('mp_payment_id')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('reference')->nullable(); // nro de operación bancaria, etc.

            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Evita pagos duplicados para el mismo mes y la misma propiedad
            $table->unique(['property_id', 'period'], 'payments_property_period_unique');

            $table->index(['neighborhood_id', 'status']);
            $table->index(['method', 'status']);
            $table->index(['property_id', 'status', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
