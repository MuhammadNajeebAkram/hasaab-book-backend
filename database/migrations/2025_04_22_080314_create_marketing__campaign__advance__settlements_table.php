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
        Schema::create('marketing_campaign_advance_settlements', function (Blueprint $table) {
            $table->id();

    // Related campaign
    $table->unsignedBigInteger('campaign_id');
    $table->foreign('campaign_id')->references('id')->on('marketing_campaigns')->onDelete('cascade');

    // Link to journal voucher
    $table->unsignedBigInteger('voucher_id')->nullable();
    $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');

    // Expense type (linked to chart of account)
    $table->unsignedBigInteger('chart_of_account_id');
    $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');

    // Expense amount
    $table->decimal('amount', 15, 2);

    // Optional description or purpose
    $table->text('description')->nullable();

    // Expense date
    $table->date('expense_date');

    // Optional attachment (bills, invoices, etc.)
    $table->string('attachment')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_advance_settlements');
    }
};
