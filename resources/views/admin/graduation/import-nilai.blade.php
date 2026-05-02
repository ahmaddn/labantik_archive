@extends('layouts.app')
@section('title', 'Import Nilai Kelulusan')
@section('page-title', 'Import Nilai Kelulusan')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Import Nilai Kelulusan</h1>
                <p class="text-gray-500 text-sm mt-1">Import data nilai siswa dari file CSV atau Excel.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    @if (session('import_errors') && count(session('import_errors')) > 0)
                        <details class="mt-2">
                            <summary class="text-sm text-green-700 cursor-pointer font-medium">Lihat detail error
                                ({{ count(session('import_errors')) }})</summary>
                            <ul class="text-xs text-green-700 mt-2 space-y-1 pl-4">
                                @foreach (session('import_errors') as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </div>
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-800 font-medium">Terjadi kesalahan saat import</p>
                    <ul class="text-xs text-red-700 mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Form Import Nilai</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.graduation.importNilai') }}" enctype="multipart/form-data"
                        class="p-6 space-y-5">
                        @csrf

                        {{-- File Upload --}}
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                File CSV/Excel <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" id="file" name="file" required accept=".csv,.xlsx,.xls"
                                    class="hidden" @error('file') aria-invalid="true" @enderror>
                                <label for="file"
                                    class="block w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 transition-colors bg-gray-50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-700">Klik untuk memilih file atau drag &
                                            drop</p>
                                        <p class="text-xs text-gray-500 mt-1">CSV, XLSX, atau XLS (Max 10MB)</p>
                                    </div>
                                </label>
                                <p id="fileName" class="text-sm text-gray-600 mt-2"></p>
                            </div>
                            @error('file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                                </svg>
                                Import Nilai
                            </button>
                            <a href="{{ route('admin.graduation.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Template Info --}}
            <div>
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5 mb-5">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-blue-900 mb-2">Format File Import</h3>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li><strong>Kolom yang diperlukan:</strong></li>
                                <li>• NIS</li>
                                <li>• Id Mapel</li>
                                <li>• NA (Nilai Akhir)</li>
                                <li>• S1 - S6 (Opsional untuk Transkrip)</li>
                                <li>• NR (Opsional untuk Transkrip)</li>
                            </ul>
                            <p class="text-xs text-blue-700 mt-3"><strong>Catatan:</strong> File harus memiliki minimal 3
                                kolom dengan header NIS, Id Mapel, dan Nilai. Anda dapat menggunakan template yang
                                di-generate
                                dari fitur download template.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-2xl border border-green-200 p-5 overflow-x-auto">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-green-900 mb-2">Contoh Data CSV</h3>
                            <div class="bg-white rounded text-xs p-2 font-mono text-gray-700 overflow-x-auto">
                                <table class="min-w-max border-collapse">
                                    <thead>
                                        <tr class="text-gray-500 border-b border-gray-100 uppercase tracking-tighter">
                                            <th class="pr-3 py-1 text-left font-bold text-[10px]">Nama Siswa</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px]">Kelas</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px]">Id Mapel</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px] text-blue-600">S1</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px] text-blue-600">...</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px] text-blue-600">S6</th>
                                            <th class="pr-3 py-1 text-left font-bold text-[10px] text-indigo-600">NA</th>
                                            <th class="py-1 text-left font-bold text-[10px]">NIS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr class="border-b border-gray-50">
                                            <td class="pr-3 py-1">AA PAHRUROJI</td>
                                            <td class="pr-3 py-1">12 TSM 2</td>
                                            <td class="pr-3 py-1 text-blue-500">019dd78b...</td>
                                            <td class="pr-3 py-1">85</td>
                                            <td class="pr-3 py-1">...</td>
                                            <td class="pr-3 py-1">88</td>
                                            <td class="pr-3 py-1 font-bold text-indigo-600">87.5</td>
                                            <td class="py-1">25262189</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-3 py-1">AA PAHRUROJI</td>
                                            <td class="pr-3 py-1">12 TSM 2</td>
                                            <td class="pr-3 py-1 text-blue-500">019dd78b...</td>
                                            <td class="pr-3 py-1">80</td>
                                            <td class="pr-3 py-1">...</td>
                                            <td class="pr-3 py-1">82</td>
                                            <td class="pr-3 py-1 font-bold text-indigo-600">81.0</td>
                                            <td class="py-1">25262189</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 mt-5">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4v2m0 4v2m0 4v2M8 8h.01M8 12h.01M8 16h.01M8 20h.01" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-amber-900 mb-2">Panduan Penggunaan</h3>
                            <ul class="text-xs text-amber-800 space-y-1">
                                <li>• Hanya kolom NIS, Id Mapel, dan Nilai yang diperlukan</li>
                                <li>• Nilai harus berupa angka antara 0-100</li>
                                <li>• NIS harus sesuai dengan data siswa di sistem</li>
                                <li>• Id Mapel harus sesuai dengan UUID mapel di sistem</li>
                                <li>• Gunakan template dari fitur "Download Template"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameDisplay = document.getElementById('fileName');
            if (fileName) {
                fileNameDisplay.textContent = '✓ File dipilih: ' + fileName;
                fileNameDisplay.classList.add('text-green-600', 'font-medium');
            } else {
                fileNameDisplay.textContent = '';
            }
        });
    </script>
@endsection
