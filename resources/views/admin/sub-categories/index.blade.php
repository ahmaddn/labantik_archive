@extends('layouts.app')
@section('title', 'Manajemen Sub-Kategori')
@section('page-title', 'Sub-Kategori')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Sub-Kategori</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola sub-kategori dan pilihan enum untuk setiap kategori.</p>
        </div>
        <a href="{{ route('admin.sub-categories.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-[#1b84ff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 transition-colors hover:bg-[#1570e0]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Sub-Kategori
        </a>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="mb-2 text-sm font-semibold text-red-800">Terjadi kesalahan:</p>
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Categories with Sub-Categories --}}
    <div class="space-y-6">
        @if ($categories->isEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50">
                    <svg class="h-7 w-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <p class="text-gray-500">Belum ada kategori. Buat kategori terlebih dahulu di <a
                        href="{{ route('admin.categories.index') }}"
                        class="font-semibold text-blue-600 hover:underline">Manajemen Kategori</a>.</p>
            </div>
        @else
            @foreach ($categories as $category)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    {{-- Category Header --}}
                    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                    <svg class="h-5 w-5 text-[#1b84ff]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $category->subCategories->count() }} sub-kategori
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Categories Table --}}
                    @if ($category->subCategories->isEmpty())
                        <div class="px-6 py-8 text-center text-gray-400">
                            <p class="text-sm">Belum ada sub-kategori untuk kategori ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">#</th>
                                        <th class="px-6 py-3 text-left font-semibold">Nama Sub-Kategori</th>
                                        <th class="px-6 py-3 text-left font-semibold">Pilihan Enum</th>
                                        <th class="hidden px-6 py-3 text-center font-semibold md:table-cell">Dokumen</th>
                                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($category->subCategories as $i => $subCategory)
                                        <tr class="transition-colors hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-400">{{ $i + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-gray-800">{{ $subCategory->name }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    @forelse($subCategory->options as $option)
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                                            {{ $option->name }}
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-gray-400">Tidak ada pilihan</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="hidden px-6 py-4 text-center md:table-cell">
                                                <span
                                                    class="{{ $subCategory->files->count() > 0 ? 'bg-blue-50 text-[#1b84ff]' : 'bg-gray-100 text-gray-400' }} inline-flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold">
                                                    {{ $subCategory->files->count() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('admin.sub-categories.edit', $subCategory->id) }}"
                                                        class="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('admin.sub-categories.destroy', $subCategory->id) }}"
                                                        onsubmit="return confirm('Hapus sub-kategori \'{{ addslashes($subCategory->name) }}\'?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="{{ $subCategory->files->count() > 0 ? 'opacity-40 cursor-not-allowed' : '' }} flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-100"
                                                            {{ $subCategory->files->count() > 0 ? 'disabled title=Sub-kategori memiliki ' . $subCategory->files->count() . ' file' : '' }}>
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@endsection
