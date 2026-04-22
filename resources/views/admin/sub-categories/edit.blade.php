@extends('layouts.app')
@section('title', 'Edit Sub-Kategori')
@section('page-title', 'Sub-Kategori')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.sub-categories.index') }}" class="rounded-lg p-2 transition-colors hover:bg-gray-100">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Edit Sub-Kategori</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi sub-kategori dan pilihan enum.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.sub-categories.update', $subCategory->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Category Selection --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <label for="google_category_id" class="mb-2 block text-sm font-semibold text-gray-800">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select id="google_category_id" name="google_category_id" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('google_category_id', $subCategory->google_category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('google_category_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sub-Category Name --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-800">
                    Nama Sub-Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Tingkat, Jenis Prestasi, dll" value="{{ old('name', $subCategory->name) }}">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Options Section --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-800">
                        Pilihan Enum / Opsi
                    </label>
                    <button type="button" id="addOptionBtn"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pilihan
                    </button>
                </div>

                <div id="optionsContainer" class="space-y-3">
                    @php
                        $oldOptions = old('options', $subCategory->options->pluck('name')->toArray());
                        $optionCount = max(1, count($oldOptions));
                    @endphp
                    @for ($i = 0; $i < $optionCount; $i++)
                        <div class="option-row flex items-end gap-3">
                            <div class="flex-1">
                                <input type="text" name="options[]"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan pilihan..." value="{{ $oldOptions[$i] ?? '' }}">
                            </div>
                            <button type="button"
                                class="remove-option-btn rounded-lg bg-red-50 px-3 py-2.5 text-red-600 transition-colors hover:bg-red-100"
                                style="display: {{ $optionCount === 1 ? 'none' : 'block' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endfor
                </div>

                <p class="mt-4 text-xs text-gray-500">
                    Ubah pilihan yang dapat dipilih pengguna saat upload file dengan sub-kategori ini.
                </p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#1b84ff] px-6 py-2.5 font-semibold text-white shadow-sm shadow-blue-200 transition-colors hover:bg-[#1570e0]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.sub-categories.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-6 py-2.5 font-semibold text-gray-700 transition-colors hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>

@section('styles')
    <style>
        .option-row {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        const addOptionBtn = document.getElementById('addOptionBtn');
        const optionsContainer = document.getElementById('optionsContainer');

        addOptionBtn.addEventListener('click', () => {
            const newOption = document.createElement('div');
            newOption.className = 'flex items-end gap-3 option-row';
            newOption.innerHTML = `
                    <div class="flex-1">
                        <input type="text" name="options[]"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               placeholder="Masukkan pilihan...">
                    </div>
                    <button type="button" class="remove-option-btn px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
            optionsContainer.appendChild(newOption);
            updateRemoveButtons();
            newOption.querySelector('input').focus();
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.option-row');
            rows.forEach(row => {
                const removeBtn = row.querySelector('.remove-option-btn');
                if (removeBtn) {
                    removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                }
            });
        }

        optionsContainer.addEventListener('click', (e) => {
            if (e.target.closest('.remove-option-btn')) {
                e.preventDefault();
                e.target.closest('.option-row').remove();
                updateRemoveButtons();
            }
        });

        // Initialize on page load
        updateRemoveButtons();
    </script>
@endsection
@endsection
