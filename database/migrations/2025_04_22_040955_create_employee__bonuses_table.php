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
        Schema::create('employee_bonus', function (Blueprint $table) {
            $table->id();
    $table->foreignId('employee_id')->constrained()->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 15, 2);
    $table->string('type')->nullable(); // e.g., 'performance', 'eid', 'year-end'
    $table->string('description')->nullable();
    
    $table->date('bonus_date'); // when it's awarded
    
    
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bonus');
    }
};
