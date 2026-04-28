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
    //
}
