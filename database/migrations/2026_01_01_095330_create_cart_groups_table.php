<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_groups', function (Blueprint $table) {
            $table->id();
                $table->integer('queue_number'); // Ganti customer_name dengan ini
                $table->enum('service_type', ['dine_in', 'take_away']);
                $table->integer('total_price');
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_groups');
    }
};
