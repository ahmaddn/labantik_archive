@extends('layouts.app')
@section('title', 'Tambah Sub-Kategori')
@section('page-title', 'Sub-Kategori')

@section('styles')
<style>
    .option-row {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6 max-w-2xl mx-auto">
        <a href="{{ route('admin.sub-categories.index') }}"
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Tambah Sub-Kategori Baru</h1>
            <p class="text-gray-500 text-sm mt-1">Buat sub-kategori dan tambahkan pilihan enum.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.sub-categories.store') }}" class="space-y-6" id="subCategoryForm">
            @csrf

            {{-- Category Selection --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <label for="google_category_id" class="block text-sm font-semibold text-gray-800 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select id="google_category_id" name="google_category_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('google_category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('google_category_id')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sub-Category Name --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <label for="name" class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Sub-Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="Contoh: Tingkat, Jenis Prestasi, dll"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Options Section --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-semibold text-gray-800">
                        Pilihan Enum / Opsi
                    </label>
                    <button type="button" id="addOptionBtn"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Pilihan
                    </button>
                </div>

                <div id="optionsContainer" class="space-y-3">
                    @php
                        $oldOptions = old('options', []);
                        $optionCount = max(1, count($oldOptions));
                    @endphp
                    @for($i = 0; $i < $optionCount; $i++)
                        <div class="flex items-center gap-3 option-row">
                            <div class="flex-1">
                                <input type="text" name="options[]"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Contoh: Kabupaten, Provinsi, Nasional, Internasional"
                                       value="{{ $oldOptions[$i] ?? '' }}">
                            </div>
                            <button type="button"
                                    class="removeBtn px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors {{ $optionCount <= 1 ? 'hidden' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endfor
                </div>

                <p class="text-xs text-gray-500 mt-4">
                    Tambahkan pilihan yang dapat dipilih pengguna saat upload file dengan sub-kategori ini.
                </p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Sub-Kategori
                </button>
                <a href="{{ route('admin.sub-categories.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('optionsContainer');
        const addBtn = document.getElementById('addOptionBtn');

        // Add new option row
        addBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 option-row';
            row.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="options[]"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="Masukkan pilihan...">
                </div>
                <button type="button"
                        class="removeBtn px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(row);

            // Attach remove listener to the new button
            row.querySelector('.removeBtn').addEventListener('click', function () {
                row.remove();
                syncRemoveButtons();
            });

            syncRemoveButtons();
            row.querySelector('input[name="options[]"]').focus();
        });

        // Attach remove listeners to existing rows (from old() repopulation)
        container.querySelectorAll('.removeBtn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.closest('.option-row').remove();
                syncRemoveButtons();
            });
        });

        // Show/hide remove buttons based on row count
        function syncRemoveButtons() {
            const rows = container.querySelectorAll('.option-row');
            const removeBtns = container.querySelectorAll('.removeBtn');
            if (rows.length <= 1) {
                removeBtns.forEach(btn => btn.classList.add('hidden'));
            } else {
                removeBtns.forEach(btn => btn.classList.remove('hidden'));
            }
        }

        syncRemoveButtons();
    });
</script>
@endsection
