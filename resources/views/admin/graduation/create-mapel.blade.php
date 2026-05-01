@extends('layouts.app')
@section('title', 'Tambah Mapel')
@section('page-title', 'Tambah Mapel')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Tambah Mapel</h1>
                <p class="text-gray-500 text-sm mt-1">Tambahkan mata pelajaran baru untuk kelulusan siswa.</p>
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
                <h2 class="text-base font-semibold text-gray-800">Form Tambah Mapel</h2>
            </div>

            <form method="POST" action="{{ route('admin.graduation.storeMapel') }}" class="p-6 space-y-5">
                @csrf

                {{-- Tipe Mapel --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Mapel <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="umum" @selected(old('type') == 'umum')>Umum (Semua Jurusan)</option>
                        <option value="jurusan" @selected(old('type') == 'jurusan')>Jurusan (Spesifik per Jurusan)</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jurusan/Expertise (Conditional) --}}
                <div id="expertise-container" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jurusan/Konsentrasi Keahlian <span class="text-red-500">*</span>
                    </label>
                    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 overflow-hidden">
                        @foreach ($expertise as $exp)
                            <label
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="expertise_ids[]" value="{{ $exp->id }}"
                                    id="expertise_{{ $exp->id }}"
                                    class="expertise-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    {{ in_array($exp->id, old('expertise_ids', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $exp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('expertise_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1.5">Pilih satu atau lebih jurusan yang akan mendapat mapel ini.</p>
                </div>

                {{-- Nama Mapel --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Mapel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        placeholder="Contoh: Matematika, Bahasa Indonesia, dll" value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800"><strong>Catatan:</strong> Mapel akan otomatis ditambahkan ke semua
                        kelas 12.</p>
                    <ul class="text-sm text-blue-700 list-disc list-inside mt-2">
                        <li>Tipe <strong>Umum</strong>: Diterapkan ke semua kelas 12</li>
                        <li>Tipe <strong>Jurusan</strong>: Diterapkan hanya ke kelas dengan jurusan yang dipilih</li>
                    </ul>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Simpan Mapel
                    </button>
                    <a href="{{ route('admin.graduation.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- GANTI script lama dengan ini --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const expertiseContainer = document.getElementById('expertise-container');

            function toggleExpertiseField() {
                if (typeSelect.value === 'jurusan') {
                    expertiseContainer.style.display = 'block';
                } else {
                    expertiseContainer.style.display = 'none';
                    // Reset semua checkbox jurusan saat tipe diubah ke umum
                    expertiseContainer.querySelectorAll('.expertise-checkbox').forEach(cb => {
                        cb.checked = false;
                    });
                }
            }

            typeSelect.addEventListener('change', toggleExpertiseField);
            toggleExpertiseField(); // jalankan saat load (untuk old value)
        });
    </script>
@endsection
