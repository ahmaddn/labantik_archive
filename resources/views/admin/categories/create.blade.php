@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="max-w-xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 mb-6 text-sm text-gray-500">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-[#1b84ff] transition-colors font-medium">Kategori</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-800 font-semibold">Tambah Kategori</span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-[#1b84ff] to-[#0ea5e9] px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base">Tambah Kategori Baru</h2>
                        <p class="text-blue-100 text-xs mt-0.5">Kategori digunakan untuk mengelompokkan dokumen.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name') }}"
                           autofocus
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                  focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                  @error('name') border-red-400 bg-red-50 @enderror"
                           placeholder="Contoh: Prestasi, Sertifikasi, Akademik"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1.5">Slug akan dibuat otomatis dari nama kategori.</p>
                </div>

                {{-- Preview slug --}}
                <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500">Slug preview:</p>
                        <code id="slugPreview" class="text-xs font-mono text-[#1b84ff]">—</code>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.categories.index') }}"
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            document.getElementById('slugPreview').textContent = slug || '—';
        });
    </script>
@endsection
