<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core_roles';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'code',
        'status',
        'app_type',
    ];

    /**
     * Relationship dengan User
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'assoc_user_roles',
            'role_id',
            'user_id'
        );
    }
}
