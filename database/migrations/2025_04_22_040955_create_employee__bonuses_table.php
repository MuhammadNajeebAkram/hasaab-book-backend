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
        Schema::create('employee_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_of_accounts','id')->onDelete('cascade');
    $table->foreignId('bonus_id')->constrained('bonuses', 'id')->onDelete(('cascade'));
    $table->decimal('amount', 15, 2);   
    $table->string('description')->nullable();
    
    $table->date('bonus_date'); // when it's awarded
    $table->enum('status', ['pending', 'paid'])->default('pending');
    
    
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bonuses');
    }
};
