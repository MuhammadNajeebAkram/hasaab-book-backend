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
    Schema::create('voucher_entries', function (Blueprint $table) {
        $table->id();
        
        $table->unsignedBigInteger('voucher_id'); // Link to vouchers table
        $table->unsignedBigInteger('account_id'); // Link to chart_of_accounts
        
        $table->decimal('amount', 15, 2); // Amount (always positive)
        
        $table->enum('type', ['debit', 'credit']); // Debit or Credit
        
        $table->text('description')->nullable(); // (Optional) notes per entry
        
        $table->timestamps();
        
        // Foreign Keys
        $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
        $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_entries');
    }
};
