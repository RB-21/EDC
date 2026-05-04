<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AiSetting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';

    protected $guarded = [];

    public static function getValue(string $key, $default = null)
    {
        if (!Schema::hasTable('ai_settings')) {
            return $default;
        }

        $row = static::query()->where('key', $key)->first();
        if (!$row) {
            return $default;
        }

        return $row->value ?? $default;
    }
}

