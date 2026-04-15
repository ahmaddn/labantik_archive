<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'core_users';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'last_login',
        'class_id',
        'academic_year',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship dengan Role (Many-to-Many)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'assoc_user_roles',
            'user_id',
            'role_id'
        )->withPivot('app_type');
    }

    /**
     * Relationship dengan GoogleDriveFile
     */
    public function driveFiles(): HasMany
    {
        return $this->hasMany(GoogleDriveFile::class, 'user_id');
    }

    /**
     * Check apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->roles()
            ->where('code', 'super-admin')
            ->exists();
    }

    /**
     * Get role names
     */
    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
    }
}
