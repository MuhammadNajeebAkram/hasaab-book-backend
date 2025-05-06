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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('contact_no')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('salary_account');
            $table->unsignedBigInteger('salary_advance_account');
            $table->unsignedBigInteger('employee_loan_account');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('no action');
            $table->foreign('salary_account')->references('id')->on('chart_of_accounts')->onDelete('no action');
            $table->foreign('salary_advance_account')->references('id')->on('chart_of_accounts')->onDelete('no action');
            $table->foreign('employee_loan_account')->references('id')->on('chart_of_accounts')->onDelete('no action');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('no action');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
