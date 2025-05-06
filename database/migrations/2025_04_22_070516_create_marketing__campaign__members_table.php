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
        Schema::create('marketing_campaign_members', function (Blueprint $table) {
            $table->id();
    $table->unsignedBigInteger('marketing_campaign_id');
    $table->unsignedBigInteger('employee_id');
    $table->enum('role', ['Leader', 'Helper']); // team lead, support, etc.
    $table->timestamps();

    $table->foreign('marketing_campaign_id')->references('id')->on('marketing_campaigns')->onDelete('cascade');
    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_members');
    }
};
