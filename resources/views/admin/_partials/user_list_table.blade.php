{{--
    resources/views/admin/_partials/user_list_table.blade.php

    Variables expected (passed via @include):
    - $routeIndex  : string  (e.g. 'admin.students.index')
    - $routeShow   : string  (e.g. 'admin.students.show')
    - $identifier  : string  ('nis' | 'nip')  — kolom identitas
    - $identLabel  : string  ('NIS' | 'NIP')
    - $extraCol    : string|null — nama kolom tambahan (e.g. 'class_name') atau null
    - $extraLabel  : string|null — label kolom tambahan (e.g. 'Kelas')
    - $roleCode    : string  ('siswa' | 'guru' | 'guru-piket') — untuk filter user
--}}

@php
    // Load ALL users dengan role code tertentu + eager load relasi untuk NIP/NIS/kelas
    $allUsersData = \App\Models\User::whereHas('roles', fn($q) => $q->where('code', $roleCode ?? 'siswa'))
        ->with(['employee', 'latestStudentAcademicYear.refClass', 'refClass'])
        ->get()
        ->map(function ($user) {
            $arr = $user->toArray();
            // Tambahkan field yang dibutuhkan dari relasi
            // Use model accessors so we pick up fallbacks (ref_students.student_number, ref_classes, etc.)
            $arr['nip']        = $user->nip ?? null;
            $arr['nis']        = $user->nis ?? null;
            $arr['class_name'] = $user->class_name ?? null;
            return $arr;
        })
        ->toArray();
@endphp

<div class="user-table-container" data-all-users="{{ json_encode($allUsersData) }}" data-identifier="{{ $identifier }}"
    data-ident-label="{{ $identLabel }}" data-route-show="{{ route($routeShow, ['id' => ':id']) }}"
    data-extra-col="{{ $extraCol }}" data-extra-label="{{ $extraLabel }}">

    {{-- Search + Per-page bar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex max-w-sm flex-1 items-center gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" placeholder="Cari nama atau {{ $identLabel }}..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>
            <button id="clearSearch" style="display:none;"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50">✕</button>
        </div>

        {{-- Total count --}}
        <p class="flex-shrink-0 text-sm text-gray-500">
            <span class="font-semibold text-gray-700" id="totalCount">{{ $users->total() }}</span> data ditemukan
        </p>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div id="emptyState" style="display:none;" class="py-20 text-center text-gray-400">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="h-7 w-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <p class="text-sm" id="emptyMessage">Belum ada data.</p>
        </div>

        <div id="tableContainer" style="display:none;" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">#</th>
                        <th class="px-6 py-3 text-left font-semibold">Nama</th>
                        <th class="hidden px-6 py-3 text-left font-semibold sm:table-cell" id="identHeader">
                            {{ $identLabel }}</th>
                        <th id="extraHeader" class="hidden px-6 py-3 text-left font-semibold md:table-cell"
                            style="{{ $extraCol ? '' : 'display:none;' }}">{{ $extraLabel }}</th>
                        <th class="hidden px-6 py-3 text-left font-semibold lg:table-cell">Terdaftar</th>
                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                </tbody>
            </table>
        </div>

        {{-- ── Custom Pagination ── --}}
        <div id="paginationContainer" style="display:none;"
            class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row">

            {{-- Per-page selector --}}
            <div class="flex items-center gap-2">
                <select id="perPageSelect"
                    class="cursor-pointer rounded-xl border border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-500">per halaman</span>
            </div>

            {{-- Page buttons --}}
            <div class="flex items-center gap-1" id="paginationButtons">
            </div>

            {{-- Range info --}}
            <p class="text-xs text-gray-400">
                <span id="rangeStart">1</span>–<span id="rangeEnd">10</span> dari <span id="totalData">0</span>
            </p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.user-table-container');
        const allUsersData = JSON.parse(container.dataset.allUsers);
        const identifier = container.dataset.identifier;
        const identLabel = container.dataset.identLabel;
        const routeShow = container.dataset.routeShow;
        const extraCol = container.dataset.extraCol;
        const extraLabel = container.dataset.extraLabel;

        let searchInput = document.getElementById('searchInput');
        let clearSearchBtn = document.getElementById('clearSearch');
        let perPageSelect = document.getElementById('perPageSelect');
        let tableBody = document.getElementById('tableBody');
        let emptyState = document.getElementById('emptyState');
        let tableContainer = document.getElementById('tableContainer');
        let paginationContainer = document.getElementById('paginationContainer');
        let totalCountSpan = document.getElementById('totalCount');
        let totalDataSpan = document.getElementById('totalData');
        let rangeStartSpan = document.getElementById('rangeStart');
        let rangeEndSpan = document.getElementById('rangeEnd');
        let paginationButtons = document.getElementById('paginationButtons');

        let currentPage = 1;
        let perPage = 10;
        let filteredData = [...allUsersData];

        // Restore URL params if present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) searchInput.value = urlParams.get('search');
        if (urlParams.has('per_page')) perPage = parseInt(urlParams.get('per_page'));

        perPageSelect.value = perPage;

        function filterData() {
            const search = searchInput.value.toLowerCase().trim();
            filteredData = allUsersData.filter(user => {
                const name = (user.name || '').toLowerCase();
                const identValue = (user[identifier] || '').toLowerCase();
                const classVal  = (user['class_name'] || '').toLowerCase();
                return name.includes(search) || identValue.includes(search) || classVal.includes(search);
            });
            currentPage = 1;
            updateDisplay();
            clearSearchBtn.style.display = search ? 'block' : 'none';
        }

        function updateDisplay() {
            const totalFiltered = filteredData.length;
            totalCountSpan.textContent = totalFiltered;
            totalDataSpan.textContent = totalFiltered;

            if (totalFiltered === 0) {
                emptyState.style.display = 'block';
                tableContainer.style.display = 'none';
                paginationContainer.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                tableContainer.style.display = 'block';
                paginationContainer.style.display = filteredData.length > 0 ? 'flex' : 'none';
                renderTable();
                renderPagination();
            }
        }

        function renderTable() {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = filteredData.slice(start, end);

            rangeStartSpan.textContent = start + 1;
            rangeEndSpan.textContent = Math.min(end, filteredData.length);

            pageData.forEach((user, idx) => {
                const rowNum = start + idx + 1;
                const row = document.createElement('tr');
                row.className = 'transition-colors hover:bg-gray-50';

                let extraColHtml = '';
                if (extraCol) {
                    const extraVal = user[extraCol] ?? '—';
                    extraColHtml = `
                    <td class="hidden px-6 py-4 text-gray-500 md:table-cell">
                        <span class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-mono text-gray-600">${extraVal}</span>
                    </td>
                `;
                }

                const createdAt = new Date(user.created_at);
                const formattedDate = createdAt.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                });

                const identVal = user[identifier] ?? null;
                const identDisplay = identVal
                    ? `<code class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-mono text-gray-600">${identVal}</code>`
                    : `<span class="text-xs text-gray-400 italic">—</span>`;

                const detailUrl = routeShow.replace(':id', user.id);

                row.innerHTML = `
                <td class="px-6 py-4 text-gray-400 font-medium">${rowNum}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-xs font-bold text-white">
                            ${(user.name ? user.name.substring(0, 2) : 'N/A').toUpperCase()}
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-800">${user.name}</span>
                            <span class="text-xs text-gray-400">${user.email}</span>
                        </div>
                    </div>
                </td>
                <td class="hidden px-6 py-4 text-gray-500 sm:table-cell">
                    ${identDisplay}
                </td>
                ${extraColHtml}
                <td class="hidden px-6 py-4 text-xs text-gray-500 lg:table-cell">
                    ${formattedDate}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center">
                        <a href="${detailUrl}"
                           class="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>
                    </div>
                </td>
            `;
                tableBody.appendChild(row);
            });
        }

        function renderPagination() {
            paginationButtons.innerHTML = '';
            const lastPage = Math.ceil(filteredData.length / perPage);

            // Prev button
            if (currentPage === 1) {
                const prevBtn = document.createElement('span');
                prevBtn.className = 'flex h-8 w-8 items-center justify-center rounded-lg text-gray-300';
                prevBtn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
                paginationButtons.appendChild(prevBtn);
            } else {
                const prevBtn = document.createElement('button');
                prevBtn.type = 'button';
                prevBtn.className =
                    'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50';
                prevBtn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
                prevBtn.addEventListener('click', () => {
                    currentPage--;
                    renderTable();
                    renderPagination();
                });
                paginationButtons.appendChild(prevBtn);
            }

            // Page numbers
            const windowSize = 2;
            const pages = [];
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - windowSize && i <= currentPage + windowSize)) {
                    pages.push(i);
                }
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
                    activeBtn.className =
                        'flex h-8 w-8 items-center justify-center rounded-lg bg-[#1b84ff] text-xs font-bold text-white';
                    activeBtn.textContent = page;
                    paginationButtons.appendChild(activeBtn);
                } else {
                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.className =
                        'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 transition-colors hover:bg-blue-50 hover:text-[#1b84ff] hover:border-blue-200';
                    pageBtn.textContent = page;
                    pageBtn.addEventListener('click', () => {
                        currentPage = page;
                        renderTable();
                        renderPagination();
                    });
                    paginationButtons.appendChild(pageBtn);
                }
                prevPage = page;
            });

            // Next button
            if (currentPage === lastPage) {
                const nextBtn = document.createElement('span');
                nextBtn.className = 'flex h-8 w-8 items-center justify-center rounded-lg text-gray-300';
                nextBtn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                paginationButtons.appendChild(nextBtn);
            } else {
                const nextBtn = document.createElement('button');
                nextBtn.type = 'button';
                nextBtn.className =
                    'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50';
                nextBtn.innerHTML =
                    '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                nextBtn.addEventListener('click', () => {
                    currentPage++;
                    renderTable();
                    renderPagination();
                });
                paginationButtons.appendChild(nextBtn);
            }
        }

        // Event listeners
        searchInput.addEventListener('input', filterData);
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterData();
        });

        perPageSelect.addEventListener('change', (e) => {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            updateDisplay();
        });

        // Initial render
        updateDisplay();
    });
</script>
