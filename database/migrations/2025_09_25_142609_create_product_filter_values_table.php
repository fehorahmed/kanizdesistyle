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
        Schema::create('product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');        // FK to products table
            $table->unsignedBigInteger('filter_id');         // FK to filters table
            $table->unsignedBigInteger('filter_option_id')->nullable(); // FK to filter_options (for select, checkbox, radio)
            $table->string('value_text')->nullable();        // For text type filters
            $table->decimal('value_number', 15, 2)->nullable(); // For numeric filters (e.g., RAM, Price)
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('filter_id')->references('id')->on('filters')->onDelete('cascade');
            $table->foreign('filter_option_id')->references('id')->on('filter_options')->onDelete('cascade');

            // Indexes for faster querying
            $table->index(['product_id', 'filter_id']);
            $table->index('filter_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_filter_values');
    }
};
