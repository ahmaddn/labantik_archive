@extends('layouts.app')
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

            <form method="POST" action="{{ route('admin.graduation.destroy', $graduation->uuid) }}"
                onsubmit="return confirm('Hapus data kelulusan siswa {{ $graduation->user->full_name ?? 'Siswa' }}? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-red-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
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
                        <button type="button" onclick="openBulkScoreModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold rounded-lg transition-colors border border-purple-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Semua Nilai
                        </button>
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
                                    <th class="text-left px-6 py-3 font-semibold hidden sm:table-cell">Kelas</th>
                                    <th class="text-left px-6 py-3 font-semibold hidden md:table-cell">Jurusan</th>
                                    <th class="text-center px-6 py-3 font-semibold hidden lg:table-cell">Tipe</th>
                                    <th class="text-center px-6 py-3 font-semibold">Nilai</th>
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
                                                <span
                                                    class="font-semibold text-gray-800">{{ $graduationMapel->mapel->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden sm:table-cell">
                                            <span
                                                class="inline-block text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg font-medium">
                                                {{ $graduationMapel->mapel->class->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 hidden md:table-cell text-xs font-medium">
                                            {{ $graduationMapel->mapel->expertise->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center hidden lg:table-cell">
                                            @php $type = $graduationMapel->mapel->type ?? '' @endphp
                                            @if ($type === 'umum')
                                                <span
                                                    class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg">Umum</span>
                                            @elseif ($type === 'jurusan')
                                                <span
                                                    class="inline-block px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg">Jurusan</span>
                                            @elseif ($type)
                                                <span
                                                    class="inline-block px-2.5 py-1 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg">{{ $type }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
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
                                                onclick="openEditScoreModal('{{ $graduationMapel->uuid }}', '{{ addslashes($graduationMapel->mapel->name ?? '-') }}', {{ $graduationMapel->score ?? 'null' }})"
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
            <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200">

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
                            <h3 class="text-sm font-bold text-gray-900">Edit Nilai</h3>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nilai <span class="text-red-500">*</span>
                        <span class="ml-1 text-xs text-gray-400 font-normal">(0 – 100)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustScore(-1)"
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:border-gray-300 active:scale-95 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <input type="number" id="editScoreInput" min="0" max="100" placeholder="0"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-center text-lg font-bold text-gray-800 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-100">
                        <button type="button" onclick="adjustScore(1)"
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:border-gray-300 active:scale-95 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">Kosongkan jika belum ada nilai.</p>
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
                        Simpan
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
        // ══════════════════════════════════════════════════════════
        // STATE
        // ══════════════════════════════════════════════════════════
        var _editUuid = null;

        // ══════════════════════════════════════════════════════════
        // MODAL: Edit Single Score
        // ══════════════════════════════════════════════════════════
        function openEditScoreModal(uuid, mapelName, currentScore) {
            _editUuid = uuid;
            document.getElementById('editScoreMapelName').textContent = mapelName;
            document.getElementById('editScoreInput').value = currentScore !== null ? currentScore : '';
            document.getElementById('editScoreModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(function() {
                document.getElementById('editScoreInput').focus();
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

            var scoreVal = document.getElementById('editScoreInput').value;
            var score = scoreVal === '' ? null : parseFloat(scoreVal);

            if (score !== null && (score < 0 || score > 100)) {
                alert('Nilai harus antara 0 dan 100.');
                return;
            }

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
                    body: JSON.stringify({
                        uuid: _editUuid,
                        score: score
                    }),
                });

                var data = await res.json();
                if (data.success) {
                    updateScoreBadge(_editUuid, score);
                    closeEditScoreModal();
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                alert('Gagal menyimpan: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan';
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
                    alert('Nilai harus antara 0 dan 100 untuk semua mapel.');
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
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                alert('Gagal menyimpan: ' + err.message);
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
