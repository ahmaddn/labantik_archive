@extends('layouts.app')
@php $hide_global_alerts = true; @endphp
@section('title', 'Detail Kelulusan')
@section('page-title', 'Detail Kelulusan')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Detail Kelulusan</h1>
            <p class="text-gray-500 text-sm mt-1">Lihat informasi lengkap kelulusan siswa.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card: Informasi Siswa --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Siswa
                </h2>

                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-2xl font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr($graduation->user->full_name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $graduation->user->full_name ?? 'Siswa Terhapus' }}
                        </p>
                        <p class="text-sm text-gray-500">NIS: {{ $graduation->user->student_number ?? '-' }}</p>
                        <p class="text-sm text-gray-500">NISN: {{ $graduation->user->national_student_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium mb-1">Student ID</p>
                        <p class="text-sm font-mono text-gray-800">{{ $graduation->user_id }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium mb-1">Kelulusan ID</p>
                        <p class="text-sm font-mono text-gray-800">{{ $graduation->uuid }}</p>
                    </div>
                </div>
            </div>

            {{-- Card: Data Kelulusan --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Data Kelulusan
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Nomor Surat Kelulusan</p>
                            <p class="text-xs text-gray-500 mt-1">Nomor identitas kelulusan</p>
                        </div>
                        <p class="font-mono text-sm font-bold text-gray-900 bg-blue-50 px-3 py-1.5 rounded-lg">
                            {{ $graduation->letter_number ?: '-' }}
                        </p>
                    </div>

                    <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Tanggal Kelulusan</p>
                            <p class="text-xs text-gray-500 mt-1">Tanggal siswa lulus</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $graduation->graduation_date ? \Carbon\Carbon::parse($graduation->graduation_date)->format('d F Y') : '-' }}
                        </p>
                    </div>

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Jumlah Mapel</p>
                            <p class="text-xs text-gray-500 mt-1">Total mapel kelulusan</p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-bold">
                            {{ $graduation->mapels->count() }} Mapel
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card: Surat Kelulusan (hanya tampil jika ada) --}}
            @if ($graduation->letter)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Surat Kelulusan
                    </h2>

                    @php $letter = $graduation->letter; @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <p class="text-xs text-indigo-500 font-medium mb-1">Nomor Surat</p>
                            <p class="text-sm font-bold text-indigo-900 font-mono">{{ $letter->letter_number ?? '-' }}</p>
                        </div>
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <p class="text-xs text-indigo-500 font-medium mb-1">Tanggal Surat</p>
                            <p class="text-sm font-bold text-indigo-900">
                                {{ $letter->letter_date ? \Carbon\Carbon::parse($letter->letter_date)->format('d F Y') : '-' }}
                            </p>
                        </div>
                        @if ($letter->title)
                            <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                                <p class="text-xs text-gray-500 font-medium mb-1">Judul Template</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $letter->title }}</p>
                            </div>
                        @endif
                        @if ($letter->description)
                            <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                                <p class="text-xs text-gray-500 font-medium mb-1">Keterangan</p>
                                <p class="text-sm text-gray-700">{{ $letter->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Card: Daftar Mapel & Nilai --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daftar Mapel & Nilai
                    </h2>
                    @if ($graduation->mapels->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.graduation.downloadTemplate', ['user_id' => $graduation->user_id]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-colors border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Template
                            </a>

                            <a href="{{ route('admin.graduation.showImportNilai') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold rounded-lg transition-colors border border-green-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import
                            </a>

                            <button type="button" onclick="openBulkScoreModal()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold rounded-lg transition-colors border border-purple-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Semua Nilai
                            </button>
                        </div>
                    @endif
                </div>

                @if ($graduation->mapels->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-10 h-10 opacity-40 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm">Belum ada mapel dalam kelulusan ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="text-left px-6 py-3 font-semibold">#</th>
                                    <th class="text-left px-6 py-3 font-semibold">Nama Mapel</th>
                                    <th class="text-left px-6 py-3 font-semibold hidden 2xl:table-cell">Kelas</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S1</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S2</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S3</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S4</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S5</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden xl:table-cell">S6</th>
                                    <th class="text-center px-2 py-3 font-semibold hidden lg:table-cell">NR</th>
                                    <th class="text-center px-6 py-3 font-semibold">Nilai (NA)</th>
                                    <th class="text-center px-6 py-3 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="mapelScoreTableBody">
                                @foreach ($graduation->mapels as $idx => $graduationMapel)
                                    <tr class="hover:bg-gray-50 transition-colors" id="row-{{ $graduationMapel->uuid }}">
                                        <td class="px-6 py-4 text-gray-400 font-medium">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-[#1b84ff]" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800 text-xs sm:text-sm">
                                                        {{ $graduationMapel->mapel->name ?? '-' }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium hidden sm:block">
                                                        {{ $graduationMapel->mapel->expertise->name ?? 'Umum' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden 2xl:table-cell">
                                            <span
                                                class="inline-block text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg font-medium">
                                                {{ $graduationMapel->mapel->class->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem1-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_1 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem2-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_2 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem3-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_3 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem4-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_4 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem5-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_5 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden xl:table-cell text-xs font-medium text-gray-600"
                                            id="sem6-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->sem_6 ?? '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center hidden lg:table-cell text-xs font-bold text-blue-600"
                                            id="nr-{{ $graduationMapel->uuid }}">
                                            {{ $graduationMapel->nr ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php $score = $graduationMapel->score @endphp
                                            <span id="score-badge-{{ $graduationMapel->uuid }}"
                                                class="inline-block px-3 py-1 rounded-lg text-xs font-bold
                                                {{ $score >= 75 ? 'bg-green-50 text-green-700' : ($score >= 50 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                                {{ $score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button"
                                                onclick="openEditScoreModal('{{ $graduationMapel->uuid }}', '{{ addslashes($graduationMapel->mapel->name ?? '-') }}', {
                                                    score: {{ $graduationMapel->score ?? 'null' }},
                                                    sem1: {{ $graduationMapel->sem_1 ?? 'null' }},
                                                    sem2: {{ $graduationMapel->sem_2 ?? 'null' }},
                                                    sem3: {{ $graduationMapel->sem_3 ?? 'null' }},
                                                    sem4: {{ $graduationMapel->sem_4 ?? 'null' }},
                                                    sem5: {{ $graduationMapel->sem_5 ?? 'null' }},
                                                    sem6: {{ $graduationMapel->sem_6 ?? 'null' }},
                                                    nr: {{ $graduationMapel->nr ?? 'null' }}
                                                })"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium rounded-lg transition-colors border border-amber-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-20">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Timeline
                </h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1">Dibuat Pada</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($graduation->created_at)->format('d F Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($graduation->created_at)->diffForHumans() }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500 font-medium mb-1">Diperbarui Pada</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($graduation->updated_at)->format('d F Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($graduation->updated_at)->diffForHumans() }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500 font-medium mb-1">Rata-rata Nilai</p>
                        @php $avg = $graduation->mapels->avg('score'); @endphp
                        <p id="avgScoreDisplay"
                            class="text-2xl font-extrabold {{ $avg >= 75 ? 'text-green-600' : ($avg >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $avg ? number_format($avg, 1) : '-' }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-500 font-medium mb-1">Status</p>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <p class="text-sm font-semibold text-green-700">Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL: Edit Nilai (single mapel)
    ══════════════════════════════════════════════════════════ --}}
    <div id="editScoreModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeEditScoreModal()"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50">
                            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Edit Nilai Mapel</h3>
                            <p class="text-xs text-gray-500" id="editScoreMapelName">—</p>
                        </div>
                    </div>
                    <button onclick="closeEditScoreModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                        @foreach (range(1, 6) as $sem)
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Semester
                                    {{ $sem }}</label>
                                <input type="number" id="sem{{ $sem }}Input" step="0.01" min="0"
                                    max="100" placeholder="-"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-center text-sm font-bold text-gray-800 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-100">
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-5">
                        <div>
                            <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nilai Rapor (NR)</label>
                            <input type="number" id="nrInput" step="0.01" min="0" max="100"
                                placeholder="-"
                                class="w-full rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-center text-base font-bold text-blue-700 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-green-600 uppercase mb-1">Nilai Akhir (NA)</label>
                            <input type="number" id="editScoreInput" step="0.01" min="0" max="100"
                                placeholder="-"
                                class="w-full rounded-xl border border-green-100 bg-green-50 px-4 py-2.5 text-center text-base font-bold text-green-700 shadow-sm focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>
                    </div>
                    <p class="mt-4 text-[10px] text-gray-400 text-center italic">Kosongkan jika belum ada nilai. Nilai NA
                        akan muncul di Surat Kelulusan.</p>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button type="button" onclick="closeEditScoreModal()"
                        class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="saveScore()" id="saveScoreBtn"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 active:scale-[0.98] transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL: Edit Semua Nilai (bulk)
    ══════════════════════════════════════════════════════════ --}}
    <div id="bulkScoreModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeBulkScoreModal()"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Edit Semua Nilai</h3>
                            <p class="text-xs text-gray-500">Ubah nilai sekaligus untuk semua mapel</p>
                        </div>
                    </div>
                    <button onclick="closeBulkScoreModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-3" id="bulkScoreBody">
                    @foreach ($graduation->mapels as $graduationMapel)
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $graduationMapel->mapel->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $graduationMapel->mapel->expertise->name ?? 'Umum' }}
                                </p>
                            </div>
                            <input type="number" name="bulk_score[{{ $graduationMapel->uuid }}]"
                                data-uuid="{{ $graduationMapel->uuid }}" min="0" max="100"
                                value="{{ $graduationMapel->score ?? '' }}" placeholder="—"
                                class="bulk-score-input w-24 rounded-xl border border-gray-200 px-3 py-1.5 text-center text-sm font-bold text-gray-800 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-100">
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button type="button" onclick="closeBulkScoreModal()"
                        class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="saveBulkScores()" id="saveBulkScoreBtn"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 active:scale-[0.98] transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    html: `
                        <ul class="text-left text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                });
            @endif
        });

        // ══════════════════════════════════════════════════════════
        // STATE
        // ══════════════════════════════════════════════════════════
        var _editUuid = null;

        // ══════════════════════════════════════════════════════════
        // MODAL: Edit Single Score
        // ══════════════════════════════════════════════════════════
        function openEditScoreModal(uuid, mapelName, currentData) {
            _editUuid = uuid;
            document.getElementById('editScoreMapelName').textContent = mapelName;

            // NA
            document.getElementById('editScoreInput').value = currentData.score !== null ? currentData.score : '';

            // Semesters
            document.getElementById('sem1Input').value = currentData.sem1 !== null ? currentData.sem1 : '';
            document.getElementById('sem2Input').value = currentData.sem2 !== null ? currentData.sem2 : '';
            document.getElementById('sem3Input').value = currentData.sem3 !== null ? currentData.sem3 : '';
            document.getElementById('sem4Input').value = currentData.sem4 !== null ? currentData.sem4 : '';
            document.getElementById('sem5Input').value = currentData.sem5 !== null ? currentData.sem5 : '';
            document.getElementById('sem6Input').value = currentData.sem6 !== null ? currentData.sem6 : '';

            // NR
            document.getElementById('nrInput').value = currentData.nr !== null ? currentData.nr : '';

            document.getElementById('editScoreModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(function() {
                document.getElementById('nrInput').focus();
            }, 100);
        }

        function closeEditScoreModal() {
            document.getElementById('editScoreModal').classList.add('hidden');
            document.body.style.overflow = '';
            _editUuid = null;
        }

        function adjustScore(delta) {
            var input = document.getElementById('editScoreInput');
            var val = parseFloat(input.value) || 0;
            val = Math.min(100, Math.max(0, val + delta));
            input.value = val;
        }

        async function saveScore() {
            if (!_editUuid) return;

            const payload = {
                uuid: _editUuid,
                sem_1: document.getElementById('sem1Input').value || null,
                sem_2: document.getElementById('sem2Input').value || null,
                sem_3: document.getElementById('sem3Input').value || null,
                sem_4: document.getElementById('sem4Input').value || null,
                sem_5: document.getElementById('sem5Input').value || null,
                sem_6: document.getElementById('sem6Input').value || null,
                nr: document.getElementById('nrInput').value || null,
                score: document.getElementById('editScoreInput').value || null,
            };

            var btn = document.getElementById('saveScoreBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan…';

            try {
                var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                var res = await fetch('{{ route('admin.graduation.updateScore') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                var data = await res.json();
                if (data.success) {
                    // Update NA badge
                    updateScoreBadge(_editUuid, payload.score);

                    // Update semester columns
                    updateSemesterColumns(_editUuid, payload);

                    closeEditScoreModal();

                    // Refresh Average
                    refreshAverage();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nilai berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan')
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menyimpan: ' + err.message
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Perubahan';
            }
        }

        function updateSemesterColumns(uuid, data) {
            const fields = ['sem1', 'sem2', 'sem3', 'sem4', 'sem5', 'sem6', 'nr'];
            fields.forEach(f => {
                const el = document.getElementById(f.replace('sem', 'sem') + '-' + uuid); // sem1-uuid
                if (el) el.textContent = data[f === 'nr' ? 'nr' : f.replace('sem', 'sem_')] || '-';
            });

            // Update the Edit button onclick arguments to reflect new data
            const row = document.getElementById('row-' + uuid);
            if (row) {
                const editBtn = row.querySelector('button[onclick^="openEditScoreModal"]');
                if (editBtn) {
                    const mapelName = document.getElementById('editScoreMapelName').textContent;
                    editBtn.setAttribute('onclick', `openEditScoreModal('${uuid}', '${mapelName.replace(/'/g, "\\'")}', ${JSON.stringify({
                        score: data.score,
                        sem1: data.sem_1,
                        sem2: data.sem_2,
                        sem3: data.sem_3,
                        sem4: data.sem_4,
                        sem5: data.sem_5,
                        sem6: data.sem_6,
                        nr: data.nr
                    })})`);
                }
            }
        }

        function refreshAverage() {
            const badges = document.querySelectorAll('[id^="score-badge-"]');
            let sum = 0;
            let count = 0;
            badges.forEach(b => {
                const val = parseFloat(b.textContent);
                if (!isNaN(val)) {
                    sum += val;
                    count++;
                }
            });
            const avg = count > 0 ? (sum / count).toFixed(1) : '-';
            const display = document.getElementById('avgScoreDisplay');
            if (display) {
                display.textContent = avg;
                display.className = 'text-2xl font-extrabold ' + (avg >= 75 ? 'text-green-600' : (avg >= 50 ?
                    'text-yellow-600' : 'text-red-600'));
            }
        }

        // ══════════════════════════════════════════════════════════
        // MODAL: Bulk Score
        // ══════════════════════════════════════════════════════════
        function openBulkScoreModal() {
            document.getElementById('bulkScoreModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeBulkScoreModal() {
            document.getElementById('bulkScoreModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function saveBulkScores() {
            var inputs = document.querySelectorAll('.bulk-score-input');
            var scores = [];

            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                var val = input.value;
                var score = val === '' ? null : parseFloat(val);
                if (score !== null && (score < 0 || score > 100)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nilai Tidak Valid',
                        text: 'Nilai harus antara 0 dan 100 untuk semua mapel.'
                    });
                    return;
                }
                scores.push({
                    uuid: input.dataset.uuid,
                    score: score
                });
            }

            var btn = document.getElementById('saveBulkScoreBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan…';

            try {
                var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                var res = await fetch('{{ route('admin.graduation.updateScoreBulk') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        scores: scores
                    }),
                });

                var data = await res.json();
                if (data.success) {
                    scores.forEach(function(item) {
                        updateScoreBadge(item.uuid, item.score);
                    });
                    closeBulkScoreModal();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Semua nilai berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan')
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menyimpan: ' + err.message
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Semua';
            }
        }

        // ══════════════════════════════════════════════════════════
        // HELPER: update badge nilai di tabel tanpa reload
        // ══════════════════════════════════════════════════════════
        function updateScoreBadge(uuid, score) {
            var badge = document.getElementById('score-badge-' + uuid);
            if (!badge) return;

            badge.textContent = score !== null ? score : '-';

            // Reset class warna
            badge.className = 'inline-block px-3 py-1 rounded-lg text-xs font-bold ';
            if (score === null) {
                badge.className += 'bg-gray-100 text-gray-400';
            } else if (score >= 75) {
                badge.className += 'bg-green-50 text-green-700';
            } else if (score >= 50) {
                badge.className += 'bg-yellow-50 text-yellow-700';
            } else {
                badge.className += 'bg-red-50 text-red-700';
            }

            // Update juga input di bulk modal
            var bulkInput = document.querySelector('.bulk-score-input[data-uuid="' + uuid + '"]');
            if (bulkInput) bulkInput.value = score !== null ? score : '';
        }

        // ══════════════════════════════════════════════════════════
        // KEYBOARD
        // ══════════════════════════════════════════════════════════
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditScoreModal();
                closeBulkScoreModal();
            }
            if (e.key === 'Enter' && _editUuid) {
                saveScore();
            }
        });
    </script>
@endsection
