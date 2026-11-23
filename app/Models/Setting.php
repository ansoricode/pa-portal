<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected static ?array $cache = null;

    public static function get(string $key, $default = null)
    {
        if (static::$cache === null) {
            try {
                static::$cache = static::query()->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                static::$cache = [];
            }
        }

        $value = static::$cache[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache = null;
    }
}
