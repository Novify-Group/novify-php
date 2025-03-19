<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class MerchantUser extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'merchant_id',
        'branch_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'password',
        'photo_path',
        'id_picture_path',
        'role',
        'is_active',
        'force_password_change'
    ];

    protected $hidden = [
        'password',
        'id_picture_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'force_password_change' => 'boolean'
    ];

    protected $appends = [
        'photo_url',
        'id_picture_url'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'merchant_id' => $this->merchant_id,
            'user_type' => 'merchant_user'
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? url('storage/' . $this->photo_path) : null;
    }

    public function getIdPictureUrlAttribute(): ?string
    {
        return $this->id_picture_path ? url('storage/' . $this->id_picture_path) : null;
    }
} 