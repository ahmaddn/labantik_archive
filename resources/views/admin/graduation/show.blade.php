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
                        <p class="text-sm text-gray-500">
                            NIS: {{ $graduation->user->student_number ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            NISN: {{ $graduation->user->national_student_number ?? '-' }}
                        </p>
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

            {{-- Card: Daftar Mapel --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daftar Mapel & Nilai
                    </h2>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($graduation->mapels as $idx => $graduationMapel)
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                                <span class="font-semibold text-gray-800">
                                                    {{ $graduationMapel->mapel->name ?? '-' }}
                                                </span>
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
                                            <span
                                                class="inline-block px-3 py-1 rounded-lg text-xs font-bold
                                                {{ $score >= 75 ? 'bg-green-50 text-green-700' : ($score >= 50 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                                {{ $score ?? '-' }}
                                            </span>
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
                        @php
                            $avg = $graduation->mapels->avg('score');
                        @endphp
                        <p
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
@endsection
