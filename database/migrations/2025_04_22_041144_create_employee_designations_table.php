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
        Schema::create('employee_designations', function (Blueprint $table) {
            $table->id();
    $table->string('name');                         // e.g., "Sales Executive"    
    $table->text('description')->nullable();         // Optional job role/responsibility   
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable(); 
    $table->timestamps();

    $table->foreign('created_by')->references('id')->on('users')->onDelete('no action');
    $table->foreign('updated_by')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_designations');
    }
};
