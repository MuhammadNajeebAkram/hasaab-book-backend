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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
    $table->string('code')->unique();
    $table->string('account_name');
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->boolean('has_child')->default(false);
    $table->decimal('opening_balance', 15, 2)->default(0);
    $table->enum('type', ['asset', 'liability', 'equity', 'expense', 'income'])->nullable();
    $table->enum('report_type', ['income_statement', 'balance_sheet', 'cash_flow', 'other'])->nullable();
    $table->enum('normal_balance', ['debit', 'credit'])->default('debit');
    $table->boolean('is_active')->default(true);
    $table->boolean('is_cash_account')->default(false);
    $table->boolean('is_bank_account')->default(false);
    $table->boolean('is_default')->default(false);
    $table->unsignedTinyInteger('level')->default(1);
    $table->text('description')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
$table->unsignedBigInteger('updated_by')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
