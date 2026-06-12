@extends('layouts.app')
@php $hide_global_alerts = true; @endphp
@section('title', 'Manajemen Nomor Ijazah')
@section('page-title', 'Nomor Ijazah')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div class="shrink-0">
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Manajemen Nomor Ijazah</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola dan import nomor ijazah untuk masing-masing siswa.</p>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.graduation.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        
        <a href="{{ route('admin.graduation.ijazah.export') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-50 hover:bg-green-100 text-green-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-green-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Template Excel
        </a>

        <button onclick="openImportModal()"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Import Excel
        </button>
    </div>
</div>

{{-- Notifikasi --}}
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <div class="text-sm font-medium">{!! session('success') !!}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <div class="text-sm font-medium">{!! session('error') !!}</div>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-gray-50/30">
        <h2 class="text-base font-bold text-gray-800 shrink-0">Daftar Nomor Ijazah Siswa</h2>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
            <form method="GET" action="{{ route('admin.graduation.ijazah.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 bg-white">
                </div>

                {{-- Class Filter --}}
                <div class="relative w-full sm:w-auto shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <select name="class_id" onchange="this.form.submit()" 
                        class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white appearance-none cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>{{ $c->academic_level }} {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="relative w-full sm:w-auto shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                        class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="filled" {{ $statusFilter === 'filled' ? 'selected' : '' }}>Sudah Terisi</option>
                        <option value="empty" {{ $statusFilter === 'empty' ? 'selected' : '' }}>Belum Terisi</option>
                    </select>
                </div>
            </form>

            <button onclick="saveAll()" id="btnSaveAll" class="hidden shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-sm shadow-blue-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Simpan</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS / NISN</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Ijazah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="ijazahTableBody">
                @forelse($graduations as $index => $grad)
                    @php
                        $student = $grad->user;
                        $latestYear = $student->academicYears->first();
                        $className = $latestYear?->class ? ($latestYear->class->academic_level . ' ' . $latestYear->class->name) : '-';
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $student->full_name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $student->student_number }} / {{ $student->national_student_number }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $className }}</td>
                        <td class="px-6 py-4">
                            <input type="text" 
                                class="ijazah-input w-full md:w-64 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                data-id="{{ $student->id }}"
                                value="{{ $student->diploma_number }}"
                                placeholder="Belum ada nomor"
                                onchange="markChanged(this)">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data siswa lulusan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($graduations->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $graduations->links() }}
    </div>
    @endif
</div>

{{-- MODAL IMPORT --}}
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-gray-900">Import Nomor Ijazah</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.graduation.ijazah.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-4">Pastikan Anda menggunakan format file Excel yang diunduh dari tombol <strong>Download Template Excel</strong>.</p>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl relative hover:bg-gray-50 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload file excel</span>
                                    <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">XLSX, XLS, CSV up to 5MB</p>
                        </div>
                    </div>
                    <div id="file-name" class="mt-2 text-sm text-center text-gray-700 font-medium hidden"></div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors text-sm">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let changedInputs = new Set();

    function markChanged(input) {
        changedInputs.add(input);
        document.getElementById('btnSaveAll').classList.remove('hidden');
    }

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function updateFileName(input) {
        const fileNameElement = document.getElementById('file-name');
        if (input.files.length > 0) {
            fileNameElement.textContent = input.files[0].name;
            fileNameElement.classList.remove('hidden');
        } else {
            fileNameElement.classList.add('hidden');
        }
    }

    function saveAll() {
        if (changedInputs.size === 0) return;

        const students = Array.from(changedInputs).map(input => ({
            id: input.getAttribute('data-id'),
            diploma_number: input.value
        }));

        const btn = document.getElementById('btnSaveAll');
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`;

        fetch("{{ route('admin.graduation.ijazah.updateBulk') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ students: students })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                changedInputs.clear();
                btn.classList.add('hidden');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message || 'Terjadi kesalahan sistem'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Simpan Perubahan</span>`;
        });
    }
</script>
@endsection
