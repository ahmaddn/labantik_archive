<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleDriveSubCategoryOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'google_drive_sub_category_id',
        'name',
        'order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the sub-category that owns this option.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(GoogleDriveSubCategory::class, 'google_drive_sub_category_id');
    }
}
