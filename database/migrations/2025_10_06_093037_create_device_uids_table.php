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
        Schema::create('device_uids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('uid')->index();
            $table->string('uid_type'); // e.g., 'binary_sensor', 'sensor', 'switch'
            $table->string('description')->nullable(); // e.g., 'Smoke Detection', 'Battery Level', 'Amount'
            $table->boolean('is_primary')->default(false); // Mark one UID as primary for display
            $table->timestamps();
            
            $table->unique(['device_id', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_uids');
    }
};
