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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Form Import Mapel</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.graduation.importMapel') }}" enctype="multipart/form-data"
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

                        {{-- Kelas (Multi-select) --}}
                        <div>
                            <label for="class_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select id="class_ids" name="class_ids[]" multiple required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_ids') border-red-500 @enderror">
                                <option value="">-- Pilih Kelas (bisa pilih lebih dari satu) --</option>
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
                                <option value="">-- Pilih Jurusan (bisa pilih lebih dari satu) --</option>
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
                                <li>1. name (Nama Mapel)</li>
                                <li>2. type (umum/jurusan)</li>
                            </ul>
                            <p class="text-xs text-blue-700 mt-3"><strong>Catatan:</strong> Kelas dan Jurusan dipilih dari
                                form di atas. Semua kombinasi kelas dan jurusan yang dipilih akan mendapat mapel dari file
                                ini.</p>
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
                        <div>
                            <h3 class="font-semibold text-green-900 mb-2">Contoh Data CSV</h3>
                            <div class="bg-white rounded text-xs p-2 font-mono text-gray-700 overflow-x-auto">
                                <pre>name,type
Matematika,jurusan
Bahasa Indonesia,umum
Bahasa Inggris,umum
Dasar Akuntansi,jurusan</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle file upload display
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('fileName');

        fileInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                fileNameDisplay.textContent = '✓ File dipilih: ' + fileName;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        // Handle drag & drop
        const dropZone = fileInput.parentElement.parentElement;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;

            // Trigger change event
            const event = new Event('change', {
                bubbles: true
            });
            fileInput.dispatchEvent(event);
        }
    </script>
@endsection
