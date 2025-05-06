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
        Schema::create('employee_loan_entries', function (Blueprint $table) {
            $table->id();
    $table->foreignId('employee_loan_id')->constrained('employee_loans')->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
    $table->enum('payment_type', ['issued', 'recovered']);    
    $table->decimal('amount', 15, 2);    
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loan_entries');
    }
};
