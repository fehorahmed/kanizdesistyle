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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('1=purchase,2=sale');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('distribution_order_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_code');
            $table->float('quantity');
            $table->float('unit_price',50)->default(0);
            $table->text('notes')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
