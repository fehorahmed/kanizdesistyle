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
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_order_id');
            $table->unsignedBigInteger('sale_order_id');
            $table->unsignedBigInteger('product_id');
            $table->bigInteger('product_code');
            $table->float('sale_quantity')->default(0);
            $table->float('purchase_unit_price',50,2);
            $table->float('selling_unit_price',50,2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
