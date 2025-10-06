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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('haos_instance_id')->constrained('haos_instances')->onDelete('cascade');
            $table->string('name');
            $table->string('device_type');
            $table->string('physical_device_name'); // e.g., "Smoke Sensor 1-KSV"
            
            // Mandatory location fields (Entity > Zone > Level)
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('zone_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('locations')->onDelete('cascade');
            
            // Optional location fields (Space > Location > Sub Location)
            $table->foreignId('space_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('sub_location_id')->nullable()->constrained('locations')->onDelete('set null');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
