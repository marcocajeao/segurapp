<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->nullable()
                ->constrained('beneficiaries')
                ->nullOnDelete();

            $table->string('code')->nullable(); // Identificador interno opcional
            $table->string('street');
            $table->string('number', 20);
            $table->string('extra_address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_beneficiary')->default(true);
            $table->string('qr_token')->unique();
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['neighborhood_id', 'street', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

