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
        Schema::create('marketing_campaign_transports', function (Blueprint $table) {
            $table->id();

    $table->unsignedBigInteger('marketing_campaign_id');
    $table->enum('transport_type', ['company_vehicle', 'rented_vehicle', 'public_transport']);

    // If vehicle used
    $table->unsignedBigInteger('vehicle_id')->nullable(); // FK to vehicles table
    $table->string('driver_name')->nullable();

    // Travel details
    $table->decimal('start_meter', 10, 2)->nullable();
    $table->decimal('end_meter', 10, 2)->nullable();
    $table->decimal('fuel_expense', 12, 2)->default(0);
    $table->decimal('rental_cost', 12, 2)->default(0);
    $table->text('remarks')->nullable();

    $table->timestamps();

    // Foreign Keys
    $table->foreign('marketing_campaign_id')->references('id')->on('marketing_campaigns')->onDelete('cascade');
    $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_transports');
    }
};
