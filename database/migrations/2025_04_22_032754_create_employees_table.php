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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cnic');
            $table->string('email')->nullable();
            $table->string('address');
            $table->unsignedBigInteger('city_id');
            $table->string('contact_no');
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('branch_id');
            $table->date('joining_date');
            $table->decimal('salary', 15, 2);
            $table->decimal('house_rent', 15, 2)->default(0);
            $table->decimal('travel_allowance', 15, 2)->default(0);
            $table->decimal('medical_allowance', 15, 2)->default(0);
            
            // Optional fields
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('emergency_contact')->nullable();
            $table->string('photo')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        
            $table->timestamps();
        
            // Optional FK constraints
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('designation_id')->references('id')->on('employee_designations');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
