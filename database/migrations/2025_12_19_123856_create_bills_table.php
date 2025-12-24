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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->uuid('code')->unique();
            $table->integer('queue_number')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount_paid');
            $table->integer('change');
            $table->enum('payment_method', ['cash', 'transfer', 'qris'])->default('cash');
            $table->enum('service_type', ['dine_in', 'take_away'])->default('dine_in');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
