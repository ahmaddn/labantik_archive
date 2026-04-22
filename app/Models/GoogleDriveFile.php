<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoogleDriveFile extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'document_name',
        'google_category_id',
        'expertise_id',
        'google_drive_sub_category_id',
        'google_file_id',
        'name',
        'mime_type',
        'size',
        'web_view_link',
        'web_content_link',
        'year',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'year' => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveCategory::class, 'google_category_id');
    }

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(ExpertiseConcentration::class, 'expertise_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveSubCategory::class, 'google_drive_sub_category_id');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
