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
        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_group_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->tinyInteger('payment_mode')->comment('0=none,1=bank,2=cash,3=Mobile banking')
                            ->default(0);
            $table->string('name');
            $table->float('bank_commission_percent')->default(0);
            $table->bigInteger('code');
            $table->float('opening_balance',100)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_heads');
    }
};
