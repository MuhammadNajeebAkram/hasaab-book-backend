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
        Schema::create('royalty_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('royalty_schedule_id')->constrained('royalty_payment_schedules')->onDelete('cascade');
            $table->integer('cheque_no');
            $table->date('issue_date');
            $table->date('cheque_date');
            $table->decimal('amount', 14, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('royalty_cheques');
    }
};
