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
        Schema::create('advance_salaries', function (Blueprint $table) {
            $table->id();
    $table->foreignId('employee_id')->constrained('employees', 'id')->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained('vouchers', 'id')->onDelete('cascade');
    $table->foreignId('account_id')->constrained('chart_of_accounts','id')->onDelete('cascade');
    $table->decimal('amount', 15, 2);
    $table->date('advance_date');
    $table->string('reason')->nullable();
    $table->boolean('is_settled')->default(false); // useful for flagging settlement
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_salaries');
    }
};
