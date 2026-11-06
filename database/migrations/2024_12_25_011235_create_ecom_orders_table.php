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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key to users table
            $table->string('order_number')->unique(); 
            $table->string('pi_first_name')->nullable(); 
            $table->string('pi_last_name')->nullable(); 
            $table->string('pi_email')->nullable(); 
            $table->string('pi_phone_no')->nullable(); 
            $table->string('country')->nullable(); 
            $table->string('shipping_city'); 
            $table->text('order_notes')->nullable(); 
            $table->decimal('total_amount', 10, 2); // Total order amount
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending'); // Order status
            $table->text('shipping_address')->nullable(); // Shipping address
            $table->text('billing_address')->nullable(); // Billing address (optional)
            $table->timestamp('ordered_at')->nullable(); // Date of order
            $table->string('payment_method')->nullable();
            $table->string('payable_amount')->nullable();
            $table->softDeletes(); // Soft delete column

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecom_orders');
    }
};
