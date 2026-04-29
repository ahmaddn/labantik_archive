<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GoogleGraduationMapel extends Model
{
    use HasUuids;
    protected $table = 'google_graduation_mapel';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'graduation_id',
        'mapel_id',
        'score',
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
    public function graduation()
    {
        return $this->belongsTo(GoogleGraduation::class, 'graduation_id', 'uuid');
    }

    public function mapel()
    {
        return $this->belongsTo(GoogleMapel::class, 'mapel_id', 'uuid');
    }
}
