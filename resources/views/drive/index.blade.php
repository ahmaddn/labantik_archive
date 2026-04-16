@extends('layouts.app')
@section('title', 'My Drive')
@section('page-title', 'My Drive')

@section('content')
    @php
        $quotaMb     = isset($quotaLimit)      ? round($quotaLimit / 1024 / 1024, 2) : null;
        $remainingMb = isset($remainingBytes)  ? round($remainingBytes / 1024 / 1024, 2) : 0;
        $usedMb      = $quotaMb ? round($quotaMb - $remainingMb, 2) : 0;
        $usedPct     = ($quotaMb && $quotaMb > 0) ? min(100, round(($usedMb / $quotaMb) * 100)) : 0;
    @endphp

    {{-- ── Header Row ─────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">My Drive</h1>
            <p class="text-gray-500 text-sm mt-1">Dokumen Anda tersimpan aman di Google Drive.</p>
        </div>
        <div class="flex items-center gap-3">
            @if (!$isConnected)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-medium rounded-xl">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 inline-block"></span>
                    Drive belum terhubung
                </span>
            @endif
            <a href="{{ route('drive.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Dokumen
            </a>
        </div>
    </div>

    {{-- ── Quota Card ──────────────────────────────── --}}
    @if($quotaMb)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 col-span-1 sm:col-span-3">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Penggunaan Kuota</span>
                </div>
                <span class="text-sm font-bold text-gray-800">{{ $usedMb }} / {{ $quotaMb }} MB <span class="text-gray-400 font-normal">({{ $usedPct }}%)</span></span>
            </div>
            <div class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                    {{ $usedPct >= 90 ? 'bg-red-500' : ($usedPct >= 70 ? 'bg-yellow-400' : 'bg-[#1b84ff]') }}"
                     style="width: {{ $usedPct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Sisa: <strong class="text-gray-600">{{ $remainingMb }} MB</strong></p>
        </div>
    </div>
    @endif

    {{-- ── Category Accordion Boxes ─────────────────── --}}
    @if(isset($filesByCategory) && $filesByCategory->count() > 0)
        <div class="space-y-4">
            @foreach($filesByCategory as $categoryData)
                @php
                    $catColor = $loop->index % 5;
                    $colors = [
                        ['bg' => 'bg-[#1b84ff]', 'light' => 'bg-[#eff6ff]', 'border' => 'border-blue-200', 'badge' => 'bg-blue-100 text-blue-700'],
                        ['bg' => 'bg-[#0ea5e9]', 'light' => 'bg-sky-50',    'border' => 'border-sky-200',  'badge' => 'bg-sky-100 text-sky-700'],
                        ['bg' => 'bg-[#6366f1]', 'light' => 'bg-indigo-50', 'border' => 'border-indigo-200','badge' => 'bg-indigo-100 text-indigo-700'],
                        ['bg' => 'bg-[#0891b2]', 'light' => 'bg-cyan-50',   'border' => 'border-cyan-200',  'badge' => 'bg-cyan-100 text-cyan-700'],
                        ['bg' => 'bg-[#2563eb]', 'light' => 'bg-blue-50',   'border' => 'border-blue-100',  'badge' => 'bg-blue-100 text-blue-700'],
                    ];
                    $c = $colors[$catColor];
                    $boxId = 'cat-' . $loop->index;
                @endphp

                <div class="rounded-2xl overflow-hidden border {{ $c['border'] }} shadow-sm">
                    {{-- Category Header (click to expand) --}}
                    <button type="button"
                            onclick="toggleCat('{{ $boxId }}')"
                            class="{{ $c['bg'] }} w-full flex items-center justify-between px-5 py-4 group focus:outline-none">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <span class="font-bold text-white text-sm tracking-wide">{{ $categoryData['category']->name }}</span>
                            <span class="ml-1 px-2.5 py-0.5 bg-white/25 text-white text-xs rounded-full font-semibold">
                                {{ $categoryData['files']->count() }} dokumen
                            </span>
                        </div>
                        <svg id="{{ $boxId }}-chevron"
                             class="cat-chevron w-5 h-5 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Files Table (collapsible) --}}
                    <div id="{{ $boxId }}" class="cat-body {{ $c['light'] }}">
                        @if($categoryData['files']->isEmpty())
                            <div class="text-center py-10 text-gray-400 text-sm">
                                Tidak ada dokumen dalam kategori ini.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="border-b {{ $c['border'] }} text-gray-500 text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="text-left px-6 py-3 font-semibold">Nama Dokumen</th>
                                            <th class="text-left px-6 py-3 font-semibold hidden xl:table-cell">Keahlian</th>
                                            <th class="text-left px-6 py-3 font-semibold hidden md:table-cell">Ukuran</th>
                                            <th class="text-left px-6 py-3 font-semibold hidden sm:table-cell">Tanggal</th>
                                            <th class="text-center px-6 py-3 font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100/80">
                                        @foreach($categoryData['files'] as $file)
                                            <tr class="hover:bg-white/60 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <span class="font-semibold text-gray-800 block leading-tight">{{ $file->document_name }}</span>
                                                            <span class="text-xs text-gray-400">{{ $file->name }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 hidden xl:table-cell">
                                                    @if($file->expertise)
                                                        <span class="text-xs {{ $c['badge'] }} px-2 py-0.5 rounded-full font-medium">{{ $file->expertise->name }}</span>
                                                    @else
                                                        <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 text-xs hidden md:table-cell">
                                                    {{ $file->formatted_size }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 text-xs hidden sm:table-cell">
                                                    {{ $file->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <a href="{{ $file->web_content_link }}" download
                                                           class="flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-blue-50 border border-gray-200 text-blue-600 rounded-lg transition-colors text-xs font-medium shadow-sm">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            Unduh
                                                        </a>
                                                        <a href="{{ $file->web_view_link }}" target="_blank"
                                                           class="flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-green-50 border border-gray-200 text-green-600 rounded-lg transition-colors text-xs font-medium shadow-sm">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                            Lihat
                                                        </a>
                                                        <form method="POST" action="{{ route('drive.destroy', $file->id) }}"
                                                              onsubmit="return confirm('Hapus \'{{ addslashes($file->document_name) }}\'?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-red-50 border border-gray-200 text-red-500 rounded-lg transition-colors text-xs font-medium shadow-sm">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                </div>
            @endforeach
        </div>

    {{-- ── Fallback: flat list (jika filesByCategory tidak dikirim) ── --}}
    @elseif(isset($files) && $files->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Semua Dokumen</h2>
                <span class="text-sm text-gray-500">{{ $files->total() }} dokumen</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="text-left px-6 py-3">Nama Dokumen</th>
                            <th class="text-left px-6 py-3 hidden lg:table-cell">Kategori</th>
                            <th class="text-left px-6 py-3 hidden xl:table-cell">Keahlian</th>
                            <th class="text-left px-6 py-3 hidden md:table-cell">Ukuran</th>
                            <th class="text-left px-6 py-3 hidden sm:table-cell">Tanggal</th>
                            <th class="text-center px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($files as $file)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-800 block">{{ $file->document_name }}</span>
                                            <span class="text-xs text-gray-400">{{ $file->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                                    @if($file->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            {{ $file->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 hidden xl:table-cell">
                                    {{ $file->expertise?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $file->formatted_size }}</td>
                                <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ $file->web_content_link }}" download
                                           class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">
                                            Unduh
                                        </a>
                                        <a href="{{ $file->web_view_link }}" target="_blank"
                                           class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-xs font-medium">
                                            Lihat
                                        </a>
                                        <form method="POST" action="{{ route('drive.destroy', $file->id) }}"
                                              onsubmit="return confirm('Hapus \'{{ addslashes($file->document_name) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-medium">
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
            @if($files->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $files->links() }}</div>
            @endif
        </div>

    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 py-20 text-center">
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#1b84ff] opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium">Belum ada dokumen. Tambahkan dokumen pertama Anda!</p>
            <a href="{{ route('drive.create') }}"
               class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Dokumen
            </a>
        </div>
    @endif

    <script>
        function toggleCat(id) {
            const body    = document.getElementById(id);
            const chevron = document.getElementById(id + '-chevron');
            const isOpen  = body.classList.contains('open');
            if (isOpen) {
                body.classList.remove('open');
                chevron.classList.remove('rotated');
            } else {
                body.classList.add('open');
                chevron.classList.add('rotated');
            }
        }
        // Auto-open first category
        document.addEventListener('DOMContentLoaded', function() {
            const first = document.querySelector('.cat-body');
            if (first) {
                first.classList.add('open');
                const id = first.id + '-chevron';
                const ch = document.getElementById(id);
                if (ch) ch.classList.add('rotated');
            }
        });
    </script>
@endsection
