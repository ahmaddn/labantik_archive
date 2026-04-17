@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Data Guru</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data guru dan lihat dokumen portofolio mereka.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50">
                <svg class="h-6 w-6 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $users->total() }}</p>
                <p class="text-xs font-medium text-gray-500">Total Guru</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-green-50">
                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">
                    {{ \App\Models\GoogleDriveFile::whereHas('user', fn($q) => $q->whereHas('roles', fn($r) => $r->where('code', 'guru')))->count() }}
                </p>
                <p class="text-xs font-medium text-gray-500">Total Dokumen</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-orange-50">
                <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $users->currentPage() }} / {{ $users->lastPage() }}</p>
                <p class="text-xs font-medium text-gray-500">Halaman</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    @include('admin._partials.user_list_table', [
        'roleCode'    => 'guru',
        'routeIndex'  => 'admin.teachers.index',
        'routeShow'   => 'admin.teachers.show',
        'identifier'  => 'nip',
        'identLabel'  => 'NIP',
        'extraCol'    => null,
        'extraLabel'  => null,
    ])
@endsection
