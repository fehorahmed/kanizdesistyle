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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // Coupon code, e.g., WELCOME10
            $table->string('description')->nullable(); // Optional description
            $table->integer('discount');        // Discount amount or percentage
            $table->enum('type', ['percent', 'fixed'])->default('fixed'); // Discount type
            $table->double('min_purchase', 20,2)->default(0.00);
            $table->date('start_date')->nullable(); // When the coupon starts
            $table->date('end_date')->nullable();   // Expiration date
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
