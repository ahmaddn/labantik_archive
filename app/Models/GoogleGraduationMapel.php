<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleGraduationMapel extends Model
{
    protected $table = 'google_graduation_mapel';

    protected $fillable = [
        'graduation_id',
        'mapel_id',
    ];

    public function graduation()
    {
        return $this->belongsTo(GoogleGraduation::class, 'graduation_id', 'id');
    }
}
