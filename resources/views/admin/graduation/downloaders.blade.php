@extends('layouts.app')
@section('title', 'Daftar Siswa Mendownload')
@section('page-title', 'Siswa Mendownload')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="shrink-0">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Siswa Yang Mendownload Dokumen</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar siswa yang telah mencetak/mendownload dokumen kelulusan.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            {{-- Search Form --}}
            <form action="{{ route('admin.graduation.downloaders') }}" method="GET" class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition-colors shadow-sm">
            </form>

            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center justify-center gap-2 px-3 py-2 sm:px-5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @if($downloaders->isEmpty())
            <div class="text-center py-10">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada siswa yang mendownload dokumen.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Jumlah Download</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Terakhir Print</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($downloaders as $i => $dl)
                            <tr class="hover:bg-orange-50 transition-colors">
                                <td class="px-6 py-4 text-gray-500">{{ $downloaders->firstItem() + $i }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-bold text-orange-600">
                                                {{ strtoupper(substr($dl->user->name ?? '?', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $dl->user->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                        {{ $dl->print_count >= 5 ? 'bg-red-100 text-red-700' : ($dl->print_count >= 3 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        {{ $dl->print_count }}x
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    @if($dl->last_print_at)
                                        {{ \Carbon\Carbon::parse($dl->last_print_at)->translatedFormat('d M Y, H:i') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($dl->updated_at)->translatedFormat('d M Y, H:i') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $downloaders->links() }}
            </div>
        @endif
    </div>
@endsection
