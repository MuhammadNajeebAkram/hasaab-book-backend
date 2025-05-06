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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
    $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
    $table->foreignId('account_id')->constrained('chart_of_accounts','id')->onDelete('cascade');  // Salary Account

    $table->year('year');                          // e.g., 2025
    $table->tinyInteger('month');                  // e.g., 4 (for April)

    $table->decimal('basic_salary', 15, 2);        // From employee profile
    $table->decimal('house_rent', 15, 2)->default(0);
    $table->decimal('medical_allowance', 15, 2)->default(0);
    $table->decimal('travel_allowance', 15, 2)->default(0);
    $table->decimal('overtime', 15, 2)->default(0);
    $table->decimal('other_allowance', 15, 2)->default(0);

    $table->decimal('advance_deduction', 15, 2)->default(0);   // From advance_salaries
    $table->decimal('loan_deduction', 15, 2)->default(0);      // From employee_loans
   

    $table->decimal('gross_salary', 15, 2);        // Sum of basic + allowances
    $table->decimal('net_salary', 15, 2);          // Gross - deductions

    $table->enum('status', ['pending', 'paid'])->default('pending');
    $table->date('payment_date')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
