<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->text('villages')->nullable();
            $table->string('radius', 100)->nullable();
            $table->string('drive_url', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->dropColumn(['villages', 'radius', 'drive_url']);
        });
    }
};
