<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Slider extends Model
{
    protected $guarded = [];
    protected $casts = [
        'status' => 'boolean',
    ];
    protected $appends = ['banner_url'];

    public function getBannerUrlAttribute()
    {
        return $this->banner_public_id
            ? Storage::disk('cloudinary')->url($this->banner_public_id)
            : null;
    }
}
