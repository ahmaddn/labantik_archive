<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertiseConcentration extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core_expertise_concentrations';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function driveFiles(): HasMany
    {
        return $this->hasMany(GoogleDriveFile::class, 'expertise_id');
    }
}
