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

<div class="ijazah-table-container bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" data-all-graduations="{{ json_encode($allGraduationsData) }}">
    <div class="px-6 py-5 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-gray-50/30">
        <h2 class="text-base font-bold text-gray-800 shrink-0">Daftar Nomor Ijazah Siswa</h2>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" placeholder="Cari nama, NIS..." 
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 bg-white">
            </div>

            {{-- Class Filter --}}
            <div class="relative w-full sm:w-auto shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <select id="classFilter" 
                    class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white appearance-none cursor-pointer">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->academic_level }} {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="relative w-full sm:w-auto shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <select id="statusFilter" 
                    class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="filled">Sudah Terisi</option>
                    <option value="empty">Belum Terisi</option>
                </select>
            </div>

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
                {{-- Data will be populated by JS --}}
            </tbody>
        </table>
        
        <div id="emptyState" style="display:none;" class="py-12 text-center text-gray-500">
            Tidak ada data siswa yang sesuai dengan filter.
        </div>
    </div>
    
    {{-- Pagination Container --}}
    <div id="paginationContainer" style="display:none;" class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row">
        <div class="flex items-center gap-2">
            <select id="perPageSelect" class="cursor-pointer rounded-xl border border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500">per halaman</span>
        </div>
        <div class="flex items-center gap-1 flex-wrap justify-center" id="paginationButtons"></div>
        <p class="text-xs text-gray-400 whitespace-nowrap">
            <span id="rangeStart">1</span>–<span id="rangeEnd">10</span> dari <span id="totalData">0</span>
        </p>
    </div>
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
    let allData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 50;

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.ijazah-table-container');
        if(container && container.dataset.allGraduations) {
            allData = JSON.parse(container.dataset.allGraduations);
            filteredData = [...allData];
            initFilters();
            renderTable();
        }
    });

    function initFilters() {
        const searchInput = document.getElementById('searchInput');
        const classFilter = document.getElementById('classFilter');
        const statusFilter = document.getElementById('statusFilter');
        const perPageSelect = document.getElementById('perPageSelect');

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilters();
            });
        }

        if(classFilter) {
            classFilter.addEventListener('change', function() {
                currentPage = 1;
                applyFilters();
            });
        }

        if(statusFilter) {
            statusFilter.addEventListener('change', function() {
                currentPage = 1;
                applyFilters();
            });
        }

        if(perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                itemsPerPage = parseInt(this.value);
                currentPage = 1;
                renderTable();
            });
        }
    }

    function applyFilters() {
        const searchVal = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const classVal = document.getElementById('classFilter')?.value || '';
        const statusVal = document.getElementById('statusFilter')?.value || '';

        filteredData = allData.filter(item => {
            // Search
            const matchSearch = !searchVal || 
                (item.full_name && item.full_name.toLowerCase().includes(searchVal)) ||
                (item.student_number && item.student_number.toLowerCase().includes(searchVal)) ||
                (item.national_student_number && item.national_student_number.toLowerCase().includes(searchVal)) ||
                (item.diploma_number && item.diploma_number.toLowerCase().includes(searchVal));

            // Class
            const matchClass = !classVal || String(item.class_id) === String(classVal);

            // Status
            let matchStatus = true;
            if (statusVal === 'filled') {
                matchStatus = item.diploma_number && item.diploma_number.trim() !== '';
            } else if (statusVal === 'empty') {
                matchStatus = !item.diploma_number || item.diploma_number.trim() === '';
            }

            return matchSearch && matchClass && matchStatus;
        });

        renderTable();
    }

    function renderTable() {
        const tbody = document.getElementById('ijazahTableBody');
        const emptyState = document.getElementById('emptyState');
        const paginationContainer = document.getElementById('paginationContainer');

        if (!tbody) return;

        tbody.innerHTML = '';

        if (filteredData.length === 0) {
            emptyState.style.display = 'block';
            paginationContainer.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        paginationContainer.style.display = 'flex';

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
        const currentData = filteredData.slice(startIndex, endIndex);

        currentData.forEach((item, index) => {
            const actualIndex = startIndex + index + 1;
            const diplomaNumber = item.diploma_number || '';
            const className = item.class_name || '-';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50';
            tr.innerHTML = `
                <td class="px-6 py-4 text-gray-500">${actualIndex}</td>
                <td class="px-6 py-4 font-medium text-gray-900">${item.full_name || '-'}</td>
                <td class="px-6 py-4 text-gray-500">${item.student_number || '-'} / ${item.national_student_number || '-'}</td>
                <td class="px-6 py-4 text-gray-500">${className}</td>
                <td class="px-6 py-4">
                    <input type="text" 
                        class="ijazah-input w-full md:w-64 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        data-id="${item.student_id || item.user_id}"
                        value="${diplomaNumber}"
                        placeholder="Belum ada nomor"
                        onchange="markChanged(this)">
                </td>
            `;
            tbody.appendChild(tr);
        });

        updatePaginationUI();
    }

    function updatePaginationUI() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const paginationButtons = document.getElementById('paginationButtons');
        
        document.getElementById('totalData').textContent = filteredData.length;
        document.getElementById('rangeStart').textContent = filteredData.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage) + 1;
        document.getElementById('rangeEnd').textContent = Math.min(currentPage * itemsPerPage, filteredData.length);

        if (!paginationButtons) return;
        paginationButtons.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev Button
        const prevBtn = createPageButton('‹', currentPage - 1, currentPage === 1);
        paginationButtons.appendChild(prevBtn);

        // Page Numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            paginationButtons.appendChild(createPageButton(1, 1, false, currentPage === 1));
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 py-1 text-gray-400 text-sm';
                ellipsis.textContent = '...';
                paginationButtons.appendChild(ellipsis);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationButtons.appendChild(createPageButton(i, i, false, currentPage === i));
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 py-1 text-gray-400 text-sm';
                ellipsis.textContent = '...';
                paginationButtons.appendChild(ellipsis);
            }
            paginationButtons.appendChild(createPageButton(totalPages, totalPages, false, currentPage === totalPages));
        }

        // Next Button
        const nextBtn = createPageButton('›', currentPage + 1, currentPage === totalPages);
        paginationButtons.appendChild(nextBtn);
    }

    function createPageButton(text, pageTarget, disabled = false, active = false) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = text;
        btn.disabled = disabled;
        
        let baseClass = 'min-w-[32px] h-8 flex items-center justify-center rounded-lg text-sm transition-colors ';
        
        if (disabled) {
            baseClass += 'text-gray-300 cursor-not-allowed';
        } else if (active) {
            baseClass += 'bg-blue-50 text-blue-600 font-semibold border border-blue-200';
        } else {
            baseClass += 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-transparent';
        }
        
        btn.className = baseClass;
        
        if (!disabled && !active) {
            btn.onclick = () => {
                currentPage = pageTarget;
                renderTable();
            };
        }
        
        return btn;
    }

    function markChanged(input) {
        changedInputs.add(input);
        document.getElementById('btnSaveAll').classList.remove('hidden');
        
        // Update in memory array so pagination doesn't lose value
        const studentId = input.getAttribute('data-id');
        const newVal = input.value;
        const item = allData.find(i => String(i.student_id || i.user_id) === String(studentId));
        if(item) item.diploma_number = newVal;
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
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Simpan</span>`;
        });
    }
</script>
@endsection
