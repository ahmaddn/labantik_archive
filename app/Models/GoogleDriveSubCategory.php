<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GoogleDriveSubCategory extends Model
{
    use HasUuids;

    protected $fillable = [
        'google_category_id',
        'name',
        'slug',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Get the category that owns the sub-category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveCategory::class, 'google_category_id');
    }

    /**
     * Get the options for this sub-category.
     */
    public function options(): HasMany
    {
        return $this->hasMany(GoogleDriveSubCategoryOption::class)->orderBy('order');
    }

    /**
     * Get the files that belong to this sub-category.
     */
    public function files(): HasMany
    {
        return $this->hasMany(GoogleDriveFile::class, 'google_drive_sub_category_id');
    }
}
