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
        Schema::create('unit_performance', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name');
            $table->integer('efficiency');
            $table->integer('capacity');
            $table->timestamps();
            
            $table->index('unit_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_performance');
    }
};
