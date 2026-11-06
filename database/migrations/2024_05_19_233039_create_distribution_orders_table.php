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
        Schema::create('distribution_orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1 = Distribution Sales,2 = Distribution Damage Product return');
            $table->string('order_no')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('dsr_id');
            $table->float('total',100,2);
            $table->float('paid',100,2);
            $table->float('due',100,2);
            $table->text('notes')->nullable();
            $table->date('date');
            $table->boolean('close_status')->default(0);
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
        Schema::dropIfExists('distribution_orders');
    }
};
