<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;

class UploadService extends Model
{
    use Cacheable;

    protected $guarded = ['id'];
    protected $casts = [
        'rules' => 'array',
    ];

    protected array $cacheKeys = [
        'upload_services.all',
    ];

    public static function allCached()
    {
        return self::rememberCache('upload_services.all', 3600, fn() => self::all());
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = preg_replace('/\D/', '', $value);
    }

    public function getFormattedPriceAttribute(): string
    {
        return formatPrice($this->attributes['price']);
    }
}
