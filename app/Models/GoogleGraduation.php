<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleGraduation extends Model
{
    protected $table = 'google_graduation';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'user_id',
        'letter_number',
        'graduation_date',
    ];

    public function mapels()
    {
        return $this->hasMany(GoogleGraduationMapel::class, 'graduation_id', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
