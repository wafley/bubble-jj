<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $guarded = ['id'];

    public function setUsername1Attribute($value)
    {
        $this->attributes['username_1'] = $this->sanitizeUsername($value);
    }

    public function setUsername2Attribute($value)
    {
        $this->attributes['username_2'] = $this->sanitizeUsername($value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function sanitizeUsername(?string $value): ?string
    {
        if (!$value) return null;

        $value = strtolower(trim($value));
        $value = preg_replace('/^(https?:\/\/)?(www\.)?tiktok\.com\/@/i', '', $value);
        return ltrim($value, '@');
    }
}
