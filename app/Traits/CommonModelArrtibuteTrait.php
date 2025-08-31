<?php

namespace App\Traits;

use Carbon\Carbon;

trait CommonModelArrtibuteTrait
{
    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y, h:i:s A') : null;
    }
    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y, h:i:s A') : null;
    }
    public function getStatusNameAttribute()
    {
        return $this->status ? "Active" : "Inactive";
    }
}
