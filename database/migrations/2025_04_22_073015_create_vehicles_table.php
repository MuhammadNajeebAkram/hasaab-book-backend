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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
    $table->string('name'); // e.g., Corolla 2019
    $table->string('registration_number');
    $table->string('vehicle_type'); // car, van, etc.
    $table->decimal('current_meter', 10, 2)->default(0);
    $table->enum('ownership_type', ['company', 'rented'])->default('company'); 
    $table->boolean('is_active')->default(true);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
