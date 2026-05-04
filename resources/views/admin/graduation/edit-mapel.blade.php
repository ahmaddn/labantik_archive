@extends('layouts.app')
@php $hide_global_alerts = true; @endphp
@section('title', 'Edit Mapel')
@section('page-title', 'Edit Mapel')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Edit Mapel</h1>
                <p class="text-gray-500 text-sm mt-1">Ubah informasi mata pelajaran.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Form Edit Mapel</h2>
            </div>

            <form method="POST" action="{{ route('admin.graduation.updateMapel', $mapel->uuid) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Kelas --}}
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select id="class_id" name="class_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('class_id', $mapel->class_id) == $class->id)>
                                {{ $class->academic_level }} {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jurusan/Expertise --}}
                <div id="expertise-container">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jurusan/Konsentrasi Keahlian
                    </label>
                    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 overflow-hidden">
                        @foreach ($expertise as $exp)
                            <label
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="expertise_ids[]" value="{{ $exp->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    {{ in_array($exp->id, old('expertise_ids', $selectedExpertiseIds ?? [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $exp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('expertise_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1.5">Untuk tipe <strong>Jurusan</strong>, pilih minimal satu jurusan.
                    </p>
                </div>

                {{-- Nama Mapel --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Mapel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        placeholder="Contoh: Matematika, Bahasa Indonesia, dll" value="{{ old('name', $mapel->name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Has NA Setting --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative inline-flex h-6 w-11 items-center">
                            <input type="checkbox" name="has_na" value="1" @checked(old('has_na', $mapel->has_na))
                                class="peer sr-only">
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-gray-800">Aktifkan Nilai Akhir (NA)</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Jika diaktifkan, mapel ini akan menyertakan kolom Nilai Akhir (NA) di transkrip dan surat kelulusan.</span>
                        </div>
                    </label>
                </div>

                {{-- Tipe Mapel --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Mapel <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="umum" @selected(old('type', $mapel->type) == 'umum')>Umum</option>
                        <option value="jurusan" @selected(old('type', $mapel->type) == 'jurusan')>Jurusan</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Section --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">ID Mapel</p>
                            <p class="text-xs text-blue-700 mt-1 font-mono">{{ $mapel->uuid }}</p>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Perbarui Mapel
                    </button>
                    <a href="{{ route('admin.graduation.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                        Batal
                    </a>

                    {{-- ✅ Satu onclick saja: konfirmasi dulu, baru submit form delete --}}
                    <button type="button" id="btnHapusMapel"
                        class="ml-auto inline-flex items-center gap-2 px-6 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-colors border border-red-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </form>
        </div>

        {{-- Form DELETE di luar form EDIT --}}
        <form id="form-delete-mapel" method="POST" action="{{ route('admin.graduation.destroyMapel', $mapel->uuid) }}"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    html: `
                        <ul class="text-left text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                });
            @endif

            // Hapus mapel
            document.getElementById('btnHapusMapel').addEventListener('click', function() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Hapus mapel "{{ addslashes($mapel->name) }}"? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-delete-mapel').submit();
                    }
                });
            });

            // Toggle jurusan berdasarkan tipe
            var typeSelect = document.getElementById('type');
            var expertiseContainer = document.getElementById('expertise-container');

            function toggleExpertise() {
                expertiseContainer.style.display = typeSelect.value === 'jurusan' ? 'block' : 'none';
            }

            typeSelect.addEventListener('change', toggleExpertise);
            toggleExpertise();

        });
    </script>
@endsection
