<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    /** Tambah 1 kunjungan untuk hari ini. */
    public static function hit(): void
    {
        $row = static::firstOrCreate(['date' => now()->toDateString()], ['count' => 0]);
        $row->increment('count');
    }

    public static function stats(): array
    {
        try {
            return [
                'today' => (int) static::where('date', now()->toDateString())->value('count'),
                'month' => (int) static::where('date', '>=', now()->startOfMonth()->toDateString())->sum('count'),
                'total' => (int) static::sum('count'),
            ];
        } catch (\Throwable $e) {
            return ['today' => 0, 'month' => 0, 'total' => 0];
        }
    }

    public static function dailyTrend(int $days = 30): array
    {
        $lastDay = now()->startOfDay();
        $firstDay = $lastDay->copy()->subDays(max(1, $days) - 1);
        $counts = static::whereBetween('date', [$firstDay->toDateString(), $lastDay->toDateString()])
            ->pluck('count', 'date');

        $trend = [];
        for ($date = $firstDay->copy(); $date->lte($lastDay); $date->addDay()) {
            $dateKey = $date->toDateString();
            $trend[] = [
                'date' => $dateKey,
                'label' => $date->format('d/m'),
                'count' => (int) $counts->get($dateKey, 0),
            ];
        }

        return $trend;
    }
}
