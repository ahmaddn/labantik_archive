@extends('layouts.app')
@section('title', 'Manajemen Sertifikat')
@section('page-title', 'Sertifikat')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Sertifikat Dinamis</h1>
            <p class="text-gray-500 text-sm mt-1">Buat, kelola template sertifikat, dan atur attachment role pengguna.</p>
        </div>

        <a href="{{ route('admin.certificates.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Sertifikat Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Nama Sertifikat</th>
                        <th class="px-6 py-4">Orientasi</th>
                        <th class="px-6 py-4">Target Role</th>
                        <th class="px-6 py-4">Penandatangan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($certificates as $cert)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <div>{{ $cert->title }}</div>
                                @if($cert->certificate_number_format)
                                    <span class="text-xs font-mono text-gray-500">Format: {{ $cert->certificate_number_format }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $cert->orientation == 'landscape' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($cert->orientation) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($cert->recipient_type == 'narasumber')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-semibold inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Narasumber: {{ $cert->custom_recipient_name }}
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($cert->roles as $role)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-md text-xs font-medium">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-xs italic">Semua Role Peserta</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-700">
                                <div><strong class="text-gray-900">Ttd 1:</strong> {{ $cert->signer1Employee ? $cert->signer1Employee->full_name : '-' }}</div>
                                @if($cert->show_back_page)
                                    <div><strong class="text-gray-900">Ttd 2:</strong> {{ $cert->signer2Employee ? $cert->signer2Employee->full_name : '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($cert->status == 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.certificates.preview', $cert->id) }}" target="_blank"
                                       class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-xs transition-colors flex items-center gap-1"
                                       title="Preview Sertifikat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Preview
                                    </a>

                                    <a href="{{ route('admin.certificates.edit', $cert->id) }}"
                                       class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium rounded-lg text-xs transition-colors flex items-center gap-1"
                                       title="Edit Sertifikat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.certificates.destroy', $cert->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-medium rounded-lg text-xs transition-colors flex items-center gap-1"
                                                title="Hapus Sertifikat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-base font-semibold text-gray-600 mb-1">Belum ada sertifikat yang dibuat</p>
                                <p class="text-xs text-gray-400 mb-4">Klik tombol "Buat Sertifikat Baru" untuk membuat sertifikat dinamis pertama Anda.</p>
                                <a href="{{ route('admin.certificates.create') }}" class="inline-flex items-center px-4 py-2 bg-[#1b84ff] text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-[#1570e0]">
                                    Buat Sertifikat Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
@endsection
