<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'core_users';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'last_login',
        'class_id',
        'academic_year',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login'        => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────

    /**
     * Role (Many-to-Many)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'assoc_user_roles',
            'user_id',
            'role_id'
        )->withPivot('app_type');
    }

    /**
     * Google Drive files
     */
    public function driveFiles(): HasMany
    {
        return $this->hasMany(GoogleDriveFile::class, 'user_id');
    }

    /**
     * Data kepegawaian (guru / guru TU) dari core_employees
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Data akademik siswa terbaru dari ref_student_academic_years
     */
    public function latestStudentAcademicYear(): HasOne
    {
        return $this->hasOne(StudentAcademicYear::class, 'student_id')
            ->latestOfMany();
    }

    /**
     * Kelas langsung dari core_users.class_id → ref_classes
     */
    public function refClass(): BelongsTo
    {
        return $this->belongsTo(RefClass::class, 'class_id');
    }

    // ─── Accessors ───────────────────────────────────────────────────────

    /**
     * NIP dari core_employees (digunakan oleh guru dan guru TU)
     */
    public function getNipAttribute(): ?string
    {
        return $this->employee?->nip;
    }

    /**
     * NIS siswa.
     * Jika tabel ref_student_academic_years memiliki kolom `nis`, akan terbaca otomatis.
     * Sesuaikan jika NIS disimpan di tabel lain.
     */
    public function getNisAttribute(): ?string
    {
        // Prefer field on latest student academic year if present
        if ($this->latestStudentAcademicYear?->nis) {
            return $this->latestStudentAcademicYear->nis;
        }

        // Fallback: some installs store student numbers in `ref_students.student_number`
        $student = DB::table('ref_students')->where('user_id', $this->id)->first();
        if ($student) {
            return $student->student_number
                ?? $student->national_student_number
                ?? $student->national_identification_number
                ?? null;
        }

        return null;
    }

    /**
     * Nama kelas: prioritas dari core_users.class_id → ref_classes,
     * fallback dari ref_student_academic_years → ref_classes.
     */
    public function getClassNameAttribute(): ?string
    {
        // Priority: direct refClass, then latestStudentAcademicYear->refClass
        if ($this->refClass?->name) {
            return $this->refClass->name;
        }

        if ($this->latestStudentAcademicYear?->refClass?->name) {
            return $this->latestStudentAcademicYear->refClass->name;
        }

        // Fallback: if latestStudentAcademicYear has class_id, fetch name from ref_classes
        $classId = $this->latestStudentAcademicYear?->class_id ?? null;
        if ($classId) {
            $row = DB::table('ref_classes')->where('id', $classId)->first();
            return $row->name ?? null;
        }

        return null;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    /**
     * Check apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->roles()
            ->where('code', 'super-admin')
            ->exists();
    }
    public function isSiswa(): bool
    {
        return $this->roles()
            ->where('code', 'siswa')
            ->exists();
    }

    /**
     * Get role names
     */
    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Ambil show route berdasarkan role pertama yang dikenali
     * (helper untuk history page)
     */
    public function getAdminShowRouteAttribute(): ?string
    {
        $codes = $this->roles->pluck('code')->toArray();

        if (in_array('siswa', $codes)) {
            return route('admin.students.show', $this->id);
        }
        if (in_array('guru', $codes)) {
            return route('admin.teachers.show', $this->id);
        }
        if (in_array('guru-piket', $codes)) {
            return route('admin.piket.show', $this->id);
        }

        return null;
    }
}
