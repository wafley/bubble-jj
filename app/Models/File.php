<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::deleting(function ($file) {
            if (Storage::disk('public')->exists($file->filename)) {
                Storage::disk('public')->delete($file->filename);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
