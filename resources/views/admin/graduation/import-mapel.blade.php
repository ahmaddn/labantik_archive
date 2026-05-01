@extends('layouts.app')
@section('title', 'Import Mapel')
@section('page-title', 'Import Mapel')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Import Mapel</h1>
                <p class="text-gray-500 text-sm mt-1">Import data mapel dari file CSV atau Excel.</p>
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

        {{-- Tabs --}}
        <div class="mb-6">
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
                <button type="button" id="tab-auto-btn" onclick="switchTab('auto')"
                    class="tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-150 bg-white text-gray-900 shadow-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Import Otomatis
                    </span>
                </button>
                <button type="button" id="tab-manual-btn" onclick="switchTab('manual')"
                    class="tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-150 text-gray-500 hover:text-gray-700">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Import + Pilih Manual
                    </span>
                </button>
            </div>
        </div>

        {{-- ===================== TAB OTOMATIS ===================== --}}
        <div id="tab-auto" class="tab-panel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-base font-semibold text-gray-800">Import Otomatis</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Kelas & jurusan dibaca dari file Excel. Tipe
                                <strong>umum</strong> akan diterapkan ke semua kelas & jurusan.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.graduation.importMapelAuto') }}"
                            enctype="multipart/form-data" class="p-6 space-y-5">
                            @csrf

                            {{-- File Upload --}}
                            <div>
                                <label for="file_auto" class="block text-sm font-medium text-gray-700 mb-2">
                                    File CSV/Excel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="file" id="file_auto" name="file" required accept=".csv,.xlsx,.xls"
                                        class="hidden">
                                    <label for="file_auto"
                                        class="drop-zone block w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 transition-colors bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-sm font-medium text-gray-700">Klik atau drag & drop file</p>
                                            <p class="text-xs text-gray-500 mt-1">CSV, XLSX, atau XLS (Max 10MB)</p>
                                        </div>
                                    </label>
                                    <p id="fileName_auto" class="text-sm text-gray-600 mt-2"></p>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6" />
                                    </svg>
                                    Import Otomatis
                                </button>
                                <a href="{{ route('admin.graduation.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Sidebar --}}
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-2">Format File (Otomatis)</h3>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li><strong>Kolom yang diperlukan:</strong></li>
                                    <li>1. <code class="bg-blue-100 px-1 rounded">name</code> — Nama Mapel</li>
                                    <li>2. <code class="bg-blue-100 px-1 rounded">type</code> — <em>umum</em> /
                                        <em>jurusan</em>
                                    </li>
                                    <li>3. <code class="bg-blue-100 px-1 rounded">expertise_name</code> — Nama jurusan
                                        (wajib jika tipe <em>jurusan</em>)</li>
                                </ul>
                                <p class="text-xs text-blue-700 mt-3">Tipe <strong>umum</strong> tidak perlu
                                    <code>expertise_name</code> dan akan diaplikasikan ke <strong>semua kelas &
                                        jurusan</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
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
                                            <tr class="text-gray-500 border-b border-gray-100">
                                                <th class="pr-3 py-1 text-left font-medium">name</th>
                                                <th class="pr-3 py-1 text-left font-medium">type</th>
                                                <th class="py-1 text-left font-medium">expertise_name</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600">
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Matematika</td>
                                                <td class="pr-3 py-1">jurusan</td>
                                                <td class="py-1">Rekayasa Perangkat Lunak (RPL)</td>
                                            </tr>
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Bahasa Indonesia</td>
                                                <td class="pr-3 py-1">umum</td>
                                                <td class="py-1 text-gray-400 italic">(kosong)</td>
                                            </tr>
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Bahasa Inggris</td>
                                                <td class="pr-3 py-1">umum</td>
                                                <td class="py-1 text-gray-400 italic">(kosong)</td>
                                            </tr>
                                            <tr>
                                                <td class="pr-3 py-1">Dasar Akuntansi</td>
                                                <td class="pr-3 py-1">jurusan</td>
                                                <td class="py-1">Akuntansi (AK)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== TAB MANUAL ===================== --}}
        <div id="tab-manual" class="tab-panel hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-base font-semibold text-gray-800">Import + Pilih Manual</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih kelas & jurusan secara manual. File hanya perlu
                                kolom <strong>name</strong> dan <strong>type</strong>.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.graduation.importMapel') }}"
                            enctype="multipart/form-data" class="p-6 space-y-5">
                            @csrf

                            {{-- File Upload --}}
                            <div>
                                <label for="file_manual" class="block text-sm font-medium text-gray-700 mb-2">
                                    File CSV/Excel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="file" id="file_manual" name="file" required
                                        accept=".csv,.xlsx,.xls" class="hidden">
                                    <label for="file_manual"
                                        class="drop-zone block w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 transition-colors bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-sm font-medium text-gray-700">Klik atau drag & drop file</p>
                                            <p class="text-xs text-gray-500 mt-1">CSV, XLSX, atau XLS (Max 10MB)</p>
                                        </div>
                                    </label>
                                    <p id="fileName_manual" class="text-sm text-gray-600 mt-2"></p>
                                </div>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kelas (Multi-select) --}}
                            <div>
                                <label for="class_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kelas <span class="text-red-500">*</span>
                                </label>
                                <select id="class_ids" name="class_ids[]" multiple required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_ids') border-red-500 @enderror">
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" @selected(in_array($class->id, old('class_ids', [])))>
                                            {{ $class->academic_level }} {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-gray-500 text-xs mt-1">Ctrl+Click untuk pilih lebih dari satu kelas</p>
                                @error('class_ids')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jurusan (Multi-select) --}}
                            <div>
                                <label for="expertise_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jurusan <span class="text-red-500">*</span>
                                </label>
                                <select id="expertise_ids" name="expertise_ids[]" multiple required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expertise_ids') border-red-500 @enderror">
                                    @foreach ($expertise as $exp)
                                        <option value="{{ $exp->id }}" @selected(in_array($exp->id, old('expertise_ids', [])))>
                                            {{ $exp->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-gray-500 text-xs mt-1">Ctrl+Click untuk pilih lebih dari satu jurusan</p>
                                @error('expertise_ids')
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
                                    Import Mapel
                                </button>
                                <a href="{{ route('admin.graduation.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Sidebar --}}
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-2">Format File (Manual)</h3>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li><strong>Kolom yang diperlukan:</strong></li>
                                    <li>1. <code class="bg-blue-100 px-1 rounded">name</code> — Nama Mapel</li>
                                    <li>2. <code class="bg-blue-100 px-1 rounded">type</code> — <em>umum</em> /
                                        <em>jurusan</em>
                                    </li>
                                </ul>
                                <p class="text-xs text-blue-700 mt-3">Kelas & Jurusan dipilih dari form. Semua kombinasi
                                    yang dipilih akan mendapat mapel dari file ini.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
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
                                            <tr class="text-gray-500 border-b border-gray-100">
                                                <th class="pr-3 py-1 text-left font-medium">name</th>
                                                <th class="py-1 text-left font-medium">type</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600">
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Matematika</td>
                                                <td class="py-1">jurusan</td>
                                            </tr>
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Bahasa Indonesia</td>
                                                <td class="py-1">umum</td>
                                            </tr>
                                            <tr class="border-b border-gray-50">
                                                <td class="pr-3 py-1">Bahasa Inggris</td>
                                                <td class="py-1">umum</td>
                                            </tr>
                                            <tr>
                                                <td class="pr-3 py-1">Dasar Akuntansi</td>
                                                <td class="py-1">jurusan</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ---- Tab switching ----
        function switchTab(tab) {
            const panels = document.querySelectorAll('.tab-panel');
            panels.forEach(p => p.classList.add('hidden'));

            document.getElementById('tab-' + tab).classList.remove('hidden');

            // Reset button styles
            document.getElementById('tab-auto-btn').className =
                'tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-150 text-gray-500 hover:text-gray-700';
            document.getElementById('tab-manual-btn').className =
                'tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-150 text-gray-500 hover:text-gray-700';

            // Set active
            document.getElementById('tab-' + tab + '-btn').className =
                'tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-150 bg-white text-gray-900 shadow-sm';

            localStorage.setItem('importMapelTab', tab);
        }

        // Restore last tab
        const savedTab = localStorage.getItem('importMapelTab') || 'auto';
        switchTab(savedTab);

        // ---- File upload display & drag-drop (reusable) ----
        function initFileInput(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);
            const dropZone = input.nextElementSibling; // the <label>

            input.addEventListener('change', function(e) {
                display.textContent = e.target.files.length > 0 ?
                    '✓ File dipilih: ' + e.target.files[0].name : '';
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => {
                dropZone.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            ['dragenter', 'dragover'].forEach(ev => {
                dropZone.addEventListener(ev, () => {
                    dropZone.classList.add('border-blue-400', 'bg-blue-50');
                });
            });

            ['dragleave', 'drop'].forEach(ev => {
                dropZone.addEventListener(ev, () => {
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                });
            });

            dropZone.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                input.files = files;
                input.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });
        }

        initFileInput('file_auto', 'fileName_auto');
        initFileInput('file_manual', 'fileName_manual');
    </script>
@endsection
