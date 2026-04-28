<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StudentSignature extends Model
{
    use HasUuids;

    protected $table = 'google_student_signatures';

    protected $fillable = [
        'signature_data',
    ];
}
