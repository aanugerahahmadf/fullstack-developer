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
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('username')->default('admin');
            $table->string('password')->default('password.123');
            $table->string('ip_address');
            $table->unsignedSmallInteger('rtsp_port')->default(554);
            $table->unsignedSmallInteger('hls_port')->default(8000);
            $table->string('ip_rtsp_url')->nullable();
            $table->string('hls_url')->nullable();
            $table->index('building_id');
            $table->index('room_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};
