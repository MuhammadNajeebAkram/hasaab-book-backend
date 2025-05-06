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
        Schema::create('marketing_campaign_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->onDelete('cascade');            
            $table->text('activity'); // e.g., "Visited 5 book stores in Multan"
            $table->date('activity_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_activity_logs');
    }
};
