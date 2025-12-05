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
        // Check if the column doesn't already exist before adding it
        if (!Schema::hasColumn('users', config('filament-edit-profile.locale_column', 'locale'))) {
            Schema::table('users', function (Blueprint $table) {
                $table->string(config('filament-edit-profile.locale_column', 'locale'))->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before dropping it
        if (Schema::hasColumn('users', config('filament-edit-profile.locale_column', 'locale'))) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(config('filament-edit-profile.locale_column', 'locale'));
            });
        }
    }
};