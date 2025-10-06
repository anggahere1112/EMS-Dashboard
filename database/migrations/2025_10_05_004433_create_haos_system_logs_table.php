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
        Schema::create('haos_system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('haos_instance_id')->constrained('haos_instances')->onDelete('cascade');
            $table->string('uid'); // HAOS entity UID like 'sensor.disk_free_haos_1_ksv'
            $table->string('state');
            $table->string('unit')->nullable();
            $table->timestamp('last_changed');
            $table->timestamp('last_reported');
            $table->string('comparison_hash');
            $table->timestamps();
            
            $table->index(['haos_instance_id', 'created_at']);
            $table->index(['uid', 'created_at']);
            $table->index(['comparison_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('haos_system_logs');
    }
};
