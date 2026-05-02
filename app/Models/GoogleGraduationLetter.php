<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleGraduationLetter extends Model
{
    protected $table = 'google_graduation_letters';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'letter_number',
        'transcript_letter_number',
        'academic_year',
        'headmaster_id',
        'graduation_date',
        'statement',
        'content',
        'stamp_image',
        'signature_image',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = \Illuminate\Support\Str::uuid();
            }
        });
    }
}
