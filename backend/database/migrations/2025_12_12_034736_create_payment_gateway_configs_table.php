<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();

            $table->string('mp_public_key');
            $table->string('mp_access_token');
            $table->string('mp_webhook_secret')->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique('neighborhood_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};

