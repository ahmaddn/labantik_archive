<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleGraduationLetter;
use Illuminate\Http\Request;

class GraduationLetterController extends Controller
{
    /**
     * Store a new graduation letter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter_number'            => 'required|string|max:255',
            'transcript_letter_number' => 'nullable|string|max:255',
            'academic_year'            => 'required|string|max:10',
            'headmaster_id'            => 'required|exists:core_users,id',
            'graduation_date'          => 'required|date',
            'statement'                => 'required|string',
            'content'                  => 'required|string',
            'stamp_image'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'signature_image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'letter_number.required'   => 'Nomor surat harus diisi',
            'academic_year.required'   => 'Tahun pelajaran harus diisi',
            'headmaster_id.required'   => 'Kepala sekolah harus dipilih',
            'headmaster_id.exists'     => 'Kepala sekolah tidak valid',
            'graduation_date.required' => 'Tanggal kelulusan harus diisi',
            'graduation_date.date'     => 'Format tanggal tidak valid',
            'statement.required'       => 'Pernyataan kepala sekolah harus diisi',
            'content.required'         => 'Isi/konten surat harus diisi',
            'stamp_image.image'        => 'Stempel harus berupa gambar',
            'signature_image.image'    => 'Tanda tangan harus berupa gambar',
        ]);

        try {
            if ($request->hasFile('stamp_image')) {
                $validated['stamp_image'] = $request->file('stamp_image')->store('graduation', 'public');
            }
            if ($request->hasFile('signature_image')) {
                $validated['signature_image'] = $request->file('signature_image')->store('graduation', 'public');
            }

            GoogleGraduationLetter::create($validated);

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Template surat keterangan lulus berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get a single letter (for edit modal via AJAX)
     */
    public function show($id)
    {
        $letter = GoogleGraduationLetter::findOrFail($id);
        return response()->json($letter);
    }

    /**
     * Update an existing graduation letter
     */
    public function update(Request $request, $id)
    {
        $letter = GoogleGraduationLetter::findOrFail($id);

        $validated = $request->validate([
            'letter_number'            => 'required|string|max:255',
            'transcript_letter_number' => 'nullable|string|max:255',
            'academic_year'            => 'required|string|max:10',
            'headmaster_id'            => 'required|exists:core_users,id',
            'graduation_date'          => 'required|date',
            'statement'                => 'required|string',
            'content'                  => 'required|string',
            'stamp_image'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'signature_image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'letter_number.required'   => 'Nomor surat harus diisi',
            'academic_year.required'   => 'Tahun pelajaran harus diisi',
            'headmaster_id.required'   => 'Kepala sekolah harus dipilih',
            'headmaster_id.exists'     => 'Kepala sekolah tidak valid',
            'graduation_date.required' => 'Tanggal kelulusan harus diisi',
            'graduation_date.date'     => 'Format tanggal tidak valid',
            'statement.required'       => 'Pernyataan kepala sekolah harus diisi',
            'content.required'         => 'Isi/konten surat harus diisi',
            'stamp_image.image'        => 'Stempel harus berupa gambar',
            'signature_image.image'    => 'Tanda tangan harus berupa gambar',
        ]);

        try {
            if ($request->hasFile('stamp_image')) {
                // Delete old image
                if ($letter->stamp_image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($letter->stamp_image);
                }
                $validated['stamp_image'] = $request->file('stamp_image')->store('graduation', 'public');
            }
            if ($request->hasFile('signature_image')) {
                // Delete old image
                if ($letter->signature_image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($letter->signature_image);
                }
                $validated['signature_image'] = $request->file('signature_image')->store('graduation', 'public');
            }

            $letter->update($validated);

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Template surat keterangan lulus berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a graduation letter
     */
    public function destroy($id)
    {
        try {
            $letter = GoogleGraduationLetter::findOrFail($id);
            
            // Delete images
            if ($letter->stamp_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($letter->stamp_image);
            }
            if ($letter->signature_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($letter->signature_image);
            }

            $letter->delete();

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Template surat keterangan lulus berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
}
