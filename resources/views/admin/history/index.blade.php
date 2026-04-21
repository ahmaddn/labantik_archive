@extends('layouts.app')
@section('title', 'History Upload')
@section('page-title', 'History')

@php
    $roleBadges = [
        'siswa'      => ['label' => 'Siswa',    'bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
        'guru'       => ['label' => 'Guru',     'bg' => 'bg-green-100',  'text' => 'text-green-700'],
        'guru-piket' => ['label' => 'Guru TU',  'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
    ];

    function formatBytes(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
@endphp

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">History Upload</h1>
            <p class="text-gray-500 text-sm mt-1">Rekap seluruh dokumen yang diunggah oleh pengguna.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalUploads) }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Dokumen</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalUploaders) }}</p>
                <p class="text-xs text-gray-500 font-medium">Pengguna yang Upload</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ formatBytes($totalSize) }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Ukuran</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-4">
        <form method="GET" action="{{ route('admin.history.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama pengguna atau dokumen..."
                       class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-[#1b84ff] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>

            {{-- Role filter --}}
            <select name="role"
                    class="rounded-xl border border-gray-200 bg-gray-50 py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-[#1b84ff] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1b84ff] sm:w-44">
                <option value="">Semua Role</option>
                <option value="siswa"      {{ $roleFilter === 'siswa'      ? 'selected' : '' }}>Siswa</option>
                <option value="guru"       {{ $roleFilter === 'guru'       ? 'selected' : '' }}>Guru</option>
                <option value="guru-piket" {{ $roleFilter === 'guru-piket' ? 'selected' : '' }}>Guru TU</option>
            </select>

            {{-- Per page --}}
            <select name="per_page"
                    class="rounded-xl border border-gray-200 bg-gray-50 py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-[#1b84ff] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1b84ff] sm:w-36">
                @foreach([10, 15, 25, 50, 100] as $opt)
                    <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }} per hal.</option>
                @endforeach
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>

            @if($search || $roleFilter)
                <a href="{{ route('admin.history.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium rounded-xl transition-colors text-sm flex-shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Riwayat Upload</h2>
            <span class="text-xs text-gray-400">{{ $files->total() }} dokumen ditemukan</span>
        </div>

        @if($files->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium">Belum ada dokumen yang diunggah{{ $search ? ' dengan kata kunci tersebut' : '' }}.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold">#</th>
                            <th class="text-left px-6 py-3 font-semibold">Pengguna</th>
                            <th class="text-left px-6 py-3 font-semibold hidden sm:table-cell">Nama Dokumen</th>
                            <th class="text-left px-6 py-3 font-semibold hidden md:table-cell">Kategori</th>
                            <th class="text-center px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($files as $i => $file)
                            @php
                                $user      = $file->user;
                                $userRoles = $user?->roles ?? collect();
                                $roleCodes = $userRoles->pluck('code')->toArray();

                                // Tentukan show route berdasarkan role
                                if (in_array('siswa', $roleCodes)) {
                                    $showRoute  = route('admin.students.show', $user->id);
                                    $roleCode   = 'siswa';
                                } elseif (in_array('guru', $roleCodes)) {
                                    $showRoute  = route('admin.teachers.show', $user->id);
                                    $roleCode   = 'guru';
                                } elseif (in_array('guru-piket', $roleCodes)) {
                                    $showRoute  = route('admin.piket.show', $user->id);
                                    $roleCode   = 'guru-piket';
                                } else {
                                    $showRoute  = null;
                                    $roleCode   = null;
                                }

                                $badge = $roleCode ? ($roleBadges[$roleCode] ?? null) : null;
                                $rowNum = ($files->currentPage() - 1) * $files->perPage() + $i + 1;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-medium text-xs">{{ $rowNum }}</td>

                                {{-- Pengguna --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-xs font-bold text-white">
                                            {{ strtoupper(substr($user?->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-gray-800 leading-tight">
                                                {{ $user?->name ?? '—' }}
                                            </span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-xs text-gray-400">{{ $user?->email ?? '' }}</span>
                                                @if($badge)
                                                    <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                        {{ $badge['label'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nama Dokumen --}}
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                            @if(str_contains($file->mime_type ?? '', 'pdf'))
                                                <svg class="h-3.5 w-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                                </svg>
                                            @else
                                                <svg class="h-3.5 w-3.5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-gray-800 leading-tight text-xs">{{ $file->document_name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $file->name }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="px-6 py-4 hidden md:table-cell">
                                    @if($file->category)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $file->category->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tanpa Kategori</span>
                                    @endif
                                </td>

                                

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($showRoute)
                                            <a href="{{ $showRoute }}"
                                               class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($files->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-400 order-2 sm:order-1">
                        Menampilkan {{ $files->firstItem() }}–{{ $files->lastItem() }} dari {{ $files->total() }} dokumen
                    </p>
                    <div class="order-1 sm:order-2">
                        {{ $files->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
