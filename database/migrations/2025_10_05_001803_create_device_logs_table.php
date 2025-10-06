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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            
            // Simplified location tracking - only entity and most specific location
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            
            $table->string('uid'); // HAOS entity UID like 'sensor.temperature_1_ksv'
            $table->string('state');
            $table->string('unit')->nullable();
            $table->timestamp('last_changed');
            $table->timestamp('last_reported');
            $table->string('comparison_hash')->index();
            $table->timestamps();
            
            $table->index(['device_id', 'created_at']);
            $table->index(['entity_id', 'created_at']);
            $table->index(['location_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
