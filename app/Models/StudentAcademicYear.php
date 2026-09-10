<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicYear extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ref_student_academic_years';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year',
    ];

    /**
     * Relasi ke User (siswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relasi ke RefClass
     */
    public function refClass(): BelongsTo
    {
        return $this->belongsTo(RefClass::class, 'class_id');
    }
}
