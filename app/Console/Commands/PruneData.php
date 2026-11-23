<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\SurveyResponse;
use Illuminate\Console\Command;

class PruneData extends Command
{
    protected $signature = 'portal:prune {--logs=365 : Hapus log aktivitas lebih tua dari N hari} {--survey-ip=90 : Hapus alamat IP survei lebih tua dari N hari}';

    protected $description = 'Retensi data: hapus log aktivitas lama dan anonimkan IP pada masukan survei lama';

    public function handle(): int
    {
        $logs = ActivityLog::where('created_at', '<', now()->subDays((int) $this->option('logs')))->delete();
        $ips = SurveyResponse::whereNotNull('ip_address')
            ->where('created_at', '<', now()->subDays((int) $this->option('survey-ip')))
            ->update(['ip_address' => null]);

        $this->info("Log aktivitas dihapus: $logs | IP survei dianonimkan: $ips");

        return self::SUCCESS;
    }
}
