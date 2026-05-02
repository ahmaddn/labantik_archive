<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GoogleMapel extends Model
{
    use HasUuids; // Otomatis generate uuid saat create

    protected $table = 'google_mapel';

    protected $primaryKey = 'uuid'; // FIX: primary key adalah uuid, bukan id
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'class_id',
        'expertise_id',
        'name',
        'type',
        'order',
        'join',
        'has_na',
    ];

    public function graduations()
    {
        return $this->hasMany(GoogleGraduationMapel::class, 'mapel_id', 'uuid');
    }

    public function expertise()
    {
        return $this->belongsTo(ExpertiseConcentration::class, 'expertise_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo(RefClass::class, 'class_id', 'id');
    }
}
