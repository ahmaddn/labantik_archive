{{--
    resources/views/admin/_partials/user_docs_show.blade.php

    Variables expected:
    - $user            : User model
    - $filesByCategory : Collection (from controller)
    - $totalFiles      : int
    - $usedBytes       : int
    - $quotaLimit      : int
    - $remainingBytes  : int
    - $backRoute       : string (e.g. 'admin.students.index')
    - $backLabel       : string (e.g. 'Data Siswa')
    - $identLabel      : string (e.g. 'NIS')
    - $identifier      : string (e.g. 'nis')
--}}

@php
    $quotaMb     = round($quotaLimit / 1024 / 1024, 2);
    $usedMb      = round($usedBytes / 1024 / 1024, 2);
    $remainingMb = round($remainingBytes / 1024 / 1024, 2);
    $usedPct     = $quotaMb > 0 ? min(100, round(($usedMb / $quotaMb) * 100)) : 0;
@endphp

{{-- Breadcrumb / Back --}}
<div class="mb-5 flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route($backRoute) }}" class="flex items-center gap-1.5 font-medium text-[#1b84ff] hover:underline">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ $backLabel }}
    </a>
    <span>/</span>
    <span class="text-gray-700 font-medium">{{ $user->name }}</span>
</div>

{{-- Header --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-xl font-extrabold text-white shadow-md">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
            <p class="mt-0.5 text-sm text-gray-500">
                {{ $identLabel }}: <span class="font-semibold text-gray-700">{{ $user->{$identifier} ?? '—' }}</span>
                @if(isset($user->class_name) && $user->class_name)
                    &nbsp;·&nbsp; Kelas: <span class="font-semibold text-gray-700">{{ $user->class_name }}</span>
                @endif
            </p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ $totalFiles }} Dokumen
        </span>
    </div>
</div>

{{-- Quota Card --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                <svg class="h-4 w-4 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-gray-700">Penggunaan Storage</span>
        </div>
        <span class="text-sm font-bold text-gray-800">
            {{ $usedMb }} / {{ $quotaMb }} MB
            <span class="font-normal text-gray-400">({{ $usedPct }}%)</span>
        </span>
    </div>
    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
        <div class="{{ $usedPct >= 90 ? 'bg-red-500' : ($usedPct >= 70 ? 'bg-yellow-400' : 'bg-[#1b84ff]') }} h-full rounded-full transition-all duration-500"
             style="width: {{ $usedPct }}%"></div>
    </div>
    <p class="mt-2 text-xs text-gray-400">Sisa: <strong class="text-gray-600">{{ $remainingMb }} MB</strong></p>
</div>

{{-- Category Accordion --}}
@if($filesByCategory->count() > 0)
    <div class="space-y-4">
        @foreach($filesByCategory as $categoryData)
            @php
                $catColor = $loop->index % 5;
                $colors = [
                    ['bg' => 'bg-[#1b84ff]', 'light' => 'bg-[#eff6ff]', 'border' => 'border-blue-200',   'badge' => 'bg-blue-100 text-blue-700'],
                    ['bg' => 'bg-[#0ea5e9]', 'light' => 'bg-sky-50',    'border' => 'border-sky-200',    'badge' => 'bg-sky-100 text-sky-700'],
                    ['bg' => 'bg-[#6366f1]', 'light' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-700'],
                    ['bg' => 'bg-[#0891b2]', 'light' => 'bg-cyan-50',   'border' => 'border-cyan-200',   'badge' => 'bg-cyan-100 text-cyan-700'],
                    ['bg' => 'bg-[#2563eb]', 'light' => 'bg-blue-50',   'border' => 'border-blue-100',   'badge' => 'bg-blue-100 text-blue-700'],
                ];
                $c     = $colors[$catColor];
                $boxId = 'cat-' . $loop->index;
            @endphp
            <div class="{{ $c['border'] }} overflow-hidden rounded-2xl border shadow-sm">
                <button type="button" onclick="toggleCat('{{ $boxId }}')"
                        class="{{ $c['bg'] }} group flex w-full items-center justify-between px-5 py-4 focus:outline-none">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold tracking-wide text-white">{{ $categoryData['category']->name }}</span>
                        <span class="ml-1 rounded-full bg-white/25 px-2.5 py-0.5 text-xs font-semibold text-white">
                            {{ $categoryData['files']->count() }} dokumen
                        </span>
                    </div>
                    <svg id="{{ $boxId }}-chevron" class="cat-chevron h-5 w-5 text-white opacity-80"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="{{ $boxId }}" class="cat-body {{ $c['light'] }}">
                    @if($categoryData['files']->isEmpty())
                        <div class="py-10 text-center text-sm text-gray-400">Tidak ada dokumen dalam kategori ini.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="{{ $c['border'] }} border-b text-xs uppercase tracking-wider text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">Nama Dokumen</th>
                                        <th class="hidden px-6 py-3 text-left font-semibold xl:table-cell">Keahlian</th>
                                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">Ukuran</th>
                                        <th class="hidden px-6 py-3 text-left font-semibold sm:table-cell">Tanggal</th>
                                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100/80">
                                    @foreach($categoryData['files'] as $file)
                                        <tr class="transition-colors hover:bg-white/60">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">
                                                        @if(str_contains($file->mime_type ?? '', 'pdf'))
                                                            <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="block font-semibold leading-tight text-gray-800">{{ $file->document_name }}</span>
                                                        <span class="text-xs text-gray-400">{{ $file->name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden px-6 py-4 xl:table-cell">
                                                @if($file->expertise)
                                                    <span class="{{ $c['badge'] }} rounded-full px-2 py-0.5 text-xs font-medium">{{ $file->expertise->name }}</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="hidden px-6 py-4 text-xs text-gray-500 md:table-cell">{{ $file->formatted_size }}</td>
                                            <td class="hidden px-6 py-4 text-xs text-gray-500 sm:table-cell">{{ $file->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ $file->web_content_link }}" download
                                                       class="flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-blue-600 shadow-sm transition-colors hover:bg-blue-50">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                        Unduh
                                                    </a>
                                                    <a href="{{ $file->web_view_link }}" target="_blank"
                                                       class="flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-green-600 shadow-sm transition-colors hover:bg-green-50">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Lihat
                                                    </a>
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
@else
    <div class="rounded-2xl border border-gray-200 bg-white py-20 text-center shadow-sm">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50">
            <svg class="h-8 w-8 text-[#1b84ff] opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-500">Pengguna ini belum mengunggah dokumen apapun.</p>
    </div>
@endif

<script>
function toggleCat(id) {
    const body    = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    const isOpen  = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    chevron.classList.toggle('rotated', !isOpen);
}
document.addEventListener('DOMContentLoaded', function () {
    const first = document.querySelector('.cat-body');
    if (first) {
        first.classList.add('open');
        const ch = document.getElementById(first.id + '-chevron');
        if (ch) ch.classList.add('rotated');
    }
});
</script>
