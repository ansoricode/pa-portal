<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->renameColumn('drive_url', 'map_url');
        });

        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->text('map_url')->nullable()->change();
        });

        DB::table('jurisdictions')->update(['map_url' => null]);
    }

    public function down(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->renameColumn('map_url', 'drive_url');
        });
    }
};
