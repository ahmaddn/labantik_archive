{{--
    resources/views/admin/_partials/mapel_list_table.blade.php

    Variables expected (passed via @include):
    - $routeEdit       : string  (e.g. 'admin.graduation.editMapel')
    - $routeDelete     : string  (e.g. 'admin.graduation.destroyMapel')
    - $routeDeleteBulk : string  (e.g. 'admin.graduation.destroyMapelBulk')
--}}

@php
    $allMapelsData = \App\Models\GoogleMapel::with(['class', 'expertise'])
        ->get()
        ->map(function ($mapel) {
            $arr = $mapel->toArray();
            $arr['class_name'] = $mapel->class->name ?? '-';
            $arr['class_academic'] = $mapel->class->academic_level ?? '-';
            $arr['expertise_name'] = $mapel->expertise->name ?? '-';
            return $arr;
        })
        ->toArray();
@endphp

<div class="mapel-table-container" data-all-mapels="{{ json_encode($allMapelsData) }}"
    data-route-edit="{{ route($routeEdit, ['id' => ':id']) }}"
    data-route-delete="{{ route($routeDelete, ['id' => ':id']) }}" data-route-delete-bulk="{{ route($routeDeleteBulk) }}"
    data-route-update-order="{{ route('admin.graduation.updateMapelOrder') }}">

    {{-- Search bar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative min-w-[200px] flex-1">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="mapelSearchInput" placeholder="Cari nama mapel atau kelas..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>

            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                <select id="mapelClassFilter"
                    class="cursor-pointer appearance-none rounded-xl border border-gray-200 bg-white py-2 pl-9 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                    <option value="">Semua Kelas</option>
                </select>
            </div>

            <button id="mapelClearSearch" style="display:none;"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50">✕ Reset</button>
        </div>

        <p class="flex-shrink-0 text-sm text-gray-500">
            <span class="font-semibold text-gray-700" id="mapelTotalCount">{{ count($allMapelsData) }}</span> data ditemukan
        </p>
    </div>

    {{-- Bulk action bar --}}
    <div id="bulkActionBar"
        class="mb-3 hidden items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-red-100">
                <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-red-800">
                <span id="bulkSelectedCount" class="font-bold">0</span> mapel dipilih
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="bulkCancelBtn" onclick="clearAllCheckboxes()"
                class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-50">
                Batal
            </button>
            <button type="button" id="bulkDeleteBtn" onclick="confirmBulkDelete()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-red-700 active:scale-[0.98]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus yang Dipilih
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div id="mapelEmptyState" style="display:none;" class="py-20 text-center text-gray-400">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="h-7 w-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6.253v13m0-13C6.228 6.253 2.092 10.814 2.092 16.427c0 5.613 4.136 10.174 9.908 10.174s9.908-4.561 9.908-10.174c0-5.613-4.136-10.174-9.908-10.174z" />
                </svg>
            </div>
            <p class="text-sm">Belum ada mapel. Buat mapel pertama!</p>
        </div>

        <div id="mapelTableContainer" style="display:none;" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" id="selectAllCheckbox"
                                    class="h-4 w-4 cursor-pointer rounded border-gray-300 text-red-600 accent-red-600 focus:ring-red-500"
                                    title="Pilih semua">
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">#</th>

                        {{-- Sortable: Nama Mapel --}}
                        <th class="px-6 py-3 text-left font-semibold">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="name">
                                Nama Mapel
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="name">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        {{-- Sortable: Kelas --}}
                        <th class="hidden px-6 py-3 text-left font-semibold sm:table-cell">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="class_name">
                                Kelas
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="class_name">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        {{-- Sortable: Jurusan --}}
                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="expertise_name">
                                Jurusan
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="expertise_name">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        {{-- Sortable: Tipe --}}
                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="type">
                                Tipe
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="type">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        {{-- Sortable: Urutan --}}
                        <th class="hidden px-6 py-3 text-center font-semibold lg:table-cell">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="order">
                                Urutan
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="order">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        {{-- Sortable: Join --}}
                        <th class="hidden px-6 py-3 text-center font-semibold lg:table-cell">
                            <button type="button" class="mapel-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group" data-col="join">
                                Join
                                <span class="mapel-sort-icon text-gray-300 group-hover:text-[#1b84ff]" data-col="join">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                            </button>
                        </th>

                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="mapelTableBody"></tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="mapelPaginationContainer" style="display:none;"
            class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row">
            <div class="flex items-center gap-2">
                <select id="mapelPerPageSelect"
                    class="cursor-pointer rounded-xl border border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-500">per halaman</span>
            </div>
            <div class="flex items-center gap-1" id="mapelPaginationButtons"></div>
            <p class="text-xs text-gray-400">
                <span id="mapelRangeStart">1</span>–<span id="mapelRangeEnd">10</span>
                dari <span id="mapelTotalData">0</span>
            </p>
        </div>
    </div>

</div>

<form id="bulkDeleteForm" method="POST" style="display:none;">
    @csrf
</form>

{{-- Modal: Edit Urutan & Join --}}
<div id="orderJoinModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeOrderJoinModal()"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Urutan & Gabungan Baris</h3>
                        <p class="text-xs text-gray-500" id="modalMapelName">—</p>
                    </div>
                </div>
                <button onclick="closeOrderJoinModal()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-5">
                <div class="rounded-xl bg-blue-50 border border-blue-100 p-3.5 text-xs text-blue-700 leading-relaxed">
                    <p class="font-semibold text-blue-800 mb-1">💡 Tentang kolom ini:</p>
                    <p><strong>Urutan</strong>: Nomor urut mapel dalam surat kelulusan (1, 2, 3, …). Semakin kecil angka, semakin atas posisinya.</p>
                    <p class="mt-1"><strong>Join Baris</strong>: Kode pengelompokan mapel. Mapel-mapel dengan angka Join yang <em>sama</em> akan digabung — berbagi satu nomor & satu nilai bersama (rowspan). Gunakan angka unik (misal 0) jika mapel tidak perlu digabung.</p>
                </div>

                <div>
                    <label for="inputOrder" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Urutan <span class="text-red-500">*</span>
                        <span class="ml-1 text-xs text-gray-400 font-normal">(posisi dalam tabel nilai)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustValue('inputOrder', -1)"
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:border-gray-300 active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <input type="number" id="inputOrder" min="1" max="999" value="1"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2 text-center text-sm font-semibold text-gray-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <button type="button" onclick="adjustValue('inputOrder', 1)"
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:border-gray-300 active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="inputJoin" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Join Baris (Rowspan) <span class="text-red-500">*</span>
                        <span class="ml-1 text-xs text-gray-400 font-normal">(gabung kolom No & Nilai)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustValue('inputJoin', -1)"
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:border-gray-300 active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <input type="number" id="inputJoin" min="1" max="10" value="1"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2 text-center text-sm font-semibold text-gray-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <button type="button" onclick="adjustValue('inputJoin', 1)"
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:border-gray-300 active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    </div>

                    <div class="mt-3 overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                        <div class="px-3 py-2 text-xs font-medium text-gray-500 border-b border-gray-100">Preview rowspan:</div>
                        <div class="p-3">
                            <table class="w-full text-xs border-collapse" id="joinPreviewTable">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-200 px-2 py-1 text-center w-8">No</th>
                                        <th class="border border-gray-200 px-2 py-1 text-left">Mata Pelajaran</th>
                                        <th class="border border-gray-200 px-2 py-1 text-center w-12">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody id="joinPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                <button type="button" onclick="closeOrderJoinModal()"
                    class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" onclick="saveOrderJoin()" id="saveOrderJoinBtn"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Toast Notification ──────────────────────────────────── --}}
<div id="mapelToast"
    class="fixed bottom-6 right-6 z-[9999] hidden items-center gap-3 rounded-2xl px-5 py-3.5 shadow-2xl text-sm font-semibold text-white"
    style="transition: opacity 0.35s ease, transform 0.35s ease;">
    <svg id="mapelToastIcon" class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
    </svg>
    <span id="mapelToastMsg"></span>
</div>

<style>
    /* Sorting header styles */
    .mapel-sort-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        font-weight: 600;
        font-size: inherit;
        color: inherit;
        text-transform: inherit;
        letter-spacing: inherit;
    }

    .mapel-sort-btn.sort-active {
        color: #1b84ff;
    }

    .mapel-sort-btn.sort-active .mapel-sort-icon {
        color: #1b84ff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.mapel-table-container');
        const allMapelsData = JSON.parse(container.dataset.allMapels);
        const routeEdit = container.dataset.routeEdit;
        const routeDelete = container.dataset.routeDelete;

        const searchInput = document.getElementById('mapelSearchInput');
        const clearSearchBtn = document.getElementById('mapelClearSearch');
        const classFilter = document.getElementById('mapelClassFilter');
        const perPageSelect = document.getElementById('mapelPerPageSelect');
        const tableBody = document.getElementById('mapelTableBody');
        const emptyState = document.getElementById('mapelEmptyState');
        const tableContainer = document.getElementById('mapelTableContainer');
        const paginationContainer = document.getElementById('mapelPaginationContainer');
        const totalCountSpan = document.getElementById('mapelTotalCount');
        const totalDataSpan = document.getElementById('mapelTotalData');
        const rangeStartSpan = document.getElementById('mapelRangeStart');
        const rangeEndSpan = document.getElementById('mapelRangeEnd');
        const paginationButtons = document.getElementById('mapelPaginationButtons');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        let currentPage = 1;
        let perPage = 10;
        let filteredData = [...allMapelsData];

        // ── Sort state ───────────────────────────────────────────────────
        let sortCol = null;
        let sortDir = null;

        const iconBoth = `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>`;
        const iconAsc  = `<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4l-4 4h3v12h2V8h3L7 4zm10 16l4-4h-3V4h-2v12h-3l4 4z" opacity=".3"/><path d="M7 4l-4 4h3v12h2V8h3L7 4z"/></svg>`;
        const iconDesc = `<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4l-4 4h3v12h2V8h3L7 4z" opacity=".3"/><path d="M17 20l4-4h-3V4h-2v12h-3l4 4z"/></svg>`;

        function updateSortIcons() {
            document.querySelectorAll('.mapel-sort-btn').forEach(btn => {
                const col = btn.dataset.col;
                const iconEl = btn.querySelector('.mapel-sort-icon');
                if (col === sortCol) {
                    btn.classList.add('sort-active');
                    iconEl.innerHTML = sortDir === 'asc' ? iconAsc : iconDesc;
                } else {
                    btn.classList.remove('sort-active');
                    iconEl.innerHTML = iconBoth;
                }
            });
        }

        function applySort(data) {
            if (!sortCol || !sortDir) return data;
            return [...data].sort((a, b) => {
                let valA = a[sortCol] ?? '';
                let valB = b[sortCol] ?? '';

                // Numeric sort
                if (sortCol === 'order' || sortCol === 'join') {
                    valA = Number(valA) || 0;
                    valB = Number(valB) || 0;
                    return sortDir === 'asc' ? valA - valB : valB - valA;
                }

                // class_name: sort by academic + name combined
                if (sortCol === 'class_name') {
                    valA = String(`${a.class_academic} ${a.class_name}`).toLowerCase();
                    valB = String(`${b.class_academic} ${b.class_name}`).toLowerCase();
                } else {
                    valA = String(valA).toLowerCase();
                    valB = String(valB).toLowerCase();
                }

                if (valA < valB) return sortDir === 'asc' ? -1 : 1;
                if (valA > valB) return sortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        // Attach sort button listeners
        document.querySelectorAll('.mapel-sort-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const col = this.dataset.col;
                if (sortCol === col) {
                    if (sortDir === 'asc') {
                        sortDir = 'desc';
                    } else if (sortDir === 'desc') {
                        sortCol = null;
                        sortDir = null;
                    }
                } else {
                    sortCol = col;
                    sortDir = 'asc';
                }
                currentPage = 1;
                clearAllCheckboxes();
                updateSortIcons();
                updateDisplay();
            });
        });

        // ── Populate class filter options ────────────────────────────────────────
        const classMap = {};
        allMapelsData.forEach(mapel => {
            const key = `${mapel.class_academic} ${mapel.class_name}`.trim();
            if (key && key !== ' ' && !classMap[key]) {
                classMap[key] = { academic: mapel.class_academic, name: mapel.class_name };
            }
        });
        Object.keys(classMap).sort().forEach(label => {
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label;
            classFilter.appendChild(opt);
        });

        // ── Filter ──────────────────────────────────────────────────────────────
        function filterData() {
            const search = searchInput.value.toLowerCase().trim();
            const selectedClass = classFilter.value;

            filteredData = allMapelsData.filter(mapel => {
                const name = (mapel.name || '').toLowerCase();
                const className = (mapel.class_name || '').toLowerCase();
                const expertise = (mapel.expertise_name || '').toLowerCase();
                const type = (mapel.type || '').toLowerCase();
                const classLabel = `${mapel.class_academic} ${mapel.class_name}`.trim();

                const matchSearch = !search || name.includes(search) || className.includes(search) ||
                    expertise.includes(search) || type.includes(search);
                const matchClass = !selectedClass || classLabel === selectedClass;

                return matchSearch && matchClass;
            });

            currentPage = 1;
            clearAllCheckboxes();
            updateDisplay();

            const hasFilter = search || selectedClass;
            clearSearchBtn.style.display = hasFilter ? 'block' : 'none';
        }

        // ── Display ──────────────────────────────────────────────────────────────
        function updateDisplay() {
            const sortedData = applySort(filteredData);
            const total = sortedData.length;
            totalCountSpan.textContent = total;
            totalDataSpan.textContent = total;

            if (total === 0) {
                emptyState.style.display = 'block';
                tableContainer.style.display = 'none';
                paginationContainer.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                tableContainer.style.display = 'block';
                paginationContainer.style.display = 'flex';
                renderTable(sortedData);
                renderPagination(sortedData);
            }
        }

        // ── Table ─────────────────────────────────────────────────────────────────
        function renderTable(sortedData) {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = sortedData.slice(start, end);

            rangeStartSpan.textContent = start + 1;
            rangeEndSpan.textContent = Math.min(end, sortedData.length);

            pageData.forEach((mapel, idx) => {
                const rowNum = start + idx + 1;
                const uuid = mapel.uuid ?? mapel.id;
                const row = document.createElement('tr');
                row.className = 'transition-colors hover:bg-gray-50';
                row.dataset.uuid = uuid;

                const typeBadge = mapel.type === 'umum' ?
                    `<span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg">Umum</span>` :
                    `<span class="inline-block px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg">Jurusan</span>`;

                const orderVal = mapel.order !== null && mapel.order !== undefined ? mapel.order : '—';
                const joinVal = mapel.join !== null && mapel.join !== undefined ? mapel.join : 0;

                const editUrl = routeEdit.replace(':id', uuid);
                const deleteUrl = routeDelete.replace(':id', uuid);

                const safeName = (mapel.name || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');

                const joinBadge = joinVal == 0 ?
                    `<span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-lg bg-gray-100 px-2 text-xs font-bold text-gray-400">—</span>` :
                    `<span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-lg bg-amber-50 px-2 text-xs font-bold text-amber-700">${joinVal}</span>`;

                row.innerHTML = `
                    <td class="w-10 px-4 py-4">
                        <div class="flex items-center justify-center">
                            <input type="checkbox"
                                class="row-checkbox h-4 w-4 cursor-pointer rounded border-gray-300 text-red-600 accent-red-600 focus:ring-red-500"
                                value="${uuid}"
                                data-name="${safeName}">
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400 font-medium">${rowNum}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-gray-800">${mapel.name}</span>
                        </div>
                    </td>
                    <td class="hidden px-6 py-4 text-gray-500 sm:table-cell">
                        <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">${mapel.class_academic} ${mapel.class_name}</span>
                    </td>
                    <td class="hidden px-6 py-4 text-xs font-medium text-gray-700 md:table-cell">
                        ${mapel.expertise_name}
                    </td>
                    <td class="hidden px-6 py-4 md:table-cell">
                        ${typeBadge}
                    </td>
                    <td class="hidden px-6 py-4 text-center lg:table-cell">
                        <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-lg bg-indigo-50 px-2 text-xs font-bold text-indigo-700">${orderVal}</span>
                    </td>
                    <td class="hidden px-6 py-4 text-center lg:table-cell">
                        ${joinBadge}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                            <button type="button"
                                onclick="openOrderJoinModal('${uuid}', '${safeName}', ${mapel.order ?? 1}, ${mapel.join ?? 1})"
                                class="flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-medium text-indigo-700 transition-colors hover:bg-indigo-100"
                                title="Edit urutan & join">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                Urutan
                            </button>
                            <a href="${editUrl}"
                               class="flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <button type="button"
                                onclick="confirmDeleteMapel('${deleteUrl}', '${safeName}')"
                                class="flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-100">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            tableBody.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', onRowCheckboxChange);
            });

            // Re-apply checked state for selected UUIDs
            tableBody.querySelectorAll('.row-checkbox').forEach(cb => {
                if (selectedUuids.has(cb.value)) {
                    cb.checked = true;
                    highlightRow(cb);
                }
            });

            syncSelectAll();
        }

        // ── Pagination ────────────────────────────────────────────────────────────
        function renderPagination(sortedData) {
            paginationButtons.innerHTML = '';
            const lastPage = Math.ceil(sortedData.length / perPage);

            function makeNavBtn(isDisabled, direction, onClick) {
                const svgPath = direction === 'prev' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7';
                const svg = `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${svgPath}"/></svg>`;
                if (isDisabled) {
                    const span = document.createElement('span');
                    span.className = 'flex h-8 w-8 items-center justify-center rounded-lg text-gray-300';
                    span.innerHTML = svg;
                    return span;
                }
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50';
                btn.innerHTML = svg;
                btn.addEventListener('click', onClick);
                return btn;
            }

            paginationButtons.appendChild(makeNavBtn(currentPage === 1, 'prev', () => { currentPage--; updateDisplay(); }));

            const windowSize = 2;
            const pages = [];
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - windowSize && i <= currentPage + windowSize)) pages.push(i);
            }

            let prevPage = null;
            pages.forEach(page => {
                if (prevPage && page - prevPage > 1) {
                    const dots = document.createElement('span');
                    dots.className = 'flex h-8 items-center px-1 text-gray-400 text-xs';
                    dots.textContent = '…';
                    paginationButtons.appendChild(dots);
                }
                if (page === currentPage) {
                    const activeBtn = document.createElement('span');
                    activeBtn.className = 'flex h-8 w-8 items-center justify-center rounded-lg bg-[#1b84ff] text-xs font-bold text-white';
                    activeBtn.textContent = page;
                    paginationButtons.appendChild(activeBtn);
                } else {
                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.className = 'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 transition-colors hover:bg-blue-50 hover:text-[#1b84ff] hover:border-blue-200';
                    pageBtn.textContent = page;
                    pageBtn.addEventListener('click', () => { currentPage = page; updateDisplay(); });
                    paginationButtons.appendChild(pageBtn);
                }
                prevPage = page;
            });

            paginationButtons.appendChild(makeNavBtn(currentPage === lastPage, 'next', () => { currentPage++; updateDisplay(); }));
        }

        // ── Checkbox logic ────────────────────────────────────────────────────────
        const selectedUuids = new Set();

        function onRowCheckboxChange(e) {
            const cb = e.target;
            if (cb.checked) selectedUuids.add(cb.value);
            else selectedUuids.delete(cb.value);
            syncSelectAll();
            updateBulkBar();
            highlightRow(cb);
        }

        function syncSelectAll() {
            const allOnPage = tableBody.querySelectorAll('.row-checkbox');
            const checkedOnPage = tableBody.querySelectorAll('.row-checkbox:checked');
            if (allOnPage.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedOnPage.length === allOnPage.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedOnPage.length > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        function highlightRow(cb) {
            const row = cb.closest('tr');
            if (!row) return;
            if (cb.checked) { row.classList.add('bg-red-50'); row.classList.remove('hover:bg-gray-50'); }
            else { row.classList.remove('bg-red-50'); row.classList.add('hover:bg-gray-50'); }
        }

        function updateBulkBar() {
            const bar = document.getElementById('bulkActionBar');
            const countEl = document.getElementById('bulkSelectedCount');
            const count = selectedUuids.size;
            countEl.textContent = count;
            if (count > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
            else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
        }

        selectAllCheckbox.addEventListener('change', function() {
            const allOnPage = tableBody.querySelectorAll('.row-checkbox');
            allOnPage.forEach(cb => {
                cb.checked = this.checked;
                if (this.checked) selectedUuids.add(cb.value);
                else selectedUuids.delete(cb.value);
                highlightRow(cb);
            });
            selectAllCheckbox.indeterminate = false;
            updateBulkBar();
        });

        window.clearAllCheckboxes = function() {
            selectedUuids.clear();
            tableBody.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = false; highlightRow(cb); });
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            updateBulkBar();
        };

        window._getSelectedUuids = () => selectedUuids;

        // ── Events ───────────────────────────────────────────────────────
        searchInput.addEventListener('input', filterData);
        classFilter.addEventListener('change', filterData);
        clearSearchBtn.addEventListener('click', () => { searchInput.value = ''; classFilter.value = ''; filterData(); });
        perPageSelect.addEventListener('change', (e) => { perPage = parseInt(e.target.value); currentPage = 1; updateDisplay(); });

        updateSortIcons();
        updateDisplay();

        // ── Expose updater ke luar closure ────────────────────────────────────
        // Diperlukan oleh saveOrderJoin (yang berada di luar DOMContentLoaded)
        // agar bisa memperbarui allMapelsData & filteredData secara langsung
        // tanpa harus mem-parse ulang dari dataset (yang sudah stale).
        window._mapelUpdateItem = function(uuid, newOrder, newJoin) {
            let updated = false;
            
            // Update sumber data utama
            for (let i = 0; i < allMapelsData.length; i++) {
                if (String(allMapelsData[i].uuid ?? allMapelsData[i].id) === String(uuid)) {
                    allMapelsData[i].order = newOrder;
                    allMapelsData[i].join  = newJoin;
                    updated = true;
                    break;
                }
            }

            if (updated) {
                // Perbarui juga data di DOM dataset
                const container = document.querySelector('.mapel-table-container');
                if (container) container.dataset.allMapels = JSON.stringify(allMapelsData);
            }
            
            // Panggil filterData() yang akan memfilter ulang dari allMapelsData dan memanggil updateDisplay()
            filterData();
        };
    });

    // ── Bulk delete ───────────────────────────────────────────────────────
    function confirmBulkDelete() {
        const selected = window._getSelectedUuids ? window._getSelectedUuids() : new Set();
        if (selected.size === 0) return;

        const confirmed = confirm(`Hapus ${selected.size} mapel yang dipilih?\n\nTindakan ini tidak dapat dibatalkan.`);
        if (!confirmed) return;

        const container = document.querySelector('.mapel-table-container');
        const bulkDeleteUrl = container.dataset.routeDeleteBulk;
        const form = document.getElementById('bulkDeleteForm');
        form.action = bulkDeleteUrl;
        form.querySelectorAll('input[name="uuids[]"]').forEach(el => el.remove());
        selected.forEach(uuid => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'uuids[]';
            input.value = uuid;
            form.appendChild(input);
        });
        form.submit();
    }

    // ── Order / Join modal ────────────────────────────────────────────────
    let _currentMapelUuid = null;

    function openOrderJoinModal(uuid, name, order, join) {
        _currentMapelUuid = uuid;
        document.getElementById('modalMapelName').textContent = name;
        document.getElementById('inputOrder').value = order || 1;
        document.getElementById('inputJoin').value = join || 1;
        document.getElementById('orderJoinModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateJoinPreview();
    }

    function closeOrderJoinModal() {
        document.getElementById('orderJoinModal').classList.add('hidden');
        document.body.style.overflow = '';
        _currentMapelUuid = null;
    }

    function adjustValue(inputId, delta) {
        const input = document.getElementById(inputId);
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 1;
        let val = parseInt(input.value) || 1;
        val = Math.min(max, Math.max(min, val + delta));
        input.value = val;
        if (inputId === 'inputJoin') updateJoinPreview();
    }

    function updateJoinPreview() {
        const join = parseInt(document.getElementById('inputJoin').value) || 1;
        const tbody = document.getElementById('joinPreviewBody');
        const mapelName = document.getElementById('modalMapelName').textContent;
        tbody.innerHTML = '';

        if (join === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border border-gray-200 px-2 py-1 text-center font-bold bg-indigo-50 text-indigo-700">1</td>
                <td class="border border-gray-200 px-2 py-1">${mapelName}</td>
                <td class="border border-gray-200 px-2 py-1 text-center bg-indigo-50 text-indigo-700 font-bold">85</td>
            `;
            tbody.appendChild(tr);
        } else {
            const samplePair = [mapelName, '(mapel lain dengan Join=' + join + ')'];
            samplePair.forEach((name, i) => {
                const tr = document.createElement('tr');
                if (i === 0) {
                    tr.innerHTML = `
                        <td class="border border-gray-200 px-2 py-1 text-center align-middle font-bold bg-indigo-50 text-indigo-700" rowspan="2">1</td>
                        <td class="border border-gray-200 px-2 py-1">${name}</td>
                        <td class="border border-gray-200 px-2 py-1 text-center align-middle bg-indigo-50 text-indigo-700 font-bold" rowspan="2">85</td>
                    `;
                } else {
                    tr.innerHTML = `<td class="border border-gray-200 px-2 py-1 text-xs text-gray-400 italic">${name}</td>`;
                }
                tbody.appendChild(tr);
            });
        }
    }

    document.getElementById('inputJoin').addEventListener('input', updateJoinPreview);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOrderJoinModal(); });

    async function saveOrderJoin() {
        if (!_currentMapelUuid) return;

        const order = parseInt(document.getElementById('inputOrder').value);
        const join = parseInt(document.getElementById('inputJoin').value);
        const btn = document.getElementById('saveOrderJoinBtn');
        const container = document.querySelector('.mapel-table-container');
        const updateUrl = container.dataset.routeUpdateOrder;

        if (!order || order < 1) { alert('Urutan harus angka minimal 1'); return; }
        if (!join || join < 1) { alert('Join baris harus angka minimal 1'); return; }

        btn.disabled = true;
        btn.innerHTML = `<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan…`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            const res = await fetch(updateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ uuid: _currentMapelUuid, order, join }),
            });
            const data = await res.json();
            if (data.success) {
                // Perbarui array dalam closure & re-render langsung SEBELUM modal ditutup
                if (window._mapelUpdateItem) {
                    window._mapelUpdateItem(_currentMapelUuid, order, join);
                }
                closeOrderJoinModal(); // Menutup modal akan men-set _currentMapelUuid jadi null
                showMapelToast('Urutan & join berhasil diperbarui!');
            } else {
                alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (err) {
            alert('Gagal menyimpan: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Simpan`;
        }
    }

    // ── Toast helper ──────────────────────────────────────────────────────
    function showMapelToast(message, type = 'success') {
        const toast  = document.getElementById('mapelToast');
        const msgEl  = document.getElementById('mapelToastMsg');
        const iconEl = document.getElementById('mapelToastIcon');

        msgEl.textContent = message;
        toast.style.background = type === 'success'
            ? 'linear-gradient(135deg, #22c55e, #16a34a)'
            : 'linear-gradient(135deg, #ef4444, #dc2626)';
        iconEl.innerHTML = type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />';

        toast.style.opacity = '0';
        toast.style.transform = 'translateY(12px)';
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(12px)';
            setTimeout(() => { toast.classList.add('hidden'); toast.classList.remove('flex'); }, 350);
        }, 3000);
    }

    function confirmDeleteMapel(deleteUrl, mapelName) {
        if (!confirm(`Hapus mapel "${mapelName}"? Tindakan ini tidak dapat dibatalkan.`)) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
</script>
