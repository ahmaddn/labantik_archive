@extends('layouts.app')
@section('title', 'Tambah Dokumen')
@section('page-title', 'Upload Dokumen')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 mb-6 text-sm text-gray-500">
            <a href="{{ route('drive.index') }}" class="hover:text-[#1b84ff] transition-colors font-medium">My Drive</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-800 font-semibold">Upload Dokumen</span>
        </div>

        {{-- Info / Warning banners --}}
        @if (!$isConnected)
            <div class="mb-5 flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p><strong>Google Drive belum terhubung.</strong> Hubungi Super Admin untuk menghubungkan.</p>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-[#1b84ff] to-[#0ea5e9] px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base">Tambah Dokumen Baru</h2>
                        @if(isset($remainingBytes))
                            @php $remMb = round($remainingBytes / 1024 / 1024, 2); @endphp
                            <p class="text-blue-100 text-xs mt-0.5">Sisa kuota: {{ $remMb }} MB</p>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('drive.store') }}" enctype="multipart/form-data" id="uploadForm" class="p-6 space-y-5">
                @csrf

                {{-- Nama Dokumen --}}
                <div>
                    <label for="document_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="document_name"
                           name="document_name"
                           value="{{ old('document_name') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                  focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                  @error('document_name') border-red-400 @enderror"
                           placeholder="Contoh: Ijazah SMA, Sertifikat Lomba, KTP"
                           required>
                    @error('document_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        File PDF / Gambar <span class="text-red-500">*</span>
                    </label>
                    <div id="dropZone"
                         class="border-2 border-dashed border-gray-300 hover:border-[#1b84ff] rounded-xl p-8 text-center cursor-pointer transition-colors
                                @error('file') border-red-400 @enderror"
                         onclick="document.getElementById('file').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-[#1b84ff]','bg-blue-50')"
                         ondragleave="this.classList.remove('border-[#1b84ff]','bg-blue-50')"
                         ondrop="handleDrop(event)">
                        <div id="dropIcon" class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm font-medium" id="fileLabel">Klik atau seret file ke sini</p>
                            <p class="text-gray-400 text-xs">PDF atau gambar (JPG, PNG) · Maks. 1 MB</p>
                        </div>
                    </div>
                    <input type="file" id="file" name="file"
                           accept=".pdf,application/pdf,.jpg,.jpeg,.png,image/*"
                           class="hidden" onchange="showFileName(this)" required>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p id="clientError" class="text-red-500 text-xs mt-2 hidden"></p>
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="google_category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="google_category_id"
                            name="google_category_id"
                            onchange="toggleExpertiseField()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                   focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                   @error('google_category_id') border-red-400 @enderror"
                            required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    data-slug="{{ $category->slug }}"
                                    {{ old('google_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('google_category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keahlian (kondisional) --}}
                <div id="expertiseField" class="hidden">
                    <label for="expertise_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keahlian <span class="text-red-500">*</span>
                    </label>
                    <select id="expertise_id" name="expertise_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                   focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                   @error('expertise_id') border-red-400 @enderror">
                        <option value="">— Pilih Keahlian —</option>
                        @foreach($expertises as $expertise)
                            <option value="{{ $expertise->id }}" {{ old('expertise_id') == $expertise->id ? 'selected' : '' }}>
                                {{ $expertise->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expertise_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('drive.index') }}"
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl
                                   transition-colors text-sm shadow-sm shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$isConnected || (isset($remainingBytes) && $remainingBytes <= 0) ? 'disabled' : '' }}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span id="submitBtnText">Upload Dokumen</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showFileName(input) {
            if (input.files[0]) {
                document.getElementById('fileLabel').textContent = '✓ ' + input.files[0].name;
                document.getElementById('dropZone').classList.add('border-green-400', 'bg-green-50');
                document.getElementById('dropZone').classList.remove('border-gray-300');
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const dt = event.dataTransfer;
            if (dt.files.length) {
                document.getElementById('file').files = dt.files;
                showFileName(document.getElementById('file'));
                document.getElementById('file').dispatchEvent(new Event('change'));
            }
        }

        function toggleExpertiseField() {
            const sel  = document.getElementById('google_category_id');
            const slug = sel.options[sel.selectedIndex].getAttribute('data-slug');
            const ef   = document.getElementById('expertiseField');
            const es   = document.getElementById('expertise_id');
            if (slug === 'prestasi') {
                ef.classList.remove('hidden'); es.required = true;
            } else {
                ef.classList.add('hidden'); es.required = false; es.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleExpertiseField);

        document.getElementById('uploadForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitBtnText').textContent = 'Mengupload…';
        });

        (function() {
            const fileInput     = document.getElementById('file');
            const submitBtn     = document.getElementById('submitBtn');
            const clientError   = document.getElementById('clientError');
            const remainingBytes = Number({{ $remainingBytes ?? 0 }});
            const perFileLimit   = 1 * 1024 * 1024;

            function setError(msg) { clientError.textContent = msg; clientError.classList.remove('hidden'); }
            function clearError()  { clientError.textContent = '';  clientError.classList.add('hidden'); }

            fileInput.addEventListener('change', function() {
                clearError();
                const f = fileInput.files && fileInput.files[0];
                if (!f) return;
                const isPdf  = f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf');
                const isImg  = f.type.startsWith('image/');
                if (!isPdf && !isImg) { setError('Hanya file PDF atau gambar yang diperbolehkan.'); submitBtn.disabled = true; return; }
                if (f.size > perFileLimit) { setError('Ukuran file tidak boleh lebih dari 1 MB.'); submitBtn.disabled = true; return; }
                if (f.size > remainingBytes) { setError('Sisa kuota tidak cukup. Sisa: ' + (remainingBytes / (1024*1024)).toFixed(2) + ' MB.'); submitBtn.disabled = true; return; }
                submitBtn.disabled = false;
            });
        })();
    </script>
@endsection
