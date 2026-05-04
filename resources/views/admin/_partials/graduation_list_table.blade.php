{{--
    resources/views/admin/_partials/graduation_list_table.blade.php

    Variables expected (passed via @include):
    - $routeIndex  : string  (e.g. 'admin.graduation.index')
    - $routeShow   : string  (e.g. 'admin.graduation.show')
    - $routeDelete : string  (e.g. 'admin.graduation.destroy')
--}}

@php
    $items = isset($graduations) ? $graduations : \App\Models\GoogleGraduation::with(['user.graduationStatement', 'mapels', 'user.academicYears.class'])->get();
    
    $allGraduationsData = $items->map(function ($graduation) {
            $arr = $graduation->toArray();
            $arr['user_name'] = $graduation->user->full_name ?? 'User Terhapus';
            $arr['letter_number'] = $graduation->letter->letter_number ?? '-';
            $arr['transcript_letter_number'] = $graduation->letter->transcript_letter_number ?? '-';
            $arr['graduation_date'] = $graduation->letter->graduation_date ?? null;
            $arr['mapel_count'] = $graduation->mapels->count();
            $arr['has_signature'] = $graduation->user->graduationStatement?->signature_id ? true : false;

            // Ambil kelas dari academic year terbaru
            $latestYear = $graduation->user->academicYears->first();
            $arr['class_id'] = $latestYear?->class_id;
            $arr['class_name'] = $latestYear?->class
                ? $latestYear->class->academic_level . ' ' . $latestYear->class->name
                : '-';

            return $arr;
        })
        ->toArray();
@endphp

<div class="graduation-table-container" data-all-graduations="{{ json_encode($allGraduationsData) }}"
    data-route-show="{{ route($routeShow, ['id' => ':id']) }}"
    data-route-delete="{{ route($routeDelete, ['id' => ':id']) }}"
    data-route-surat-kelulusan="{{ route('admin.graduation.showSuratKelulusan', ['id' => ':id']) }}"
    data-route-surat-pernyataan="{{ route('admin.graduation.showSuratPernyataan', ['id' => ':id']) }}"
    data-route-transkrip-nilai="{{ route('admin.graduation.showTranskripNilai', ['id' => ':id']) }}"
    data-route-generate-tokens="{{ route('admin.graduation.generateTokens') }}"
    data-route-generate-tokens-class="{{ route('admin.graduation.generateTokensClass') }}"
    data-route-generate-token-student="{{ route('admin.graduation.generateTokenStudent') }}">

    {{-- Search + controls bar --}}
    <div class="mb-4 flex flex-col gap-3">
        {{-- Search row --}}
        <div class="flex flex-col sm:flex-row items-center gap-2">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="graduationSearchInput" placeholder="Cari nama atau no. surat..."
                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-4 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
            </div>

            <div class="relative w-full sm:w-48">
                <select id="classFilterSelect"
                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none focus:ring-1 focus:ring-[#1b84ff]">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->academic_level }} {{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <button id="graduationClearSearch" style="display:none;"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-500 transition-colors hover:bg-gray-50 flex-shrink-0">✕</button>
        </div>

        {{-- Count row + Export Surat --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <p class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-700"
                        id="graduationTotalCount">{{ count($allGraduationsData) }}</span>
                    data ditemukan
                </p>

                {{-- Global Signature Mode Selector --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mode TTD:</span>
                    <select id="globalSigMode" onchange="updateAllExportLinks()"
                        class="text-xs border-none focus:ring-0 cursor-pointer text-gray-700 font-semibold bg-transparent p-0 pr-6">
                        <option value="none">Polos (Tanpa TTD)</option>
                        <option value="sig">Hanya Tanda Tangan</option>
                        <option value="both" selected>Lengkap (TTD + Stempel)</option>
                    </select>
                </div>
            </div>

            {{-- Token Actions + Export Surat --}}
            <div class="flex items-center gap-2 flex-wrap" id="suratExportGroup">

                {{-- ── Generate Token ─────────────────────────────────── --}}
                <button id="btnGenerateTokenClass" onclick="doGenerateTokensClass()"
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-amber-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Generate Token Kelas</span>
                </button>

                <button id="btnGenerateTokensAll" onclick="doGenerateTokens()"
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-amber-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Generate Token Semua</span>
                </button>

                {{-- ── Export Token Excel ──────────────────────────────── --}}
                <a href="{{ route('admin.graduation.exportTokens') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export Token</span>
                </a>

                {{-- ── Surat Kelulusan ───────────────────────────────── --}}
                <div class="relative" id="dropdownWrapperKelulusan">
                    <button onclick="toggleExportDropdown('kelulusan')"
                        class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Surat Kelulusan</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownKelulusan"
                        class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 p-3 space-y-1">
                        <div class="px-1 pb-2 border-b border-gray-100 mb-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mode Tanda Tangan</p>
                            <select id="sigModeKelulusan" 
                                class="w-full text-xs rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-1.5">
                                <option value="none">Polos (Tanpa TTD)</option>
                                <option value="sig">Hanya Tanda Tangan</option>
                                <option value="both" selected>Lengkap (TTD + Stempel)</option>
                            </select>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1 pb-1">Export sebagai
                        </p>

                        <button onclick="doExport('kelulusan', 'all')"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Semua
                        </button>

                        <div>
                            <button onclick="toggleSubFilter('kelulusan','jurusan')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Per Jurusan
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowKelulusanJurusan" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subKelulusanJurusan" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectKelulusanJurusan"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-200">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($expertise as $exp)
                                        <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('kelulusan', 'jurusan')"
                                    class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>

                        <div>
                            <button onclick="toggleSubFilter('kelulusan','kelas')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Per Kelas
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowKelulusanKelas" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subKelulusanKelas" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectKelulusanKelas"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-200">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->academic_level }}
                                            {{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('kelulusan', 'kelas')"
                                    class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Surat Pernyataan ───────────────────────────────── --}}
                <div class="relative" id="dropdownWrapperPernyataan">
                    <button onclick="toggleExportDropdown('pernyataan')"
                        class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-purple-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Surat Pernyataan</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownPernyataan"
                        class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 p-3 space-y-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1 pb-1">Export
                            sebagai</p>

                        <button onclick="doExport('pernyataan', 'all')"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Semua
                        </button>

                        <div>
                            <button onclick="toggleSubFilter('pernyataan','jurusan')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Per Jurusan
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowPernyataanJurusan" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subPernyataanJurusan" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectPernyataanJurusan"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-200">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($expertise as $exp)
                                        <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('pernyataan', 'jurusan')"
                                    class="w-full py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>

                        <div>
                            <button onclick="toggleSubFilter('pernyataan','kelas')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Per Kelas
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowPernyataanKelas" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subPernyataanKelas" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectPernyataanKelas"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-200">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->academic_level }}
                                            {{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('pernyataan', 'kelas')"
                                    class="w-full py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Transkrip Nilai ───────────────────────────────── --}}
                <div class="relative" id="dropdownWrapperTranskrip">
                    <button onclick="toggleExportDropdown('transkrip')"
                        class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-xl transition-colors text-xs sm:text-sm shadow-sm border border-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Transkrip Nilai</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownTranskrip"
                        class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 p-3 space-y-1">
                        <div class="px-1 pb-2 border-b border-gray-100 mb-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mode Tanda Tangan</p>
                            <select id="sigModeTranskrip" 
                                class="w-full text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5">
                                <option value="none">Polos (Tanpa TTD)</option>
                                <option value="sig">Hanya Tanda Tangan</option>
                                <option value="both" selected>Lengkap (TTD + Stempel)</option>
                            </select>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1 pb-1">Export
                            sebagai
                        </p>

                        <button onclick="doExport('transkrip', 'all')"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Semua
                        </button>

                        <div>
                            <button onclick="toggleSubFilter('transkrip','jurusan')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Per Jurusan
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowTranskripJurusan" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subTranskripJurusan" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectTranskripJurusan"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-200">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($expertise as $exp)
                                        <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('transkrip', 'jurusan')"
                                    class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>

                        <div>
                            <button onclick="toggleSubFilter('transkrip','kelas')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.123-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Per Kelas
                                </span>
                                <svg class="w-3 h-3 transition-transform" id="arrowTranskripKelas" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="subTranskripKelas" class="hidden px-3 pt-1 pb-2 space-y-2">
                                <select id="selectTranskripKelas"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-200">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->academic_level }}
                                            {{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <button onclick="doExport('transkrip', 'kelas')"
                                    class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors">Export</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- View mode toggle (mobile only) --}}
            <div class="flex items-center gap-1 sm:hidden">
                <button id="viewModeTable" onclick="setViewMode('table')"
                    class="p-1.5 rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-colors view-mode-btn active-view"
                    title="Tampilan Tabel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                    </svg>
                </button>
                <button id="viewModeCard" onclick="setViewMode('card')"
                    class="p-1.5 rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition-colors view-mode-btn"
                    title="Tampilan Kartu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Empty state --}}
    <div id="graduationEmptyState" style="display:none;"
        class="py-16 text-center text-gray-400 rounded-2xl border border-gray-200 bg-white">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50">
            <svg class="h-7 w-7 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-sm" id="graduationEmptyMessage">Belum ada data kelulusan.</p>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE VIEW                                                    --}}
    {{-- ============================================================ --}}
    <div id="graduationTableWrapper" style="display:none;">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto -webkit-overflow-scrolling-touch">
                <table class="w-full text-sm min-w-[480px]">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            {{-- Sortable headers --}}
                            <th class="px-4 sm:px-6 py-3 text-center font-semibold">Aksi</th>
                            <th class="hidden xl:table-cell px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="token">
                                    Token
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="token">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="user_name">
                                    Nama Siswa
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="user_name">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="letter_number">
                                    No. Surat / Transkrip
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="letter_number">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>
                            <th class="hidden sm:table-cell px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="class_name">
                                    Kelas
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="class_name">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>
                            <th class="hidden md:table-cell px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="graduation_date">
                                    Tgl Lulus
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="graduation_date">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>
                            <th class="hidden lg:table-cell px-4 sm:px-6 py-3 text-left font-semibold">
                                <button type="button"
                                    class="grad-sort-btn inline-flex items-center gap-1 hover:text-[#1b84ff] transition-colors group"
                                    data-col="mapel_count">
                                    Jml Mapel
                                    <span class="grad-sort-icon text-gray-300 group-hover:text-[#1b84ff]"
                                        data-col="mapel_count">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </span>
                                </button>
                            </th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="graduationTableBody"></tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="graduationPaginationContainer" style="display:none;"
                class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-4 sm:px-6 py-4 sm:flex-row">
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
                <div class="flex items-center gap-1 flex-wrap justify-center" id="graduationPaginationButtons"></div>
                <p class="text-xs text-gray-400 whitespace-nowrap">
                    <span id="graduationRangeStart">1</span>–<span id="graduationRangeEnd">10</span>
                    dari <span id="graduationTotalData">0</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CARD VIEW (default on mobile)                                --}}
    {{-- ============================================================ --}}
    <div id="graduationCardWrapper" style="display:none;">
        <div class="space-y-3" id="graduationCardBody"></div>

        <div id="graduationCardPaginationContainer" style="display:none;"
            class="flex items-center justify-between gap-3 pt-3">
            <div class="flex items-center gap-2">
                <select id="graduationCardPerPageSelect"
                    class="cursor-pointer rounded-xl border border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-[#1b84ff] focus:outline-none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-xs text-gray-500">/ hal</span>
            </div>
            <div class="flex items-center gap-1" id="graduationCardPaginationButtons"></div>
        </div>
    </div>
</div>

<style>
    .active-view {
        background-color: #eff6ff;
        color: #1b84ff;
        border-color: #bfdbfe;
    }

    @media (min-width: 640px) {

        #viewModeCard,
        #viewModeTable {
            display: none !important;
        }
    }

    /* Sorting header styles */
    .grad-sort-btn {
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

    .grad-sort-btn.sort-active {
        color: #1b84ff;
    }

    .grad-sort-btn.sort-active .grad-sort-icon {
        color: #1b84ff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.graduation-table-container');
        const allData = JSON.parse(container.dataset.allGraduations);
        window.allGraduationsData = allData;
        const routeShow = container.dataset.routeShow;
        const routeSuratKelulusan = container.dataset.routeSuratKelulusan;
        const routeSuratPernyataan = container.dataset.routeSuratPernyataan;
        const routeTranskripNilai = container.dataset.routeTranskripNilai;

        const searchInput = document.getElementById('graduationSearchInput');
        const classFilter = document.getElementById('classFilterSelect');
        const clearBtn = document.getElementById('graduationClearSearch');
        const perPageSelect = document.getElementById('graduationPerPageSelect');

        window.checkSignature = function(e, uuid) {
            const g = window.allGraduationsData.find(item => item.uuid === uuid);
            if (g && !g.has_signature) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Belum Ada',
                    text: 'Siswa ini belum mengupload tanda tangan.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                return false;
            }
            return true;
        };
        const cardPerPage = document.getElementById('graduationCardPerPageSelect');

        const tableBody = document.getElementById('graduationTableBody');
        const cardBody = document.getElementById('graduationCardBody');
        const emptyState = document.getElementById('graduationEmptyState');
        const tableWrapper = document.getElementById('graduationTableWrapper');
        const cardWrapper = document.getElementById('graduationCardWrapper');
        const paginCont = document.getElementById('graduationPaginationContainer');
        const cardPaginCont = document.getElementById('graduationCardPaginationContainer');
        const totalCount = document.getElementById('graduationTotalCount');
        const totalData = document.getElementById('graduationTotalData');
        const rangeStart = document.getElementById('graduationRangeStart');
        const rangeEnd = document.getElementById('graduationRangeEnd');
        const paginBtns = document.getElementById('graduationPaginationButtons');
        const cardPaginBtns = document.getElementById('graduationCardPaginationButtons');

        let currentPage = 1;
        let perPage = 10;
        let filteredData = [...allData];
        let viewMode = window.innerWidth < 640 ? 'card' : 'table';

        // ── Sort state ───────────────────────────────────────────────────
        let sortCol = 'class_name'; // Default sort by class
        let sortDir = 'asc'; // Default sort direction

        // SVG icons
        const iconBoth =
            `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>`;
        const iconAsc =
            `<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4l-4 4h3v12h2V8h3L7 4zm10 16l4-4h-3V4h-2v12h-3l4 4z" opacity=".3"/><path d="M7 4l-4 4h3v12h2V8h3L7 4z"/></svg>`;
        const iconDesc =
            `<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4l-4 4h3v12h2V8h3L7 4z" opacity=".3"/><path d="M17 20l4-4h-3V4h-2v12h-3l4 4z"/></svg>`;

        function updateSortIcons() {
            document.querySelectorAll('.grad-sort-btn').forEach(btn => {
                const col = btn.dataset.col;
                const iconEl = btn.querySelector('.grad-sort-icon');
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

                // Numeric sort for mapel_count
                if (sortCol === 'mapel_count') {
                    valA = Number(valA) || 0;
                    valB = Number(valB) || 0;
                    return sortDir === 'asc' ? valA - valB : valB - valA;
                }

                // Date sort for graduation_date
                if (sortCol === 'graduation_date') {
                    valA = valA ? new Date(valA).getTime() : 0;
                    valB = valB ? new Date(valB).getTime() : 0;
                    return sortDir === 'asc' ? valA - valB : valB - valA;
                }

                // String sort
                valA = String(valA).toLowerCase();
                valB = String(valB).toLowerCase();
                if (valA < valB) return sortDir === 'asc' ? -1 : 1;
                if (valA > valB) return sortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        // Attach sort button listeners
        document.querySelectorAll('.grad-sort-btn').forEach(btn => {
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
                updateSortIcons();
                updateDisplay();
            });
        });

        // ── Helpers ──────────────────────────────────────────────────────
        function fmtDate(dateStr) {
            if (!dateStr) return '—';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function avatar(name) {
            return (name ? name.substring(0, 2) : 'NA').toUpperCase();
        }

        // ── Filter ───────────────────────────────────────────────────────
        function filterData() {
            const q = searchInput.value.toLowerCase().trim();
            const classId = classFilter.value;

            filteredData = allData.filter(g => {
                const matchesSearch = (g.user_name || '').toLowerCase().includes(q) ||
                    (g.letter_number || '').toLowerCase().includes(q);
                const matchesClass = !classId || String(g.class_id) === String(classId);

                return matchesSearch && matchesClass;
            });
            currentPage = 1;
            updateDisplay();
            clearBtn.style.display = (q || classId) ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterData);
        classFilter.addEventListener('change', filterData);

        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            classFilter.value = '';
            filterData();
        });

        // ── View mode ────────────────────────────────────────────────────
        window.setViewMode = function(mode) {
            viewMode = mode;
            document.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active-view'));
            document.getElementById(mode === 'table' ? 'viewModeTable' : 'viewModeCard').classList.add(
                'active-view');
            updateDisplay();
        };

        // ── Main render ──────────────────────────────────────────────────
        function updateDisplay() {
            const sortedData = applySort(filteredData);
            const total = sortedData.length;
            totalCount.textContent = total;
            if (totalData) totalData.textContent = total;

            const effectiveMode = window.innerWidth >= 640 ? 'table' : viewMode;

            if (total === 0) {
                emptyState.style.display = 'block';
                tableWrapper.style.display = 'none';
                cardWrapper.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';

            if (effectiveMode === 'card') {
                tableWrapper.style.display = 'none';
                cardWrapper.style.display = 'block';
                renderCards(sortedData);
            } else {
                cardWrapper.style.display = 'none';
                tableWrapper.style.display = 'block';
                paginCont.style.display = 'flex';
                renderTable(sortedData);
                renderPagination(sortedData);
            }
        }

        // ── Table render ─────────────────────────────────────────────────
        function renderTable(sortedData) {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const pageData = sortedData.slice(start, start + perPage);

            if (rangeStart) rangeStart.textContent = start + 1;
            if (rangeEnd) rangeEnd.textContent = Math.min(start + perPage, sortedData.length);

            pageData.forEach((g, idx) => {
                const row = document.createElement('tr');
                row.className = 'transition-colors hover:bg-gray-50';
                const detailUrl = routeShow.replace(':id', g.uuid);

                row.innerHTML = `
                                <td class="px-4 sm:px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="${detailUrl}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-0 w-56 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-40">
                                {{-- Opsi Surat Kelulusan --}}
                                <div class="px-3 py-2 border-b border-gray-50 bg-gray-50/50">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Surat Kelulusan</p>
                                    <div class="flex items-center gap-1">
                                        <button onclick="doSingleExport('kelulusan', '${g.uuid}', 'none')" title="Tanpa TTD"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">Polos</button>
                                        <button onclick="doSingleExport('kelulusan', '${g.uuid}', 'sig')" title="Hanya TTD"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">TTD</button>
                                        <button onclick="doSingleExport('kelulusan', '${g.uuid}', 'both')" title="TTD + Stempel"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">Lengkap</button>
                                    </div>
                                </div>

                                {{-- Surat Pernyataan (Selalu default karena TTD siswa) --}}
                                <button onclick="doSingleExport('pernyataan', '${g.uuid}')"
                                    class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50 transition-colors border-b border-gray-100">
                                    <svg class="w-3 h-3 inline-block mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Surat Pernyataan
                                </button>

                                {{-- Opsi Transkrip Nilai --}}
                                <div class="px-3 py-2 bg-gray-50/50 rounded-b-lg">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Transkrip Nilai</p>
                                    <div class="flex items-center gap-1">
                                        <button onclick="doSingleExport('transkrip', '${g.uuid}', 'none')" title="Tanpa TTD"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">Polos</button>
                                        <button onclick="doSingleExport('transkrip', '${g.uuid}', 'sig')" title="Hanya TTD"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">TTD</button>
                                        <button onclick="doSingleExport('transkrip', '${g.uuid}', 'both')" title="TTD + Stempel"
                                            class="flex-1 px-2 py-1 text-[10px] bg-white border border-gray-200 rounded-md hover:bg-indigo-50 hover:border-indigo-200 transition-colors">Lengkap</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="hidden xl:table-cell px-4 sm:px-6 py-4">
                    <div class="flex items-center gap-2">
                        ${g.token
                            ? `<code id="token-${g.uuid}" class="inline-block rounded-lg bg-amber-50 border border-amber-200 px-2.5 py-1 text-xs font-mono font-bold text-amber-700 tracking-widest">${g.token}</code>`
                            : `<span id="token-${g.uuid}" class="text-xs text-gray-400 italic">Belum ada</span>`
                        }
                        <button onclick="doGenerateTokenStudent('${g.uuid}', this)" 
                                title="Generate Token"
                                class="p-1 rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </td>
                <td class="px-4 sm:px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 sm:h-9 sm:w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600 text-xs font-bold text-white">
                            ${avatar(g.user_name)}
                        </div>
                        <span class="font-semibold text-gray-800 text-sm leading-tight">${g.user_name ?? 'User Terhapus'}</span>
                    </div>
                </td>
                <td class="px-4 sm:px-6 py-4">
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight w-8">SKL:</span>
                            <code class="rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-mono text-blue-700 break-all border border-blue-100">${g.letter_number ?? '-'}</code>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight w-8">TRN:</span>
                            <code class="rounded-lg bg-purple-50 px-2 py-0.5 text-[10px] font-mono text-purple-700 break-all border border-purple-100">${g.transcript_letter_number ?? '-'}</code>
                        </div>
                    </div>
                </td>
                <td class="hidden sm:table-cell px-4 sm:px-6 py-4">
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                        ${g.class_name ?? '-'}
                    </span>
                </td>
                <td class="hidden md:table-cell px-4 sm:px-6 py-4 text-xs text-gray-500">${fmtDate(g.graduation_date)}</td>
                <td class="hidden lg:table-cell px-4 sm:px-6 py-4">
                    <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        ${g.mapel_count ?? 0} Mapel
                    </span>
                </td>


            `;
                tableBody.appendChild(row);
            });
        }

        // ── Card render (mobile) ─────────────────────────────────────────
        function renderCards(sortedData) {
            cardBody.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const pageData = sortedData.slice(start, start + perPage);

            pageData.forEach((g) => {
                const detailUrl = routeShow.replace(':id', g.uuid);
                const card = document.createElement('div');
                card.className = 'bg-white border border-gray-200 rounded-2xl p-4 shadow-sm';

                card.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600 text-xs font-bold text-white">
                        ${avatar(g.user_name)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">${g.user_name ?? 'User Terhapus'}</p>
                        <p class="text-xs text-gray-500 mt-0.5">${fmtDate(g.graduation_date)}</p>
                    </div>
                    <a href="${detailUrl}" class="flex-shrink-0 inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                </div>
                <div class="mt-3 flex flex-col gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight">SKL:</span>
                            <code class="rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-mono text-blue-700 truncate border border-blue-100 max-w-[150px]">${g.letter_number ?? '-'}</code>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight">TRN:</span>
                            <code class="rounded-lg bg-purple-50 px-2 py-0.5 text-[10px] font-mono text-purple-700 truncate border border-purple-100 max-w-[150px]">${g.transcript_letter_number ?? '-'}</code>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">${g.class_name ?? '-'}</span>
                    <div class="flex items-center gap-2">
                        ${g.token
                            ? `<code id="token-card-${g.uuid}" class="rounded-lg bg-amber-50 border border-amber-200 px-2 py-1 text-xs font-mono font-bold text-amber-700 tracking-widest">${g.token}</code>`
                            : `<span id="token-card-${g.uuid}" class="text-xs text-gray-400 italic">Token: -</span>`
                        }
                        <button onclick="doGenerateTokenStudent('${g.uuid}', this)" 
                                title="Generate Token"
                                class="p-1 rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        ${g.mapel_count ?? 0} Mapel
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 flex-wrap">
                    <a href="${detailUrl}" class="flex-1 text-center inline-flex items-center justify-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                    <div class="relative group flex-1">
                        <button class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition-colors">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Surat
                        </button>
                        <div class="absolute left-0 mt-0 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-40">
                            <a href="${routeSuratKelulusan.replace(':id', g.uuid)}" target="_blank"
                                class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50 transition-colors first:rounded-t-lg">
                                <svg class="w-3 h-3 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Surat Kelulusan
                            </a>
                            <a href="${routeSuratPernyataan.replace(':id', g.uuid)}" target="_blank"
                                onclick="return checkSignature(event, '${g.uuid}')"
                                class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50 transition-colors last:rounded-b-lg border-t border-gray-100">
                                <svg class="w-3 h-3 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Surat Pernyataan
                            </a>
                            <a href="${routeTranskripNilai.replace(':id', g.uuid)}" target="_blank"
                                class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50 transition-colors last:rounded-b-lg border-t border-gray-100">
                                <svg class="w-3 h-3 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Transkrip Nilai
                            </a>
                        </div>
                    </div>
                </div>
            `;
                cardBody.appendChild(card);
            });

            const lastPage = Math.ceil(sortedData.length / perPage);
            if (lastPage > 1) {
                cardPaginCont.style.display = 'flex';
                renderCardPagination(lastPage);
            } else {
                cardPaginCont.style.display = 'none';
            }
        }

        // ── Pagination helpers ───────────────────────────────────────────
        function makeNavBtn(disabled, dir, onClick) {
            const path = dir === 'prev' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7';
            const svg =
                `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}"/></svg>`;
            if (disabled) {
                const s = document.createElement('span');
                s.className = 'flex h-8 w-8 items-center justify-center rounded-lg text-gray-300';
                s.innerHTML = svg;
                return s;
            }
            const b = document.createElement('button');
            b.type = 'button';
            b.className =
                'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50';
            b.innerHTML = svg;
            b.addEventListener('click', onClick);
            return b;
        }

        function buildPageButtons(cont, lastPage) {
            cont.innerHTML = '';
            cont.appendChild(makeNavBtn(currentPage === 1, 'prev', () => {
                currentPage--;
                updateDisplay();
            }));

            const win = 1;
            let prev = null;
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - win && i <= currentPage + win)) {
                    if (prev && i - prev > 1) {
                        const d = document.createElement('span');
                        d.className = 'flex h-8 items-center px-1 text-gray-400 text-xs';
                        d.textContent = '…';
                        cont.appendChild(d);
                    }
                    if (i === currentPage) {
                        const s = document.createElement('span');
                        s.className =
                            'flex h-8 w-8 items-center justify-center rounded-lg bg-[#1b84ff] text-xs font-bold text-white';
                        s.textContent = i;
                        cont.appendChild(s);
                    } else {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.textContent = i;
                        b.className =
                            'flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 transition-colors hover:bg-blue-50 hover:text-[#1b84ff] hover:border-blue-200';
                        b.addEventListener('click', () => {
                            currentPage = i;
                            updateDisplay();
                        });
                        cont.appendChild(b);
                    }
                    prev = i;
                }
            }
            cont.appendChild(makeNavBtn(currentPage === lastPage, 'next', () => {
                currentPage++;
                updateDisplay();
            }));
        }

        function renderPagination(sortedData) {
            buildPageButtons(paginBtns, Math.ceil(sortedData.length / perPage));
        }

        function renderCardPagination(lastPage) {
            buildPageButtons(cardPaginBtns, lastPage);
        }

        // ── Events ───────────────────────────────────────────────────────
        searchInput.addEventListener('input', filterData);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterData();
        });
        perPageSelect.addEventListener('change', e => {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            updateDisplay();
        });
        cardPerPage.addEventListener('change', e => {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            updateDisplay();
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 640) {
                if (viewMode !== 'table') setViewMode('table');
                else updateDisplay();
            }
        });

        if (viewMode === 'card') {
            document.getElementById('viewModeCard')?.classList.add('active-view');
            document.getElementById('viewModeTable')?.classList.remove('active-view');
        }

        updateSortIcons();
        updateDisplay();
    });
</script>
<script>
    function toggleExportDropdown(type) {
        const others = ['kelulusan', 'pernyataan', 'transkrip'].filter(t => t !== type);
        others.forEach(o => {
            document.getElementById('dropdown' + capitalize(o))?.classList.add('hidden');
        });
        document.getElementById('dropdown' + capitalize(type))?.classList.toggle('hidden');
    }

    function toggleSubFilter(type, filter) {
        const key = capitalize(type) + capitalize(filter);
        const sub = document.getElementById('sub' + key);
        const arrow = document.getElementById('arrow' + key);
        const isHidden = sub.classList.contains('hidden');

        ['Jurusan', 'Kelas'].forEach(f => {
            const k = capitalize(type) + f;
            document.getElementById('sub' + k)?.classList.add('hidden');
            const a = document.getElementById('arrow' + k);
            if (a) a.style.transform = 'rotate(0deg)';
        });

        if (isHidden) {
            sub.classList.remove('hidden');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
    }

    function doExport(type, mode) {
        let baseUrl = '';
        if (type === 'kelulusan') {
            baseUrl = '{{ route('admin.graduation.showSuratKelulusan', ['id' => 'all']) }}';
        } else if (type === 'pernyataan') {
            baseUrl = '{{ route('admin.graduation.showSuratPernyataan', ['id' => 'all']) }}';
        } else if (type === 'transkrip') {
            baseUrl = '{{ route('admin.graduation.showTranskripNilai', ['id' => 'all']) }}';
        }

        const params = new URLSearchParams();

        if (mode === 'jurusan') {
            const val = document.getElementById('select' + capitalize(type) + 'Jurusan')?.value;
            if (!val) {
                alert('Pilih jurusan terlebih dahulu.');
                return;
            }
            params.set('expertise_id', val);
        } else if (mode === 'kelas') {
            const val = document.getElementById('select' + capitalize(type) + 'Kelas')?.value;
            if (!val) {
                alert('Pilih kelas terlebih dahulu.');
                return;
            }
            params.set('class_id', val);
        }

        // Add sig_mode (only for kelulusan and transkrip)
        if (type === 'kelulusan' || type === 'transkrip') {
            const sigMode = document.getElementById('sigMode' + capitalize(type))?.value || 'both';
            params.set('sig_mode', sigMode);
        }

        // Check for signatures if exporting Pernyataan
        if (type === 'pernyataan') {
            const allData = window.allGraduationsData || [];
            let targetStudents = [];

            if (mode === 'semua') {
                targetStudents = allData;
            } else if (mode === 'jurusan') {
                const val = params.get('expertise_id');
                targetStudents = allData.filter(g => g.expertise_concentration_id == val);
            } else if (mode === 'kelas') {
                const val = params.get('class_id');
                targetStudents = allData.filter(g => g.class_id == val);
            }

            const missing = targetStudents.filter(s => !s.has_signature);
            if (missing.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Belum Lengkap',
                    text: `Terdapat ${missing.length} siswa yang belum mengupload tanda tangan. Tetap lanjutkan export?`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#1b84ff',
                    cancelButtonColor: '#ef4444',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
                        window.open(url, '_blank');
                        document.getElementById('dropdown' + capitalize(type))?.classList.add('hidden');
                    }
                });
                return;
            }
        }

        const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        window.open(url, '_blank');
        document.getElementById('dropdown' + capitalize(type))?.classList.add('hidden');
    }

    window.doSingleExport = function(type, uuid, overrideMode = null) {
        let baseUrl = '';
        if (type === 'kelulusan') baseUrl = '{{ route('admin.graduation.showSuratKelulusan', ['id' => ':id']) }}';
        else if (type === 'pernyataan') baseUrl = '{{ route('admin.graduation.showSuratPernyataan', ['id' => ':id']) }}';
        else if (type === 'transkrip') baseUrl = '{{ route('admin.graduation.showTranskripNilai', ['id' => ':id']) }}';

        // Check signature for pernyataan
        if (type === 'pernyataan') {
            const g = window.allGraduationsData.find(item => item.uuid === uuid);
            if (g && !g.has_signature) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Belum Ada',
                    text: 'Siswa ini belum mengupload tanda tangan.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                return;
            }
            window.open(baseUrl.replace(':id', uuid), '_blank');
            return;
        }

        const sigMode = overrideMode || document.getElementById('globalSigMode')?.value || 'both';
        const url = baseUrl.replace(':id', uuid) + '?sig_mode=' + sigMode;
        window.open(url, '_blank');
    }

    window.updateAllExportLinks = function() {
        const sigMode = document.getElementById('globalSigMode').value;
        // Sync with dropdown selectors for consistency
        const k = document.getElementById('sigModeKelulusan');
        const t = document.getElementById('sigModeTranskrip');
        if (k) k.value = sigMode;
        if (t) t.value = sigMode;
    }

    document.addEventListener('click', function(e) {
        ['Kelulusan', 'Pernyataan', 'Transkrip'].forEach(type => {
            const wrapper = document.getElementById('dropdownWrapper' + type);
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('dropdown' + type)?.classList.add('hidden');
            }
        });
    });

    function capitalize(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    // ── Generate Tokens ──────────────────────────────────────────────────────
    function doGenerateTokens() {
        if (!confirm('Token lama akan di-overwrite. Lanjutkan generate token untuk semua siswa kelas 12?')) return;

        const btn = document.getElementById('btnGenerateTokensAll');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generating...`;
        }

        const route = document.querySelector('.graduation-table-container').dataset.routeGenerateTokens;

        fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                console.error(err);
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
    }

    function doGenerateTokensClass() {
        const classId = document.getElementById('classFilterSelect').value;
        if (!classId) {
            Swal.fire('Peringatan', 'Pilih kelas terlebih dahulu pada filter kelas.', 'warning');
            return;
        }

        const className = document.getElementById('classFilterSelect').options[document.getElementById('classFilterSelect').selectedIndex].text;

        if (!confirm(`Generate token untuk semua siswa di kelas ${className}? Token lama akan di-overwrite.`)) return;

        const btn = document.getElementById('btnGenerateTokenClass');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generating...`;
        }

        const routeBase = document.querySelector('.graduation-table-container').dataset.routeGenerateTokensClass;
        const route = `${routeBase}?class_id=${classId}`;

        fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                console.error(err);
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
    }

    window.doGenerateTokenStudent = function(graduationId, btn) {
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>`;

        const route = document.querySelector('.graduation-table-container').dataset.routeGenerateTokenStudent;

        fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ graduation_id: graduationId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Update the UI without reloading
                    const tokenEl = document.getElementById(`token-${graduationId}`);
                    const tokenCardEl = document.getElementById(`token-card-${graduationId}`);
                    const tokenHtml = `<code class="inline-block rounded-lg bg-amber-50 border border-amber-200 px-2.5 py-1 text-xs font-mono font-bold text-amber-700 tracking-widest">${data.token}</code>`;
                    const tokenCardHtml = `<code class="rounded-lg bg-amber-50 border border-amber-200 px-2 py-1 text-xs font-mono font-bold text-amber-700 tracking-widest">${data.token}</code>`;
                    
                    if (tokenEl) tokenEl.outerHTML = tokenHtml;
                    if (tokenCardEl) tokenCardEl.outerHTML = tokenCardHtml;

                    // Also update window.allGraduationsData
                    const idx = window.allGraduationsData.findIndex(g => g.uuid === graduationId);
                    if (idx !== -1) window.allGraduationsData[idx].token = data.token;

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Token berhasil diperbarui.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
    }
</script>
