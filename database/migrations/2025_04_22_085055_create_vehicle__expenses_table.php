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
        Schema::create('vehicle_expenses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        
            $table->unsignedBigInteger('voucher_id')->nullable(); // Link to journal voucher
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
        
            $table->unsignedBigInteger('chart_of_account_id'); // Type of expense
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
        
            $table->decimal('amount', 15, 2);
            $table->string('expense_type'); // Optional: redundant with COA, but nice for quick filters (fuel, maintenance, etc.)
            $table->date('expense_date');
            $table->string('receipt')->nullable();
            $table->text('notes')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expenses');
    }
};
