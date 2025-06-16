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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Transaction ID from Stripe or PayPal
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->json('gateway_response')->nullable();
            $table->string('payment_method');
            $table->enum('status', ['completed', 'pending', 'failed']); // Transaction status
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
