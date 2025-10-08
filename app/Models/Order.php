<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = ['id'];

    public const DISPLAY_TYPES = [
        10 => 'Jenis JJ Coin 10 : 10 detik',
        20 => 'Jenis JJ Coin 20 : 15 detik',
        30 => 'Jenis JJ Coin 30 : 25 detik',
        99 => 'Jenis JJ Coin 99 : 60 detik',
    ];

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn($value, $attributes) => match ($attributes['status']) {
            'pending'  => 'warning',
            'rejected' => 'danger',
            'approved' => 'success',
            default    => 'secondary',
        });
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(
            fn($value, $attributes) => match ($attributes['status']) {
                'pending'  => 'Proses',
                'rejected' => 'Ditolak',
                'approved' => 'Disetujui',
                default    => ucfirst($attributes['status']),
            }
        );
    }

    protected function displayTypeLabel(): Attribute
    {
        return Attribute::get(
            fn($value, $attributes) => self::DISPLAY_TYPES[$attributes['display_type']] ?? null
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(UploadService::class, 'service_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
