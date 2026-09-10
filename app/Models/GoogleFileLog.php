<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoogleFileLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'google_file_logs';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'google_drive_file_id',
        'google_drive_sub_category_id',
        'sub_category_option',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveFile::class, 'google_drive_file_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveSubCategory::class, 'google_drive_sub_category_id');
    }
}
