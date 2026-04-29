{{--
    resources/views/admin/_partials/mapel_list_table.blade.php

    Variables expected (passed via @include):
    - $routeEdit   : string  (e.g. 'admin.graduation.editMapel')
    - $routeDelete : string  (e.g. 'admin.graduation.destroyMapel')
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
    data-route-delete="{{ route($routeDelete, ['id' => ':id']) }}">

    {{-- Search bar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex max-w-sm flex-1 items-center gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="mapelSearchInput" placeholder="Cari nama mapel atau kelas..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>
            <button id="mapelClearSearch" style="display:none;"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50">✕</button>
        </div>

        {{-- Total count --}}
        <p class="flex-shrink-0 text-sm text-gray-500">
            <span class="font-semibold text-gray-700" id="mapelTotalCount">{{ count($allMapelsData) }}</span> data
            ditemukan
        </p>
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
                        <th class="px-6 py-3 text-left font-semibold">#</th>
                        <th class="px-6 py-3 text-left font-semibold">Nama Mapel</th>
                        <th class="hidden px-6 py-3 text-left font-semibold sm:table-cell">Kelas</th>
                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">Jurusan</th>
                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">Tipe</th>
                        <th class="hidden px-6 py-3 text-center font-semibold lg:table-cell">Skor</th>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.mapel-table-container');
        const allMapelsData = JSON.parse(container.dataset.allMapels);
        const routeEdit = container.dataset.routeEdit;
        const routeDelete = container.dataset.routeDelete;

        const searchInput = document.getElementById('mapelSearchInput');
        const clearSearchBtn = document.getElementById('mapelClearSearch');
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

        let currentPage = 1;
        let perPage = 10;
        let filteredData = [...allMapelsData];

        // Restore URL params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) searchInput.value = urlParams.get('search');
        if (urlParams.has('per_page')) perPage = parseInt(urlParams.get('per_page'));
        perPageSelect.value = perPage;

        // ── Filter ──────────────────────────────────────────────────────────────
        function filterData() {
            const search = searchInput.value.toLowerCase().trim();
            filteredData = allMapelsData.filter(mapel => {
                const name = (mapel.name || '').toLowerCase();
                const className = (mapel.class_name || '').toLowerCase();
                const expertise = (mapel.expertise_name || '').toLowerCase();
                const type = (mapel.type || '').toLowerCase();
                return name.includes(search) || className.includes(search) ||
                    expertise.includes(search) || type.includes(search);
            });
            currentPage = 1;
            updateDisplay();
            clearSearchBtn.style.display = search ? 'block' : 'none';
        }

        // ── Display ──────────────────────────────────────────────────────────────
        function updateDisplay() {
            const total = filteredData.length;
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
                renderTable();
                renderPagination();
            }
        }

        // ── Table ─────────────────────────────────────────────────────────────────
        function renderTable() {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = filteredData.slice(start, end);

            rangeStartSpan.textContent = start + 1;
            rangeEndSpan.textContent = Math.min(end, filteredData.length);

            pageData.forEach((mapel, idx) => {
                const rowNum = start + idx + 1;
                const row = document.createElement('tr');
                row.className = 'transition-colors hover:bg-gray-50';

                const typeBadge = mapel.type === 'umum' ?
                    `<span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg">Umum</span>` :
                    `<span class="inline-block px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg">Jurusan</span>`;

                const score = mapel.score !== null && mapel.score !== undefined ?
                    `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">${mapel.score}</span>` :
                    `<span class="text-xs text-gray-400 italic">—</span>`;

                const editUrl = routeEdit.replace(':id', mapel.uuid ?? mapel.id);
                const deleteUrl = routeDelete.replace(':id', mapel.uuid ?? mapel.id);

                row.innerHTML = `
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
                    ${score}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="${editUrl}"
                           class="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <button type="button"
                            onclick="confirmDeleteMapel('${deleteUrl}', '${mapel.name.replace(/'/g, "\\'")}')"
                            class="flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </td>
            `;
                tableBody.appendChild(row);
            });
        }

        // ── Pagination ────────────────────────────────────────────────────────────
        function renderPagination() {
            paginationButtons.innerHTML = '';
            const lastPage = Math.ceil(filteredData.length / perPage);

            function makeNavBtn(isDisabled, direction, onClick) {
                const svgPath = direction === 'prev' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7';
                const svg =
                    `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${svgPath}"/></svg>`;

                if (isDisabled) {
                    const span = document.createElement('span');
                    span.className = 'flex h-8 w-8 items-center justify-center rounded-lg text-gray-300';
                    span.innerHTML = svg;
                    return span;
                }
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className =
                    'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50';
                btn.innerHTML = svg;
                btn.addEventListener('click', onClick);
                return btn;
            }

            paginationButtons.appendChild(
                makeNavBtn(currentPage === 1, 'prev', () => {
                    currentPage--;
                    renderTable();
                    renderPagination();
                })
            );

            const windowSize = 2;
            const pages = [];
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - windowSize && i <= currentPage +
                        windowSize)) {
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

            paginationButtons.appendChild(
                makeNavBtn(currentPage === lastPage, 'next', () => {
                    currentPage++;
                    renderTable();
                    renderPagination();
                })
            );
        }

        // ── Event listeners ───────────────────────────────────────────────────────
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

        updateDisplay();
    });

    // Delete confirmation + form submit
    function confirmDeleteMapel(deleteUrl, mapelName) {
        if (!confirm(`Hapus mapel "${mapelName}"? Tindakan ini tidak dapat dibatalkan.`)) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
        form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
        document.body.appendChild(form);
        form.submit();
    }
</script>
