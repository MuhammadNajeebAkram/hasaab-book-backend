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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique();
    //$table->enum('type', ['cash payment', 'bank payment', 'cash receipt', 'bank receipt', 'journal', 'salary', 'advance salary', 'employee loan']);
    $table->string('type');
    $table->enum('payment_mode', ['cash', 'bank', 'journal']);
    $table->unsignedBigInteger('payment_account')->nullable();
    $table->date('voucher_date');
    
    $table->text('description')->nullable();
    $table->string('transaction_no')->nullable();
    $table->boolean('is_posted')->default(false);
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->unsignedBigInteger('posted_by')->nullable();
    $table->dateTime('posted_at')->nullable();

    
        
    $table->timestamps();

    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('payment_account')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
