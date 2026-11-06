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
        Schema::create('filter_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('filter_id');        // FK to filters table
            $table->string('value'); 
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('filter_id')->references('id')->on('filters')->onDelete('cascade');
            // Index for faster querying by filter
            $table->index('filter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_options');
    }
};
