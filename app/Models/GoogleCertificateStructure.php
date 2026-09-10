<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCertificateStructure extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'google_certificate_structures';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'certificate_id',
        'sort_order',
        'materi_name',
        'time_allocation',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(GoogleCertificate::class, 'certificate_id');
    }
}
