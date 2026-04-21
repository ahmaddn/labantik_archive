<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefClass extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ref_classes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'teacher_name',
        'nip_number',
        'academic_level',
        'academic_year',
        'expertise_program_id',
        'expertise_concentration_id',
    ];
}
