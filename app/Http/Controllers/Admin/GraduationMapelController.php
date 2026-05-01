<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoreExpertiseConcentration;
use App\Models\GoogleGraduationMapel;
use App\Models\GoogleMapel;
use App\Models\RefClass;
use App\Models\ExpertiseConcentration;
use Illuminate\Http\Request;

class GraduationMapelController extends Controller
{
    /**
     * Show form tambah mapel baru
     */
    public function create()
    {
        $expertise = ExpertiseConcentration::select(['id', 'name'])->get();

        return view('admin.graduation.create-mapel', compact('expertise'));
    }

    /**
     * Store mapel baru — otomatis diterapkan ke semua kelas 12
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expertise_ids'   => 'nullable|array',
            'expertise_ids.*' => 'exists:core_expertise_concentrations,id',
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:umum,jurusan',
        ]);

        try {
            $allClasses = RefClass::where('academic_level', 12)->get();

            if ($allClasses->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada kelas level 12 di database')
                    ->withInput();
            }

            $successCount = 0;
            $skipCount    = 0;

            if ($validated['type'] === 'umum') {
                foreach ($allClasses as $class) {
                    $exists = GoogleMapel::where('class_id', $class->id)
                        ->where('name', $validated['name'])
                        ->where('type', 'umum')
                        ->whereNull('expertise_id')
                        ->exists();

                    if ($exists) {
                        $skipCount++;
                        continue;
                    }

                    GoogleMapel::create([
                        'class_id'     => $class->id,
                        'expertise_id' => null,
                        'name'         => $validated['name'],
                        'type'         => 'umum',
                    ]);
                    $successCount++;
                }
            } else {
                if (empty($validated['expertise_ids'])) {
                    return redirect()->back()
                        ->with('error', 'Pilih minimal satu jurusan untuk tipe mapel jurusan')
                        ->withInput();
                }

                foreach ($validated['expertise_ids'] as $expId) {
                    $matchedClasses = $allClasses->where('expertise_concentration_id', $expId);

                    foreach ($matchedClasses as $class) {
                        $exists = GoogleMapel::where('class_id', $class->id)
                            ->where('name', $validated['name'])
                            ->where('type', 'jurusan')
                            ->where('expertise_id', $expId)
                            ->exists();

                        if ($exists) {
                            $skipCount++;
                            continue;
                        }

                        GoogleMapel::create([
                            'class_id'     => $class->id,
                            'expertise_id' => $expId,
                            'name'         => $validated['name'],
                            'type'         => 'jurusan',
                        ]);
                        $successCount++;
                    }
                }
            }

            $message = "Mapel '{$validated['name']}' berhasil ditambahkan ke {$successCount} kelas!";
            if ($skipCount > 0) $message .= " ({$skipCount} mapel sudah ada)";

            return redirect()->route('admin.graduation.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan mapel: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form edit mapel
     */
    public function edit($id)
    {
        $mapel     = GoogleMapel::where('uuid', $id)->firstOrFail();
        $classes   = RefClass::where('academic_level', 12)->get();
        $expertise = ExpertiseConcentration::all();

        $selectedExpertiseIds = $mapel->expertise_id ? [$mapel->expertise_id] : [];

        return view('admin.graduation.edit-mapel', compact('mapel', 'classes', 'expertise', 'selectedExpertiseIds'));
    }

    /**
     * Update mapel
     */
    public function update(Request $request, $id)
    {
        $mapel = GoogleMapel::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'class_id'        => 'required|exists:ref_classes,id',
            'expertise_ids'   => 'nullable|array',
            'expertise_ids.*' => 'exists:core_expertise_concentrations,id',
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:umum,jurusan',
        ]);

        try {
            if ($validated['type'] === 'jurusan' && empty($validated['expertise_ids'])) {
                return redirect()->back()
                    ->with('error', 'Pilih minimal satu jurusan untuk tipe mapel jurusan')
                    ->withInput();
            }

            if ($validated['type'] === 'umum') {
                $exists = GoogleMapel::where('uuid', '!=', $id)
                    ->where('class_id', $validated['class_id'])
                    ->where('name', $validated['name'])
                    ->where('type', 'umum')
                    ->whereNull('expertise_id')
                    ->exists();

                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'Mapel dengan nama dan kelas yang sama sudah ada')
                        ->withInput();
                }

                $mapel->update([
                    'class_id'     => $validated['class_id'],
                    'expertise_id' => null,
                    'name'         => $validated['name'],
                    'type'         => 'umum',
                ]);
            } else {
                $firstExpertiseId = $validated['expertise_ids'][0];

                $exists = GoogleMapel::where('uuid', '!=', $id)
                    ->where('class_id', $validated['class_id'])
                    ->where('name', $validated['name'])
                    ->where('type', 'jurusan')
                    ->where('expertise_id', $firstExpertiseId)
                    ->exists();

                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'Mapel dengan nama, kelas, dan jurusan yang sama sudah ada')
                        ->withInput();
                }

                $mapel->update([
                    'class_id'     => $validated['class_id'],
                    'expertise_id' => $firstExpertiseId,
                    'name'         => $validated['name'],
                    'type'         => 'jurusan',
                ]);

                if (count($validated['expertise_ids']) > 1) {
                    for ($i = 1; $i < count($validated['expertise_ids']); $i++) {
                        $expId = $validated['expertise_ids'][$i];

                        $exists = GoogleMapel::where('class_id', $validated['class_id'])
                            ->where('name', $validated['name'])
                            ->where('type', 'jurusan')
                            ->where('expertise_id', $expId)
                            ->exists();

                        if (!$exists) {
                            GoogleMapel::create([
                                'class_id'     => $validated['class_id'],
                                'expertise_id' => $expId,
                                'name'         => $validated['name'],
                                'type'         => 'jurusan',
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('admin.graduation.index')
                ->with('success', 'Mapel berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui mapel: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update urutan & join baris mapel (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'uuid'  => 'required|string',
                'order' => 'required|integer|min:1|max:999',
                'join'  => 'required|integer|min:1|max:10',
            ], [
                'uuid.required'  => 'UUID mapel wajib diisi',
                'order.required' => 'Urutan wajib diisi',
                'order.integer'  => 'Urutan harus berupa angka',
                'order.min'      => 'Urutan minimal 1',
                'join.required'  => 'Join baris wajib diisi',
                'join.integer'   => 'Join baris harus berupa angka',
                'join.min'       => 'Join baris minimal 1',
                'join.max'       => 'Join baris maksimal 10',
            ]);

            $mapel = GoogleMapel::where('uuid', $validated['uuid'])->firstOrFail();

            $mapel->update([
                'order' => $validated['order'],
                'join'  => $validated['join'],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Urutan & join mapel '{$mapel->name}' berhasil diperbarui.",
                'data'    => [
                    'uuid'  => $mapel->uuid,
                    'order' => $mapel->order,
                    'join'  => $mapel->join,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('updateMapelOrder error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete mapel (single)
     */
    public function destroy($id)
    {
        try {
            $mapel = GoogleMapel::where('uuid', $id)->firstOrFail();

            \DB::beginTransaction();
            GoogleGraduationMapel::where('mapel_id', $id)->delete();
            $mapel->delete();
            \DB::commit();

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Mapel berhasil dihapus!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus mapel: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete mapel — menerima array UUID via POST
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'uuids'   => 'required|array|min:1',
            'uuids.*' => 'required|string',
        ]);

        try {
            \DB::beginTransaction();

            // Coba cari by uuid dulu, fallback ke id jika tidak ketemu
            $mapels = GoogleMapel::whereIn('uuid', $validated['uuids'])->get(); 

            if ($mapels->isEmpty()) {
                // Fallback: mungkin yang dikirim adalah integer id
                $mapels = GoogleMapel::whereIn('id', $validated['uuids'])->get();
            }

            if ($mapels->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada mapel yang ditemukan.');
            }

            $deletedCount = 0;
            foreach ($mapels as $mapel) {
                // Hapus relasi — coba by uuid dulu, lalu by id
                GoogleGraduationMapel::where('mapel_id', $mapel->uuid)
                    ->orWhere('mapel_id', $mapel->id)
                    ->delete();

                $mapel->delete();
                $deletedCount++;
            }

            \DB::commit();

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', "{$deletedCount} mapel berhasil dihapus!");
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus mapel: ' . $e->getMessage());
        }
    }
}
