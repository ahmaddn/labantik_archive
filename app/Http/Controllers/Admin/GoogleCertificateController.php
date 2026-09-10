<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\GoogleCertificate;
use App\Models\GoogleCertificateStructure;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleCertificateController extends Controller
{
    /**
     * Tampilkan daftar sertifikat
     */
    public function index()
    {
        $certificates = GoogleCertificate::with(['roles', 'signer1Employee', 'signer2Employee', 'structures'])
            ->latest()
            ->paginate(10);

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Form tambah sertifikat
     */
    public function create()
    {
        $roles     = Role::orderBy('name')->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('admin.certificates.create', compact('roles', 'employees'));
    }

    /**
     * Simpan sertifikat baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                     => 'required|string|max:255',
            'certificate_number_format' => 'nullable|string|max:255',
            'orientation'               => 'required|in:portrait,landscape',
            'background_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'header_left_logo'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'header_right_logo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'show_header'               => 'nullable|boolean',
            'show_number'               => 'nullable|boolean',
            'show_back_page'            => 'nullable|boolean',
            'header_title'              => 'nullable|string',
            'header_subtitle'           => 'nullable|string',
            'main_title'                => 'required|string|max:255',
            'sub_title'                 => 'required|string|max:255',
            'role_caption'              => 'nullable|string|max:255',
            'content_text'              => 'nullable|string',
            'place_date'                => 'nullable|string|max:255',
            'signer_1_title'            => 'nullable|string|max:255',
            'signer_1_employee_id'      => 'nullable|exists:core_employees,id',
            'back_page_title'           => 'nullable|string|max:255',
            'signer_2_title'            => 'nullable|string|max:255',
            'signer_2_employee_id'      => 'nullable|exists:core_employees,id',
            'roles'                     => 'nullable|array',
            'roles.*'                   => 'exists:core_roles,id',
            'status'                    => 'required|in:active,draft',
            'recipient_type'            => 'required|in:peserta,narasumber',
            'custom_recipient_name'     => 'nullable|string|max:255',
            'word_art_style'            => 'nullable|string|max:100',
            'materi'                    => 'nullable|array',
            'materi.*.name'             => 'required_with:materi|string|max:255',
            'materi.*.hours'            => 'nullable|string|max:100',
        ]);

        $data = [
            'id'                        => (string) Str::uuid(),
            'title'                     => $validated['title'],
            'certificate_number_format' => $validated['certificate_number_format'] ?? null,
            'orientation'               => $validated['orientation'],
            'show_header'               => $request->has('show_header') ? 1 : 0,
            'show_number'               => $request->has('show_number') ? 1 : 0,
            'show_back_page'            => $request->has('show_back_page') ? 1 : 0,
            'header_title'              => $validated['header_title'] ?? null,
            'header_subtitle'           => $validated['header_subtitle'] ?? null,
            'main_title'                => $validated['main_title'],
            'sub_title'                 => $validated['sub_title'],
            'role_caption'              => $validated['role_caption'] ?? null,
            'content_text'              => $validated['content_text'] ?? null,
            'place_date'                => $validated['place_date'] ?? null,
            'signer_1_title'            => $validated['signer_1_title'] ?? 'Kepala Sekolah',
            'signer_1_employee_id'      => $validated['signer_1_employee_id'] ?? null,
            'back_page_title'           => $validated['back_page_title'] ?? null,
            'signer_2_title'            => $validated['signer_2_title'] ?? 'Ketua Pelaksana',
            'signer_2_employee_id'      => $validated['signer_2_employee_id'] ?? null,
            'status'                    => $validated['status'],
            'recipient_type'            => $validated['recipient_type'] ?? 'peserta',
            'custom_recipient_name'     => $validated['custom_recipient_name'] ?? null,
            'word_art_style'            => $validated['word_art_style'] ?? 'none',
        ];

        // Handle File Uploads
        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('certificates/backgrounds', 'public');
        }
        if ($request->hasFile('header_left_logo')) {
            $data['header_left_logo'] = $request->file('header_left_logo')->store('certificates/logos', 'public');
            if ($request->has('remove_logo_bg_auto')) {
                $this->removeWhiteBackground($data['header_left_logo']);
            }
        }
        if ($request->hasFile('header_right_logo')) {
            $data['header_right_logo'] = $request->file('header_right_logo')->store('certificates/logos', 'public');
            if ($request->has('remove_logo_bg_auto')) {
                $this->removeWhiteBackground($data['header_right_logo']);
            }
        }

        $certificate = GoogleCertificate::create($data);

        // Attach Roles
        $certificate->roles()->sync($validated['roles']);

        // Attach Dynamic Structure (Halaman Belakang)
        if (!empty($validated['materi'])) {
            foreach ($validated['materi'] as $index => $item) {
                if (!empty($item['name'])) {
                    GoogleCertificateStructure::create([
                        'id'              => (string) Str::uuid(),
                        'certificate_id'  => $certificate->id,
                        'sort_order'      => $index + 1,
                        'materi_name'     => $item['name'],
                        'time_allocation' => $item['hours'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil dibuat!');
    }

    /**
     * Form edit sertifikat
     */
    public function edit($id)
    {
        $certificate = GoogleCertificate::with(['roles', 'structures', 'signer1Employee', 'signer2Employee'])->findOrFail($id);
        $roles       = Role::orderBy('name')->get();
        $employees   = Employee::orderBy('full_name')->get();

        return view('admin.certificates.edit', compact('certificate', 'roles', 'employees'));
    }

    /**
     * Update sertifikat
     */
    public function update(Request $request, $id)
    {
        $certificate = GoogleCertificate::findOrFail($id);

        $validated = $request->validate([
            'title'                     => 'required|string|max:255',
            'certificate_number_format' => 'nullable|string|max:255',
            'orientation'               => 'required|in:portrait,landscape',
            'background_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'header_left_logo'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'header_right_logo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'show_header'               => 'nullable|boolean',
            'show_number'               => 'nullable|boolean',
            'show_back_page'            => 'nullable|boolean',
            'header_title'              => 'nullable|string',
            'header_subtitle'           => 'nullable|string',
            'main_title'                => 'required|string|max:255',
            'sub_title'                 => 'required|string|max:255',
            'role_caption'              => 'nullable|string|max:255',
            'content_text'              => 'nullable|string',
            'place_date'                => 'nullable|string|max:255',
            'signer_1_title'            => 'nullable|string|max:255',
            'signer_1_employee_id'      => 'nullable|exists:core_employees,id',
            'back_page_title'           => 'nullable|string|max:255',
            'signer_2_title'            => 'nullable|string|max:255',
            'signer_2_employee_id'      => 'nullable|exists:core_employees,id',
            'roles'                     => 'nullable|array',
            'roles.*'                   => 'exists:core_roles,id',
            'status'                    => 'required|in:active,draft',
            'recipient_type'            => 'required|in:peserta,narasumber',
            'custom_recipient_name'     => 'nullable|string|max:255',
            'word_art_style'            => 'nullable|string|max:100',
            'materi'                    => 'nullable|array',
            'materi.*.name'             => 'required_with:materi|string|max:255',
            'materi.*.hours'            => 'nullable|string|max:100',
        ]);

        $data = [
            'title'                     => $validated['title'],
            'certificate_number_format' => $validated['certificate_number_format'] ?? null,
            'orientation'               => $validated['orientation'],
            'show_header'               => $request->has('show_header') ? 1 : 0,
            'show_number'               => $request->has('show_number') ? 1 : 0,
            'show_back_page'            => $request->has('show_back_page') ? 1 : 0,
            'header_title'              => $validated['header_title'] ?? null,
            'header_subtitle'           => $validated['header_subtitle'] ?? null,
            'main_title'                => $validated['main_title'],
            'sub_title'                 => $validated['sub_title'],
            'role_caption'              => $validated['role_caption'] ?? null,
            'content_text'              => $validated['content_text'] ?? null,
            'place_date'                => $validated['place_date'] ?? null,
            'signer_1_title'            => $validated['signer_1_title'] ?? 'Kepala Sekolah',
            'signer_1_employee_id'      => $validated['signer_1_employee_id'] ?? null,
            'back_page_title'           => $validated['back_page_title'] ?? null,
            'signer_2_title'            => $validated['signer_2_title'] ?? 'Ketua Pelaksana',
            'signer_2_employee_id'      => $validated['signer_2_employee_id'] ?? null,
            'status'                    => $validated['status'],
            'recipient_type'            => $validated['recipient_type'] ?? 'peserta',
            'custom_recipient_name'     => $validated['custom_recipient_name'] ?? null,
            'word_art_style'            => $validated['word_art_style'] ?? 'none',
        ];

        // Upload updates
        if ($request->hasFile('background_image')) {
            if ($certificate->background_image) {
                Storage::disk('public')->delete($certificate->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('certificates/backgrounds', 'public');
        }
        if ($request->hasFile('header_left_logo')) {
            if ($certificate->header_left_logo) {
                Storage::disk('public')->delete($certificate->header_left_logo);
            }
            $data['header_left_logo'] = $request->file('header_left_logo')->store('certificates/logos', 'public');
            if ($request->has('remove_logo_bg_auto')) {
                $this->removeWhiteBackground($data['header_left_logo']);
            }
        }
        if ($request->hasFile('header_right_logo')) {
            if ($certificate->header_right_logo) {
                Storage::disk('public')->delete($certificate->header_right_logo);
            }
            $data['header_right_logo'] = $request->file('header_right_logo')->store('certificates/logos', 'public');
            if ($request->has('remove_logo_bg_auto')) {
                $this->removeWhiteBackground($data['header_right_logo']);
            }
        }

        $certificate->update($data);

        // Sync Roles
        $certificate->roles()->sync($validated['roles']);

        // Sync Structure Materi
        $certificate->structures()->delete();
        if (!empty($validated['materi'])) {
            foreach ($validated['materi'] as $index => $item) {
                if (!empty($item['name'])) {
                    GoogleCertificateStructure::create([
                        'id'              => (string) Str::uuid(),
                        'certificate_id'  => $certificate->id,
                        'sort_order'      => $index + 1,
                        'materi_name'     => $item['name'],
                        'time_allocation' => $item['hours'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil diperbarui!');
    }

    /**
     * Hapus sertifikat
     */
    public function destroy($id)
    {
        $certificate = GoogleCertificate::findOrFail($id);

        if ($certificate->background_image) {
            Storage::disk('public')->delete($certificate->background_image);
        }
        if ($certificate->header_left_logo) {
            Storage::disk('public')->delete($certificate->header_left_logo);
        }
        if ($certificate->header_right_logo) {
            Storage::disk('public')->delete($certificate->header_right_logo);
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil dihapus!');
    }

    /**
     * Preview sertifikat oleh Superadmin
     */
    public function preview($id)
    {
        $certificate = GoogleCertificate::with(['signer1Employee', 'signer2Employee', 'structures'])->findOrFail($id);
        $user        = auth()->user();

        return view('certificates.template', compact('certificate', 'user'));
    }

    /**
     * Helper PHP GD untuk menghapus latar belakang putih (menjadi transparan)
     */
    private function removeWhiteBackground(string $relativeStoragePath): void
    {
        $fullPath = storage_path('app/public/' . $relativeStoragePath);
        if (!file_exists($fullPath)) {
            return;
        }

        $image = @imagecreatefromstring(file_get_contents($fullPath));
        if (!$image) {
            return;
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        $transparentImage = imagecreatetruecolor($width, $height);
        imagealphablending($transparentImage, false);
        imagesavealpha($transparentImage, true);

        $transparentColor = imagecolorallocatealpha($transparentImage, 255, 255, 255, 127);
        imagefill($transparentImage, 0, 0, $transparentColor);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r   = ($rgb >> 16) & 0xFF;
                $g   = ($rgb >> 8) & 0xFF;
                $b   = $rgb & 0xFF;

                // Jika piksel adalah warna putih / putih pucat (RGB >= 235)
                if ($r >= 235 && $g >= 235 && $b >= 235) {
                    imagesetpixel($transparentImage, $x, $y, $transparentColor);
                } else {
                    $alpha = ($rgb >> 24) & 0x7F;
                    $color = imagecolorallocatealpha($transparentImage, $r, $g, $b, $alpha);
                    imagesetpixel($transparentImage, $x, $y, $color);
                }
            }
        }

        imagepng($transparentImage, $fullPath);
        imagedestroy($image);
        imagedestroy($transparentImage);
    }
}
