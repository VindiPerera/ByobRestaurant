<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Read a setting value (cached), casting by its stored type.
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::rememberForever("setting:{$key}", function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'float', 'decimal' => (float) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Create or update a setting and refresh its cache.
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type]
        );

        Cache::forget("setting:{$key}");
    }
}
