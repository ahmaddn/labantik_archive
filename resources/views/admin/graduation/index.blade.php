@extends('layouts.app')
@section('title', 'Manajemen Kelulusan')
@section('page-title', 'Kelulusan')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Kelulusan</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data mapel dan nilai kelulusan siswa.</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="openDownloadModal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Download Template
            </button>

            <a href="{{ route('admin.graduation.showImportMapel') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-50 hover:bg-green-100 text-green-700 font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-green-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Import Mapel
            </a>

            <a href="{{ route('admin.graduation.createMapel') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Mapel
            </a>

            {{-- Di bagian Header Button --}}
            <a href="{{ route('admin.graduation.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-purple-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Input Kelulusan
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $totalMapels }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Mapel</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $totalGraduations }}</p>
                <p class="text-xs text-gray-500 font-medium">Total Data Kelulusan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-500 font-medium">Siswa Lulus</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Daftar Mapel</h2>
        </div>

        @if ($mapels->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6v6m0 0v6m0-6h6m0 0h6M6 12h6m0 0H6" />
                    </svg>
                </div>
                <p class="text-sm">Belum ada mapel. Buat mapel pertama!</p>
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
                            <th class="text-left px-6 py-3 font-semibold hidden md:table-cell">Tipe</th>
                            <th class="text-center px-6 py-3 font-semibold hidden lg:table-cell">Skor</th>
                            <th class="text-center px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($mapels as $i => $mapel)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-medium">
                                    {{ ($mapels->currentPage() - 1) * $mapels->perPage() + $i + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-[#1b84ff]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $mapel->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span
                                        class="inline-block text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg font-medium">
                                        {{ $mapel->class->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 hidden md:table-cell text-xs font-medium">
                                    {{ $mapel->expertise->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    @if ($mapel->type === 'umum')
                                        <span
                                            class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg">Umum</span>
                                    @elseif($mapel->type === 'jurusan')
                                        <span
                                            class="inline-block px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg">Jurusan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center hidden lg:table-cell">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                        {{ $mapel->score }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="#"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="#"
                                            onsubmit="return confirm('Hapus mapel {{ addslashes($mapel->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

            @if ($mapels->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $mapels->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- DAFTAR KELULUSAN SISWA --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-800">Daftar Siswa Lulus</h2>
            <span class="text-xs font-medium text-gray-500 bg-gray-50 px-3 py-1 rounded-full">
                Total: {{ $graduations->count() }} Siswa
            </span>
        </div>

        @if ($graduations->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm">Belum ada data kelulusan. Input data pertama!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold">Nama Siswa</th>
                            <th class="text-left px-6 py-3 font-semibold">No. Surat</th>
                            <th class="text-left px-6 py-3 font-semibold">Tgl Lulus</th>
                            <th class="text-center px-6 py-3 font-semibold">Jml Mapel</th>
                            <th class="text-center px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($graduations as $grad)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 font-bold text-xs">
                                            {{ strtoupper(substr($grad->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span
                                            class="font-semibold text-gray-800">{{ $grad->user->name ?? 'User Terhapus' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $grad->letter_number }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($grad->graduation_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                                        </svg>
                                        {{ $grad->mapels_count ?? $grad->mapels->count() }} Mapel
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-xs font-medium transition-colors border border-gray-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Download Template Modal -->
    <div id="downloadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDownloadModal()">
            </div>

            <!-- Modal Panel -->
            <div
                class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Download Template Kelulusan
                            </h3>

                            <div class="space-y-4">
                                <!-- Class Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Kelas (Opsional)
                                    </label>
                                    <select id="classFilter"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Semua Kelas</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Expertise Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Jurusan (Opsional)
                                    </label>
                                    <select id="expertiseFilter"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Semua Jurusan</option>
                                        @foreach ($expertise as $exp)
                                            <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-sm text-gray-500 bg-blue-50 p-3 rounded-lg">
                                    <p>💡 Template akan berisi semua siswa yang sesuai dengan filter kelas dan jurusan
                                        yang
                                        dipilih.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" onclick="downloadTemplate()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#1b84ff] text-base font-medium text-white hover:bg-[#1570e0] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Download
                    </button>
                    <button type="button" onclick="closeDownloadModal()"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDownloadModal() {
            document.getElementById('downloadModal').classList.remove('hidden');
        }

        function closeDownloadModal() {
            document.getElementById('downloadModal').classList.add('hidden');
        }

        function downloadTemplate() {
            const classId = document.getElementById('classFilter').value;
            const expertiseId = document.getElementById('expertiseFilter').value;

            let url = '{{ route('admin.graduation.downloadTemplate') }}';

            const params = new URLSearchParams();
            if (classId) params.append('class_id', classId);
            if (expertiseId) params.append('expertise_id', expertiseId);

            if (params.toString()) {
                url += '?' + params.toString();
            }

            // Trigger download
            window.location.href = url;

            closeDownloadModal();
        }

        // Close modal when pressing Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDownloadModal();
            }
        });
    </script>
@endsection
