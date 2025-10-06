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
        Schema::create('haos_connection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('haos_instance_id')->constrained('haos_instances')->onDelete('cascade');
            $table->enum('status', ['success', 'failed', 'timeout']);
            $table->text('error_message')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->integer('devices_synced')->default(0);
            $table->timestamps();
            
            $table->index(['haos_instance_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('haos_connection_logs');
    }
};
