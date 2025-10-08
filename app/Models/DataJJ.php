<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataJJ extends Model
{
    protected $table = 'data_jj';
    protected $guarded = ['id'];

    public const DISPLAY_TYPES = [
        10 => 'Jenis JJ Coin 10 : 10 detik',
        20 => 'Jenis JJ Coin 20 : 15 detik',
        30 => 'Jenis JJ Coin 30 : 25 detik',
        99 => 'Jenis JJ Coin 99 : 60 detik',
    ];

    protected function displayTypeLabel(): Attribute
    {
        return Attribute::get(
            fn($value, $attributes) => self::DISPLAY_TYPES[$attributes['display_type']] ?? null
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(
            fn($value, $attributes) => $attributes['sts_active']
                ? 'Aktif'
                : 'Tidak Aktif'
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(
            fn($value, $attributes) => $attributes['sts_active']
                ? 'success'
                : 'danger'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
