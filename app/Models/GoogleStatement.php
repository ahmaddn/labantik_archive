<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleStatement extends Model
{
    protected $table = 'google_statement';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'user_id',
        'signature_id',
    ];

    public function signature()
    {
        return $this->belongsTo(\App\Models\StudentSignature::class, 'signature_id', 'id');
    }
}
