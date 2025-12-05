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
        Schema::create('production_trends', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('production', 10, 2);
            $table->decimal('target', 10, 2);
            $table->decimal('traffic_volume', 10, 2)->nullable(); // Traffic volume in vehicles
            $table->decimal('average_speed', 5, 2)->nullable(); // Average speed in km/h
            $table->integer('incidents')->nullable(); // Number of traffic incidents
            $table->decimal('congestion_index', 5, 2)->nullable(); // Congestion level (0-100)
            $table->integer('signal_changes')->nullable(); // Number of signal changes
            $table->decimal('green_wave_efficiency', 5, 2)->nullable(); // Green wave efficiency percentage
            $table->timestamps();
            
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_trends');
    }
};
