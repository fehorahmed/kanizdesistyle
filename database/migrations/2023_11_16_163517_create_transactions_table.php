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
            $table->date('date');
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->unsignedBigInteger('payment_account_head_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->unsignedBigInteger('account_head_id');
            $table->bigInteger('voucher_no_group_sl');
            $table->string('voucher_no');
            $table->tinyInteger('voucher_type')->comment('1=Journal Voucher,2=Payment Voucher,3=Collection Voucher,4=Contra Voucher');
            $table->tinyInteger('transaction_type')->comment('1=debit,2=credit');
            $table->float('amount',100);
            $table->unsignedBigInteger('account_head_payee_depositor_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('distribution_order_id')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
