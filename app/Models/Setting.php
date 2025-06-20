<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'options',
        'is_public',
        'is_required',
        'validation_rules',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            Cache::forget('app_settings');
        });

        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Get setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value by key.
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            return $setting->update(['value' => $value]);
        }

        return false;
    }

    /**
     * Get all settings cached.
     */
    public static function getAllCached(): array
    {
        return Cache::remember('app_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->toArray();
    }

    /**
     * Get formatted value based on type.
     */
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'number':
                return (float) $this->value;
            case 'json':
                return json_decode($this->value, true);
            case 'file':
                return $this->value ? asset('storage/' . $this->value) : null;
            default:
                return $this->value;
        }
    }

    /**
     * Scope for public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for required settings.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}