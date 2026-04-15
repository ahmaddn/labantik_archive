<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class GoogleToken extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'type',
        'access_token',
        'refresh_token',
        'expires_in',
        'token_created_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'token_created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Encrypt access token
     */
    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt access token
     */
    public function getAccessTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    /**
     * Encrypt refresh token
     */
    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt refresh token
     */
    public function getRefreshTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    /**
     * Cek apakah token expired
     */
    public function isExpired(): bool
    {
        return $this->token_created_at->addSeconds($this->expires_in)->isPast();
    }
}
