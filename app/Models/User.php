<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'avatar',
        'phone',
        'role',
        'status',
        'email',
        'password',
    ];

    protected $appends = [
        'avatar_url',
        'is_email_verified',
        'status_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'avatar',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // ** Attribute Customizations **
    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y, h:i:s A') : null;
    }
    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y, h:i:s A') : null;
    }
    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d M Y, h:i:s A') : null;
    }
    public function getIsEmailVerifiedAttribute()
    {
        return $this->email_verified_at ? true : false;
    }
    public function getStatusNameAttribute()
    {
        return $this->status ? "Active" : "Inactive";
    }
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? "" : null;
    }
}
