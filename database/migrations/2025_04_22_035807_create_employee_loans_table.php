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
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
    $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
    $table->foreignId('account_id')->constrained('chart_of_accounts','id')->onDelete('cascade');
    $table->decimal('amount', 15, 2);
    $table->integer('installments'); // total number of monthly installments
    $table->decimal('installment_amount', 15, 2);    
    $table->date('issue_date');
    $table->integer('repayment_start_year')->nullable();
    $table->integer('repayment_start_month')->nullable();
    $table->text('reason')->nullable();
    $table->enum('status', ['active', 'settled', 'defaulted'])->default('active');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
