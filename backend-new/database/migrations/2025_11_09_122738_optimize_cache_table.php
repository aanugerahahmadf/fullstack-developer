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
        Schema::table('cache', function (Blueprint $table) {
            $table->index(['key', 'expiration']);
            $table->string('key', 255)->change();
            $table->longText('value')->change();
            $table->integer('expiration')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->dropIndex(['key', 'expiration']);
            $table->dropIndex(['expiration']);
            $table->string('key')->change();
            $table->text('value')->change();
            $table->integer('expiration')->change();
        });
    }
};
