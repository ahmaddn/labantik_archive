<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleCertificate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'google_certificates';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'certificate_number_format',
        'orientation',
        'background_image',
        'show_header',
        'show_number',
        'show_back_page',
        'header_left_logo',
        'header_right_logo',
        'header_title',
        'header_subtitle',
        'main_title',
        'sub_title',
        'role_caption',
        'content_text',
        'place_date',
        'signer_1_title',
        'signer_1_employee_id',
        'back_page_title',
        'signer_2_title',
        'signer_2_employee_id',
        'status',
    ];

    protected $casts = [
        'show_header'    => 'boolean',
        'show_number'    => 'boolean',
        'show_back_page' => 'boolean',
    ];

    /**
     * Structure / Materi (Halaman Belakang)
     */
    public function structures(): HasMany
    {
        return $this->hasMany(GoogleCertificateStructure::class, 'certificate_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Relasi ke Roles yang di-attach
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'google_certificate_roles', 'certificate_id', 'role_id');
    }

    /**
     * Penandatangan 1 (e.g. Kepala Sekolah) -> Employee
     */
    public function signer1Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signer_1_employee_id');
    }

    /**
     * Penandatangan 2 (e.g. Ketua Pelaksana) -> Employee
     */
    public function signer2Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signer_2_employee_id');
    }

    /**
     * Parse placeholder {nama}, {nip}, {nis}, {nisn}, {kelas}, {email}, {year} secara dinamis untuk user tertentu
     */
    public function parsePlaceholder(?string $text, ?User $user = null): string
    {
        if (!$text) {
            return '';
        }

        if (!$user) {
            return $text;
        }

        $replacements = [
            '{nama}'  => $user->name ?? $user->full_name ?? '',
            '{nip}'   => $user->nip ?? $user->employee?->nip ?? '-',
            '{nis}'   => $user->nis ?? '-',
            '{nisn}'  => $user->nisn ?? '-',
            '{kelas}' => $user->class_name ?? '-',
            '{email}' => $user->email ?? '',
            '{year}'  => date('Y'),
        ];

        return strtr($text, $replacements);
    }
}
