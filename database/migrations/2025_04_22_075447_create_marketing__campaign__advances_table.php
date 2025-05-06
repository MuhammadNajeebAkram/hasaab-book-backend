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
        Schema::create('marketing_campaign_advances', function (Blueprint $table) {
            $table->id();

    // Related campaign
    $table->unsignedBigInteger('campaign_id');
    $table->foreign('campaign_id')->references('id')->on('marketing_campaigns')->onDelete('cascade');

   
    // How much was given
    $table->decimal('amount', 15, 2);
   

    // Link to voucher if applicable
    $table->unsignedBigInteger('voucher_id');
    $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');

    // Date of giving the advance
    $table->date('advance_date');

    // Optional remarks or purpose
    $table->text('remarks')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_advances');
    }
};
