@extends('layouts.app')
@section('title', 'Tambah Dokumen')
@section('page-title', 'Upload Dokumen')

@section('content')
    <div class="mx-auto max-w-2xl">

        {{-- Breadcrumb --}}
        <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('drive.index') }}" class="font-medium transition-colors hover:text-[#1b84ff]">Dokumen Saya</a>
            <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-semibold text-gray-800">Upload Dokumen</span>
        </div>

        {{-- Info / Warning banners --}}
        @if (!$isConnected)
            <div
                class="mb-5 flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <p><strong>Google Drive belum terhubung.</strong> Hubungi Super Admin untuk menghubungkan.</p>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-[#1b84ff] to-[#0ea5e9] px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Tambah Dokumen Baru</h2>
                        @if (isset($remainingBytes))
                            @php $remMb = round($remainingBytes / 1024 / 1024, 2); @endphp
                            <p class="mt-0.5 text-xs text-blue-100">Sisa kuota: {{ $remMb }} MB</p>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('drive.store') }}" enctype="multipart/form-data" id="uploadForm"
                class="space-y-5 p-6">
                @csrf

                {{-- Nama Dokumen --}}
                <div>
                    <label for="document_name" class="mb-2 block text-sm font-semibold text-gray-700">
                        Nama Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="document_name" name="document_name" value="{{ old('document_name') }}"
                        class="@error('document_name') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm transition-colors focus:border-[#1b84ff] focus:ring-2 focus:ring-[#1b84ff]/30"
                        placeholder="Contoh: Ijazah SMA, Sertifikat Lomba, KTP" required>
                    @error('document_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        File PDF / Gambar <span class="text-red-500">*</span>
                    </label>
                    <div id="dropZone"
                        class="@error('file') border-red-400 @enderror cursor-pointer rounded-xl border-2 border-dashed border-gray-300 p-8 text-center transition-colors hover:border-[#1b84ff]"
                        onclick="document.getElementById('file').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-[#1b84ff]','bg-blue-50')"
                        ondragleave="this.classList.remove('border-[#1b84ff]','bg-blue-50')" ondrop="handleDrop(event)">
                        <div id="dropIcon" class="flex flex-col items-center gap-2">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
                                <svg class="h-6 w-6 text-[#1b84ff]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-600" id="fileLabel">Klik atau seret file ke sini</p>
                            <p class="text-xs text-gray-400">PDF atau gambar (JPG, PNG) · Maks. 1 MB</p>
                        </div>
                    </div>
                    <input type="file" id="file" name="file"
                        accept=".pdf,application/pdf,.jpg,.jpeg,.png,image/*" class="hidden" onchange="showFileName(this)"
                        required>
                    @error('file')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p id="clientError" class="mt-2 hidden text-xs text-red-500"></p>
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="google_category_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="google_category_id" name="google_category_id"
                        onchange="updateSubCategories(); toggleExpertiseField()"
                        class="@error('google_category_id') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm transition-colors focus:border-[#1b84ff] focus:ring-2 focus:ring-[#1b84ff]/30"
                        required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" data-slug="{{ $category->slug }}"
                                {{ old('google_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('google_category_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hidden input untuk sub-category ID --}}
                <input type="hidden" id="google_drive_sub_category_id" name="google_drive_sub_category_id"
                    value="{{ old('google_drive_sub_category_id') }}">

                {{-- Sub-Kategori Options — label dinamis dari nama sub-kategori di DB --}}
                <div id="subCategoryOptionsField" class="hidden">
                    <label for="sub_category_option" class="mb-2 block text-sm font-semibold text-gray-700">
                        <span id="subCategoryOptionsLabel"></span>
                    </label>
                    <select id="sub_category_option" name="sub_category_option"
                        class="@error('sub_category_option') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm transition-colors focus:border-[#1b84ff] focus:ring-2 focus:ring-[#1b84ff]/30">
                        <option value="">— Pilih Pilihan —</option>
                    </select>
                    @error('sub_category_option')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tahun --}}
                <div>
                    <label for="year" class="mb-2 block text-sm font-semibold text-gray-700">
                        Tahun
                    </label>
                    <select id="year" name="year"
                        class="@error('year') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm transition-colors focus:border-[#1b84ff] focus:ring-2 focus:ring-[#1b84ff]/30">
                        <option value="">— Pilih Tahun (Opsional) —</option>
                        @foreach (array_reverse($yearRange) as $y)
                            <option value="{{ $y }}"
                                {{ old('year') == $y || $y == $currentYear ? 'selected' : '' }}>{{ $y }}
                            </option>
                        @endforeach
                    </select>
                    @error('year')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keahlian (kondisional) --}}
                <div id="expertiseField" class="hidden">
                    <label for="expertise_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Keahlian <span class="text-red-500">*</span>
                    </label>
                    <select id="expertise_id" name="expertise_id"
                        class="@error('expertise_id') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm transition-colors focus:border-[#1b84ff] focus:ring-2 focus:ring-[#1b84ff]/30">
                        <option value="">— Pilih Keahlian —</option>
                        @foreach ($expertises as $expertise)
                            <option value="{{ $expertise->id }}"
                                {{ old('expertise_id') == $expertise->id ? 'selected' : '' }}>
                                {{ $expertise->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expertise_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    <a href="{{ route('drive.index') }}"
                        class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#1b84ff] px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 transition-colors hover:bg-[#1570e0] disabled:cursor-not-allowed disabled:opacity-50"
                        {{ !$isConnected || (isset($remainingBytes) && $remainingBytes <= 0) ? 'disabled' : '' }}>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span id="submitBtnText">Upload Dokumen</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const categoriesData = {!! json_encode(
            $categories->map(
                    fn($c) => [
                        'id' => $c->id,
                        'subCategories' => $c->subCategories->map(
                                fn($sc) => [
                                    'id' => $sc->id,
                                    'name' => $sc->name,
                                    'options' => $sc->options->map(fn($o) => ['id' => $o->id, 'name' => $o->name])->toArray(),
                                ],
                            )->toArray(),
                    ],
                )->toArray(),
        ) !!};

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

        function updateSubCategories() {
            const categoryId = document.getElementById('google_category_id').value;
            const hiddenSubCat = document.getElementById('google_drive_sub_category_id');
            const optionsSelect = document.getElementById('sub_category_option');
            const optionsField = document.getElementById('subCategoryOptionsField');
            const optionsLabel = document.getElementById('subCategoryOptionsLabel');

            // Reset
            hiddenSubCat.value = '';
            optionsSelect.innerHTML = '<option value="">— Pilih Pilihan —</option>';
            optionsField.classList.add('hidden');

            if (!categoryId) return;

            const category = categoriesData.find(c => c.id == categoryId);
            if (!category || !category.subCategories.length) return;

            // Jika kategori punya beberapa sub-kategori, pakai yang pertama punya options
            // atau bisa di-loop jika perlu multiple groups
            const subCat = category.subCategories[0];
            hiddenSubCat.value = subCat.id;
            optionsLabel.textContent = subCat.name; // label = nama sub-kategori dari DB (e.g. "Tingkat")

            subCat.options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.name;
                option.textContent = opt.name;
                optionsSelect.appendChild(option);
            });

            optionsField.classList.remove('hidden');

            // Restore old value saat validasi gagal
            const oldVal = "{{ old('sub_category_option') }}";
            if (oldVal) optionsSelect.value = oldVal;
        }

        function toggleExpertiseField() {
            const sel = document.getElementById('google_category_id');
            const slug = sel.options[sel.selectedIndex].getAttribute('data-slug');
            const ef = document.getElementById('expertiseField');
            const es = document.getElementById('expertise_id');
            if (slug === 'prestasi') {
                ef.classList.remove('hidden');
                es.required = true;
            } else {
                ef.classList.add('hidden');
                es.required = false;
                es.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleExpertiseField();
            updateSubCategories();
        });

        document.getElementById('uploadForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitBtnText').textContent = 'Mengupload…';
        });

        (function() {
            const fileInput = document.getElementById('file');
            const submitBtn = document.getElementById('submitBtn');
            const clientError = document.getElementById('clientError');
            const remainingBytes = Number({{ $remainingBytes ?? 0 }});
            const perFileLimit = 1 * 1024 * 1024;

            function setError(msg) {
                clientError.textContent = msg;
                clientError.classList.remove('hidden');
            }

            function clearError() {
                clientError.textContent = '';
                clientError.classList.add('hidden');
            }

            fileInput.addEventListener('change', function() {
                clearError();
                const f = fileInput.files && fileInput.files[0];
                if (!f) return;
                const isPdf = f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf');
                const isImg = f.type.startsWith('image/');
                if (!isPdf && !isImg) {
                    setError('Hanya file PDF atau gambar yang diperbolehkan.');
                    submitBtn.disabled = true;
                    return;
                }
                if (f.size > perFileLimit) {
                    setError('Ukuran file tidak boleh lebih dari 1 MB.');
                    submitBtn.disabled = true;
                    return;
                }
                if (f.size > remainingBytes) {
                    setError('Sisa kuota tidak cukup. Sisa: ' + (remainingBytes / (1024 * 1024)).toFixed(2) +
                        ' MB.');
                    submitBtn.disabled = true;
                    return;
                }
                submitBtn.disabled = false;
            });
        })();
    </script>
@endsection
