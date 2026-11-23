<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('section')->index();
            $t->string('slug');
            $t->string('title');
            $t->string('icon')->nullable();
            $t->longText('content')->nullable();
            $t->string('image')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['section', 'slug']);
        });

        Schema::create('sliders', function (Blueprint $t) {
            $t->id();
            $t->string('badge')->nullable();
            $t->string('title');
            $t->text('subtitle')->nullable();
            $t->string('button_text')->nullable();
            $t->string('button_url')->nullable();
            $t->string('image')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('shortcuts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('description')->nullable();
            $t->string('icon')->default('bi-link-45deg');
            $t->string('url');
            $t->boolean('is_external')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('news', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('category')->default('Pelayanan Masyarakat');
            $t->text('excerpt')->nullable();
            $t->longText('content');
            $t->string('image')->nullable();
            $t->string('author')->nullable();
            $t->dateTime('published_at')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->string('badge');
            $t->string('title');
            $t->string('label')->nullable();
            $t->date('published_at')->nullable();
            $t->string('url')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('court_schedules', function (Blueprint $t) {
            $t->id();
            $t->date('hearing_date')->index();
            $t->string('case_number');
            $t->string('agenda');
            $t->string('courtroom');
            $t->timestamps();
        });

        Schema::create('panggilan_ghaib', function (Blueprint $t) {
            $t->id();
            $t->string('party_name');
            $t->string('case_number');
            $t->date('hearing_date');
            $t->string('file_path')->nullable();
            $t->string('external_url')->nullable();
            $t->timestamps();
        });

        Schema::create('documents', function (Blueprint $t) {
            $t->id();
            $t->string('category')->index();
            $t->string('title');
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('year')->nullable();
            $t->string('file_path')->nullable();
            $t->string('external_url')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('jurisdictions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('officials', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('position');
            $t->string('rank')->nullable();
            $t->string('nip')->nullable();
            $t->string('photo')->nullable();
            $t->string('category')->default('pejabat'); // pejabat | pegawai | honorer
            $t->date('tmt')->nullable();
            $t->text('education')->nullable();
            $t->text('career')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->boolean('is_lhkpn')->default(false);
            $t->timestamps();
        });

        Schema::create('lhkpn_reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('official_id')->constrained('officials')->cascadeOnDelete();
            $t->unsignedSmallInteger('year');
            $t->string('file_path')->nullable();
            $t->string('external_url')->nullable();
            $t->timestamps();
        });

        Schema::create('survey_indices', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('score');
            $t->string('label')->nullable();
            $t->string('icon')->default('bi-emoji-smile');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $t) {
            $t->id();
            $t->unsignedTinyInteger('rating');
            $t->text('comment')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->timestamps();
        });

        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->string('date', 10)->unique();
            $t->unsignedInteger('count')->default(0);
        });
    }

    public function down(): void
    {
        foreach (['visits', 'survey_responses', 'survey_indices', 'lhkpn_reports', 'officials', 'jurisdictions',
                  'documents', 'panggilan_ghaib', 'court_schedules', 'announcements', 'news', 'shortcuts',
                  'sliders', 'pages', 'settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
