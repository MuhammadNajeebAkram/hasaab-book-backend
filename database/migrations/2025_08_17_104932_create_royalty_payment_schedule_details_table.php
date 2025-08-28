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
        Schema::create('royalty_payment_schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('royalty_schedule_id')->constrained('royalty_payment_schedules')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->bigInteger('bank_account_id')->nullable();
            $table->string('cheque_no');
            $table->enum('status', ['pending', 'paid']);
            $table->date('paid_date')->nullable();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('royalty_payment_schedule_details');
    }
};
