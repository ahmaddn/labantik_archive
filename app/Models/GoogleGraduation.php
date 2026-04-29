<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleGraduation extends Model
{
    protected $table = 'google_graduation';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_id',
        'letter_number',
        'graduation_date',
    ];

    public function mapels()
    {
        return $this->hasMany(GoogleGraduationMapel::class, 'graduation_id', 'uuid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(RefStudent::class, 'user_id', 'id');
    }
}
