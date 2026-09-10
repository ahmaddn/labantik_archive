@extends('layouts.app')
@section('title', 'Sertifikat Saya')
@section('page-title', 'Sertifikat Saya')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Riwayat Sertifikat Saya</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar sertifikat resmi yang diterbitkan untuk akun Anda ({{ $user->name }}).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($certificates as $cert)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col justify-between">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $cert->orientation == 'landscape' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($cert->orientation) }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">{{ $cert->created_at->format('d M Y') }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $cert->title }}</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Penandatangan: {{ $cert->signer1Employee ? $cert->signer1Employee->full_name : 'Kepala Sekolah' }}
                    </p>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs text-gray-600 space-y-1">
                        <div><strong class="text-gray-800">Nama Penerima:</strong> {{ $cert->parsePlaceholder('{nama}', $user) }}</div>
                        @if($user->isSiswa())
                            <div><strong class="text-gray-800">NIS/NISN:</strong> {{ $user->nis ?? '-' }} / {{ $user->nisn ?? '-' }}</div>
                            <div><strong class="text-gray-800">Kelas:</strong> {{ $user->class_name ?? '-' }}</div>
                        @else
                            <div><strong class="text-gray-800">NIP:</strong> {{ $user->nip ?? '-' }}</div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center gap-3">
                    <a href="{{ route('user.certificates.show', $cert->id) }}" target="_blank"
                       class="flex-1 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white text-center text-xs font-semibold rounded-xl transition-colors shadow-sm shadow-blue-100 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat / Cetak PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-base font-bold text-gray-700 mb-1">Belum Ada Sertifikat</h3>
                <p class="text-xs text-gray-400">Saat ini belum ada sertifikat yang di-attach untuk role akun Anda.</p>
            </div>
        @endforelse
    </div>

    @if($certificates->hasPages())
        <div class="mt-6">
            {{ $certificates->links() }}
        </div>
    @endif
@endsection
