<?php

namespace App\Models;

use App\Traits\CommonModelArrtibuteTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Slider extends Model
{
    use CommonModelArrtibuteTrait;
    protected $guarded = [];
    protected $casts = [
        'status' => 'boolean',
    ];
    protected $hidden = ['banner'];
    protected $appends = ['banner_url', 'status_name'];

    public function getBannerUrlAttribute()
    {
        return $this->banner
            ? Storage::disk('cloudinary')->url($this->banner)
            : null;
    }
}
