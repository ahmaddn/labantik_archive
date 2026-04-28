<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleGraduation extends Model
{
    use HasUuids;

    protected $table = 'google_graduation';

    protected $fillable = [
        'user_id',
        'mapel_id',
        'letter_number',
        'graduation_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
