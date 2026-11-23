<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Indeks untuk daftar yang diurutkan/disaring/dipaginasi agar tetap cepat saat data membesar. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $t) {
            $t->index(['is_published', 'published_at']);
            $t->index('category');
        });
        Schema::table('announcements', fn (Blueprint $t) => $t->index('published_at'));
        Schema::table('panggilan_ghaib', fn (Blueprint $t) => $t->index('hearing_date'));
        Schema::table('documents', fn (Blueprint $t) => $t->index(['category', 'is_active', 'year']));
        Schema::table('survey_responses', fn (Blueprint $t) => $t->index('created_at'));
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $t) {
            $t->dropIndex(['is_published', 'published_at']);
            $t->dropIndex(['category']);
        });
        Schema::table('announcements', fn (Blueprint $t) => $t->dropIndex(['published_at']));
        Schema::table('panggilan_ghaib', fn (Blueprint $t) => $t->dropIndex(['hearing_date']));
        Schema::table('documents', fn (Blueprint $t) => $t->dropIndex(['category', 'is_active', 'year']));
        Schema::table('survey_responses', fn (Blueprint $t) => $t->dropIndex(['created_at']));
    }
};
