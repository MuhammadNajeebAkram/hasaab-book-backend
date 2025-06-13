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
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();

    $table->string('name');
    $table->text('purpose')->nullable();
    $table->enum('type', ['traditional', 'digital']); 
    
    // Dates
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();

    // Budgeting
    $table->decimal('estimated_budget', 15, 2)->default(0);
    $table->decimal('actual_cost', 15, 2)->default(0); // Update as expenses get recorded

    // Tracking & Ownership
    $table->unsignedBigInteger('created_by');
    $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
