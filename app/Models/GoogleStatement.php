<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleStatement extends Model
{
    protected $table = 'google_statement';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_id',
        'signature_id',
        'print_count',
        'last_print_at',
    ];

    protected $casts = [
        'last_print_at' => 'datetime',
    ];

    /** Relasi ke User (users.id) */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
