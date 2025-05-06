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
        Schema::create('advance_salary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_id')->constrained('advance_salaries')->onDelete('cascade');
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
        Schema::dropIfExists('advance_salary_entries');
    }
};
