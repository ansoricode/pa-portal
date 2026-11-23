<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('status', 30)->default('unchecked');
            $table->text('target_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_checks');
    }
};
