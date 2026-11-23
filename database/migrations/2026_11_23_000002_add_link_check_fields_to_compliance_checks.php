<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compliance_checks', function (Blueprint $table) {
            $table->string('link_status', 20)->default('unchecked');
            $table->unsignedSmallInteger('link_http_status')->nullable();
            $table->text('link_message')->nullable();
            $table->timestamp('link_checked_at')->nullable();
            $table->unsignedBigInteger('link_checked_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('compliance_checks', function (Blueprint $table) {
            $table->dropColumn([
                'link_status',
                'link_http_status',
                'link_message',
                'link_checked_at',
                'link_checked_by',
            ]);
        });
    }
};
