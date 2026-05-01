@extends('layouts.app')
@section('title', 'Manajemen Kelulusan')
@section('page-title', 'Kelulusan')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

            {{-- Title --}}
            <div class="shrink-0">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Manajemen Kelulusan</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola data mapel dan nilai kelulusan siswa.</p>
            </div>

            {{-- Import Error Alert --}}
            @if (session('import_errors'))
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 w-full sm:max-w-sm">
                    <p class="font-medium text-red-800 mb-2 text-sm">Detail Error:</p>
                    <ul class="text-xs text-red-700 space-y-1 max-h-40 overflow-y-auto">
                        @foreach (session('import_errors') as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:justify-end gap-2">

                {{-- ── Mapel Group (Biru) ── --}}
                <a href="{{ route('admin.graduation.showImportMapel') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2.5 sm:px-5
               bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200
               font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm w-full sm:w-auto">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Import Mapel</span>
                </a>

                <a href="{{ route('admin.graduation.createMapel') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2.5 sm:px-5
               bg-blue-600 hover:bg-blue-700 text-white
               font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm shadow-blue-200 w-full sm:w-auto">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Mapel</span>
                </a>

                {{-- ── Utility (Abu Netral) ── --}}
                <button onclick="openDownloadModal()"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2.5 sm:px-5
               bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200
               font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm w-full sm:w-auto">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Download Template</span>
                </button>

                {{-- ── Nilai Group (Violet) ── --}}
                <a href="{{ route('admin.graduation.showImportNilai') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2.5 sm:px-5
               bg-violet-50 hover:bg-violet-100 text-violet-600 border border-violet-200
               font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm w-full sm:w-auto">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                    </svg>
                    <span>Import Nilai</span>
                </a>

                <a href="{{ route('admin.graduation.create') }}"
                    class="col-span-2 sm:col-auto inline-flex items-center justify-center gap-2 px-3 py-2.5 sm:px-5
               bg-violet-600 hover:bg-violet-700 text-white
               font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm shadow-violet-200 w-full sm:w-auto">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tambah Nilai</span>
                </a>

            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                </svg>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $totalMapels }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Mapel</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $totalGraduations }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Data Kelulusan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-500 font-medium">Siswa Lulus</p>
            </div>
        </div>
        <a href="{{ route('admin.graduation.downloaders') }}" class="text-left w-full bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center gap-3 sm:gap-4 hover:border-orange-300 hover:shadow-md transition-all duration-200 cursor-pointer">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
            </div>
            <div>
                <p class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $totalDownloaders }}</p>
                <p class="text-xs text-gray-500 font-medium">Siswa Yang Mendownload Dokumen</p>
            </div>
            <div class="ml-auto">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- Daftar Mapel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm sm:text-base font-semibold text-gray-800">Daftar Mapel</h2>
        </div>
        <div class="p-4 sm:p-6">
            @include('admin._partials.mapel_list_table', [
                'routeEdit' => 'admin.graduation.editMapel',
                'routeDelete' => 'admin.graduation.destroyMapel',
                'routeDeleteBulk' => 'admin.graduation.destroyMapelBulk', // ← tambah ini
            ])
        </div>
    </div>

    {{-- DAFTAR KELULUSAN SISWA --}}
    <div class="mt-6 sm:mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm sm:text-base font-semibold text-gray-800">Daftar Siswa Lulus</h2>
        </div>

        {{-- Apply template section --}}
        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0 {{ $allHaveLetter ? 'text-green-600' : 'text-blue-600' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if ($allHaveLetter)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    @endif
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Terapkan Template ke Semua Kelulusan</p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        @if ($allHaveLetter)
                            Semua data kelulusan sudah memiliki surat yang ditetapkan.
                        @else
                            Pilih template surat, lalu klik tombol untuk mengisi letter_id semua data kelulusan siswa.
                        @endif
                    </p>
                </div>
            </div>

            @if ($allHaveLetter)
                <div
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 text-green-700 text-xs sm:text-sm font-semibold rounded-lg w-full sm:w-auto justify-center sm:justify-start">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Semua data sudah memiliki surat
                </div>
            @else
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                    <select id="templateSelectDropdown"
                        class="flex-1 px-4 py-2.5 border border-blue-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white hover:border-blue-300 transition-colors">
                        <option value="">-- Pilih Template Surat --</option>
                        @foreach ($letters as $letter)
                            <option value="{{ $letter->uuid }}">
                                {{ $letter->letter_number }}
                                ({{ \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('d M Y') }})
                            </option>
                        @endforeach
                    </select>

                    <button onclick="openApplyTemplateModal()" id="applyTemplateBtn"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 text-sm shadow-md hover:shadow-lg opacity-50 cursor-not-allowed"
                        disabled>
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="whitespace-nowrap">Gunakan ke Semua</span>
                    </button>
                </div>
            @endif
        </div>
        <div class="p-4 sm:p-6">
            @include('admin._partials.graduation_list_table', [
                'routeIndex' => 'admin.graduation.index',
                'routeShow' => 'admin.graduation.show',
                'routeDelete' => 'admin.graduation.destroy',
            ])
        </div>
    </div>

    @include('admin._partials.graduation_letter_section', ['letters' => $letters])

    {{-- ===================================================== --}}
    {{-- MODAL: Download Template                              --}}
    {{-- ===================================================== --}}
    <div id="downloadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div
            class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-0 sm:pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDownloadModal()"></div>

            {{-- Slide up on mobile, centered on desktop --}}
            <div
                class="relative inline-block w-full sm:w-auto align-bottom sm:align-middle bg-white rounded-t-2xl sm:rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg">
                {{-- Drag handle (mobile) --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden">
                    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                </div>

                <div class="bg-white px-5 pt-4 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Download Template Kelulusan</h3>
                        <button onclick="closeDownloadModal()"
                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Kelas (Opsional)</label>
                            <select id="classFilter"
                                class="w-full px-4 py-3 sm:py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->academic_level }} {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-sm text-gray-500 bg-blue-50 p-3 rounded-xl">
                            <p class="text-xs">💡 Template akan berisi semua siswa yang sesuai dengan filter kelas dan
                                jurusan yang dipilih.</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-5 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeDownloadModal()"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 px-5 py-3 sm:py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="downloadTemplate()"
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl px-5 py-3 sm:py-2 bg-[#1b84ff] text-sm font-semibold text-white hover:bg-[#1570e0] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDownloadModal() {
            document.getElementById('downloadModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDownloadModal() {
            document.getElementById('downloadModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function downloadTemplate() {
            const classId = document.getElementById('classFilter').value;
            let url = '{{ route('admin.graduation.downloadTemplate') }}';
            const params = new URLSearchParams();
            if (classId) params.append('class_id', classId);
            if (params.toString()) url += '?' + params.toString();
            window.location.href = url;
            closeDownloadModal();
        }

        // Template dropdown enable/disable button
        const templateSelectDropdown = document.getElementById('templateSelectDropdown');
        const applyTemplateBtn = document.getElementById('applyTemplateBtn');

        if (templateSelectDropdown && applyTemplateBtn) {
            templateSelectDropdown.addEventListener('change', function() {
                const has = this.value !== '';
                applyTemplateBtn.disabled = !has;
                applyTemplateBtn.classList.toggle('opacity-50', !has);
                applyTemplateBtn.classList.toggle('cursor-not-allowed', !has);
                applyTemplateBtn.classList.toggle('hover:scale-[1.02]', has);
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDownloadModal();
            }
        });
    </script>
@endsection
