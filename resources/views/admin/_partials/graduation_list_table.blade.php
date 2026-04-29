{{--
    resources/views/admin/_partials/graduation_list_table.blade.php

    Variables expected (passed via @include):
    - $routeIndex  : string  (e.g. 'admin.graduation.index')
    - $routeShow   : string  (e.g. 'admin.graduation.show')
    - $routeDelete : string  (e.g. 'admin.graduation.destroy')
--}}

@php
    $allGraduationsData = \App\Models\GoogleGraduation::with(['user', 'mapels'])
        ->get()
        ->map(function ($graduation) {
            $arr = $graduation->toArray();
            $arr['user_name'] = $graduation->user->full_name ?? 'User Terhapus';
            $arr['letter_number'] = $graduation->letter_number ?? '-';
            $arr['graduation_date'] = $graduation->graduation_date ?? null;
            $arr['mapel_count'] = $graduation->mapels->count();
            return $arr;
        })
        ->toArray();
@endphp

<div class="graduation-table-container" data-all-graduations="{{ json_encode($allGraduationsData) }}"
    data-route-show="{{ route($routeShow, ['id' => ':id']) }}"
    data-route-delete="{{ route($routeDelete, ['id' => ':id']) }}">

    {{-- Search + Per-page bar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex max-w-sm flex-1 items-center gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="graduationSearchInput" placeholder="Cari nama atau no. surat..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>
            <button id="graduationClearSearch" style="display:none;"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50">✕</button>
        </div>

        {{-- Total count --}}
        <p class="flex-shrink-0 text-sm text-gray-500">
            <span class="font-semibold text-gray-700" id="graduationTotalCount">{{ count($allGraduationsData) }}</span>
            data ditemukan
        </p>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div id="graduationEmptyState" style="display:none;" class="py-20 text-center text-gray-400">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="h-7 w-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm" id="graduationEmptyMessage">Belum ada data kelulusan.</p>
        </div>

        <div id="graduationTableContainer" style="display:none;" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">#</th>
                        <th class="px-6 py-3 text-left font-semibold">Nama Siswa</th>
                        <th class="hidden px-6 py-3 text-left font-semibold sm:table-cell">No. Surat</th>
                        <th class="hidden px-6 py-3 text-left font-semibold md:table-cell">Tgl Lulus</th>
                        <th class="hidden px-6 py-3 text-left font-semibold lg:table-cell">Jml Mapel</th>
                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="graduationTableBody">
                </tbody>
            </table>
        </div>

        {{-- Custom Pagination --}}
        <div id="graduationPaginationContainer" style="display:none;"
            class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row">

            {{-- Per-page selector --}}
            <div class="flex items-center gap-2">
                <select id="graduationPerPageSelect"
                    class="cursor-pointer rounded-xl border border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-500">per halaman</span>
            </div>

            {{-- Page buttons --}}
            <div class="flex items-center gap-1" id="graduationPaginationButtons"></div>

            {{-- Range info --}}
            <p class="text-xs text-gray-400">
                <span id="graduationRangeStart">1</span>–<span id="graduationRangeEnd">10</span>
                dari <span id="graduationTotalData">0</span>
            </p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.graduation-table-container');
        const allGraduationsData = JSON.parse(container.dataset.allGraduations);
        const routeShow = container.dataset.routeShow;
        const routeDelete = container.dataset.routeDelete;

        const searchInput = document.getElementById('graduationSearchInput');
        const clearSearchBtn = document.getElementById('graduationClearSearch');
        const perPageSelect = document.getElementById('graduationPerPageSelect');
        const tableBody = document.getElementById('graduationTableBody');
        const emptyState = document.getElementById('graduationEmptyState');
        const tableContainer = document.getElementById('graduationTableContainer');
        const paginationContainer = document.getElementById('graduationPaginationContainer');
        const totalCountSpan = document.getElementById('graduationTotalCount');
        const totalDataSpan = document.getElementById('graduationTotalData');
        const rangeStartSpan = document.getElementById('graduationRangeStart');
        const rangeEndSpan = document.getElementById('graduationRangeEnd');
        const paginationButtons = document.getElementById('graduationPaginationButtons');

        let currentPage = 1;
        let perPage = 10;
        let filteredData = [...allGraduationsData];

        // Restore URL params if present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) searchInput.value = urlParams.get('search');
        if (urlParams.has('per_page')) perPage = parseInt(urlParams.get('per_page'));
        perPageSelect.value = perPage;

        // ── Filter ──────────────────────────────────────────────────────────
        function filterData() {
            const search = searchInput.value.toLowerCase().trim();
            filteredData = allGraduationsData.filter(graduation => {
                const name = (graduation.user_name || '').toLowerCase();
                const letterNumber = (graduation.letter_number || '').toLowerCase();
                return name.includes(search) || letterNumber.includes(search);
            });
            currentPage = 1;
            updateDisplay();
            clearSearchBtn.style.display = search ? 'block' : 'none';
        }

        // ── Display ─────────────────────────────────────────────────────────
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
                paginationContainer.style.display = 'flex';
                renderTable();
                renderPagination();
            }
        }

        // ── Table ────────────────────────────────────────────────────────────
        function renderTable() {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const pageData = filteredData.slice(start, end);

            rangeStartSpan.textContent = start + 1;
            rangeEndSpan.textContent = Math.min(end, filteredData.length);

            pageData.forEach((graduation, idx) => {
                const rowNum = start + idx + 1;
                const row = document.createElement('tr');
                row.className = 'transition-colors hover:bg-gray-50';

                const userName = graduation.user_name ?? 'User Terhapus';
                const firstLetter = (userName ? userName.substring(0, 2) : 'NA').toUpperCase();
                const letterNumber = graduation.letter_number ?? '-';
                const mapelCount = graduation.mapel_count ?? 0;

                const graduationDate = graduation.graduation_date ? new Date(graduation
                    .graduation_date) : null;
                const formattedDate = graduationDate ?
                    graduationDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) :
                    '—';

                const detailUrl = routeShow.replace(':id', graduation.uuid);

                row.innerHTML = `
                    <td class="px-6 py-4 text-gray-400 font-medium">${rowNum}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600 text-xs font-bold text-white">
                                ${firstLetter}
                            </div>
                            <div>
                                <span class="block font-semibold text-gray-800">${userName}</span>
                            </div>
                        </div>
                    </td>
                    <td class="hidden px-6 py-4 text-gray-500 sm:table-cell">
                        <code class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-mono text-gray-600">${letterNumber}</code>
                    </td>
                    <td class="hidden px-6 py-4 text-xs text-gray-500 md:table-cell">
                        ${formattedDate}
                    </td>
                    <td class="hidden px-6 py-4 text-xs text-gray-500 lg:table-cell">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            ${mapelCount} Mapel
                        </span>
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

        // ── Pagination ───────────────────────────────────────────────────────
        function renderPagination() {
            paginationButtons.innerHTML = '';
            const lastPage = Math.ceil(filteredData.length / perPage);

            function makeNavBtn(isDisabled, direction, onClick) {
                const svgPath = direction === 'prev' ?
                    'M15 19l-7-7 7-7' :
                    'M9 5l7 7-7 7';
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

            // Prev
            paginationButtons.appendChild(
                makeNavBtn(currentPage === 1, 'prev', () => {
                    currentPage--;
                    renderTable();
                    renderPagination();
                })
            );

            // Page numbers
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

            // Next
            paginationButtons.appendChild(
                makeNavBtn(currentPage === lastPage, 'next', () => {
                    currentPage++;
                    renderTable();
                    renderPagination();
                })
            );
        }

        // ── Event listeners ──────────────────────────────────────────────────
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
