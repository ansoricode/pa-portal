<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('period', 30)->nullable();
        });

        DB::table('documents')
            ->where('category', 'sakip')
            ->where('description', 'like', 'Periode:%')
            ->orderBy('id')
            ->chunkById(100, function ($documents): void {
                foreach ($documents as $document) {
                    DB::table('documents')->where('id', $document->id)->update([
                        'period' => trim(substr($document->description, strlen('Periode:'))),
                        'description' => null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('documents')
            ->where('category', 'sakip')
            ->whereNotNull('period')
            ->orderBy('id')
            ->chunkById(100, function ($documents): void {
                foreach ($documents as $document) {
                    if ($document->description === null) {
                        DB::table('documents')->where('id', $document->id)->update([
                            'description' => 'Periode: '.$document->period,
                        ]);
                    }
                }
            });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('period');
        });
    }
};
