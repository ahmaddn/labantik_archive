@extends('layouts.app')
@section('title', 'Input Kelulusan Siswa')
@section('page-title', 'Input Kelulusan')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Input Transkrip Nilai</h1>
                <p class="text-gray-500 text-sm mt-1">Daftarkan nilai semester lengkap (S1-S6) dan Nilai Rapor (NR) siswa.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('admin.graduation.storeTranscript') }}" method="POST" id="graduationForm">
            @csrf

            {{-- Step 1: Select Class & Student --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-base font-semibold text-gray-800">Langkah 1: Pilih Kelas & Siswa</h3>
                    <p class="text-xs text-gray-600 mt-1">Pilih kelas terlebih dahulu, kemudian pilih siswa</p>
                </div>
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- LEFT: Class Selection --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-3 uppercase tracking-wide">
                            Pilih Kelas <span class="text-red-500">*</span>
                        </label>

                        {{-- Level Filters --}}
                        <div class="flex gap-2 mb-3 flex-wrap">
                            <button type="button" data-level=""
                                class="level-filter px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors active-level">
                                Semua
                            </button>
                            @foreach ($classes->pluck('academic_level')->unique()->sort() as $level)
                                <button type="button" data-level="{{ $level }}"
                                    class="level-filter px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">
                                    Kelas {{ $level }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Search --}}
                        <input type="text" id="classSearch" placeholder="🔍 Cari kelas atau jurusan..."
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3">

                        {{-- Class List --}}
                        <div id="classList" class="space-y-2 max-h-72 overflow-y-auto">
                            @foreach ($classes as $class)
                                <label data-level="{{ $class->academic_level }}" data-name="{{ strtolower($class->name) }}"
                                    class="class-item flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50/50 transition-all">
                                    <input type="radio" name="_class_id" value="{{ $class->id }}"
                                        class="class-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-semibold text-gray-800">
                                            {{ $class->academic_level }} {{ $class->name }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $class->academic_year }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- RIGHT: Student Selection --}}
                    <div>
                        <label for="student_id" class="block text-sm font-bold text-gray-800 mb-3 uppercase tracking-wide">
                            Pilih Siswa <span class="text-red-500">*</span>
                        </label>

                        <div id="studentSection" class="hidden flex flex-col gap-3 h-full">
                            <div id="studentLoading" class="hidden">
                                <div class="flex items-center gap-2 text-blue-600 text-sm">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                    Memuat siswa...
                                </div>
                            </div>
                            <select id="student_id" name="student_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('student_id') border-red-500 @enderror">
                                <option value="">-- Pilih Siswa --</option>
                            </select>
                            @error('student_id')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="studentEmpty" class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 opacity-20 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                            <p class="text-sm">Pilih kelas untuk memuat siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Select Subjects --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Langkah 2: Pilih Mata Pelajaran</h3>
                        <p class="text-xs text-gray-600 mt-1">Centang mapel yang akan ditempuh siswa</p>
                    </div>
                    <span id="mapelCountBadge"
                        class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-lg">
                        0 Mapel
                    </span>
                </div>

                <div class="p-6">
                    {{-- Empty State --}}
                    <div id="mapelEmpty" class="text-center py-16 text-gray-400">
                        <svg class="w-12 h-12 opacity-20 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm">Pilih kelas terlebih dahulu untuk melihat mata pelajaran</p>
                    </div>

                    {{-- Loading State --}}
                    <div id="mapelLoading" class="hidden text-center py-16 text-gray-400">
                        <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-blue-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        <p class="text-sm">Memuat mata pelajaran...</p>
                    </div>

                    {{-- Mapel List --}}
                    <div id="mapelList" class="hidden space-y-4"></div>

                    @error('mapel_ids')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
                            <p class="font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        <span id="selectedCount" class="font-bold text-gray-800">0</span>
                        <span class="text-gray-500">mata pelajaran dipilih</span>
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.graduation.index') }}"
                            class="px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200 text-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-sm text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Kelulusan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classRadios = document.querySelectorAll('.class-radio');
            const classItems = document.querySelectorAll('.class-item');
            const classSearch = document.getElementById('classSearch');
            const levelFilters = document.querySelectorAll('.level-filter');
            const studentSection = document.getElementById('studentSection');
            const studentEmpty = document.getElementById('studentEmpty');
            const studentSelect = document.getElementById('student_id');
            const studentLoading = document.getElementById('studentLoading');
            const mapelEmpty = document.getElementById('mapelEmpty');
            const mapelLoading = document.getElementById('mapelLoading');
            const mapelList = document.getElementById('mapelList');
            const mapelCountBadge = document.getElementById('mapelCountBadge');
            const selectedCount = document.getElementById('selectedCount');

            let activeLevel = '';

            // ── Escape HTML untuk mencegah XSS & syntax error ─────────────────
            function escHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // ── Filter level ───────────────────────────────────────────────────
            levelFilters.forEach(btn => {
                btn.addEventListener('click', () => {
                    activeLevel = btn.dataset.level;
                    levelFilters.forEach(b => b.classList.remove('active-level', 'bg-blue-100',
                        'text-blue-700', 'border-blue-300'));
                    btn.classList.add('active-level', 'bg-blue-100', 'text-blue-700',
                        'border-blue-300');
                    filterClasses();
                });
            });

            // ── Filter search ──────────────────────────────────────────────────
            classSearch.addEventListener('input', filterClasses);

            function filterClasses() {
                const q = classSearch.value.toLowerCase().trim();
                classItems.forEach(item => {
                    const matchLevel = !activeLevel || item.dataset.level == activeLevel;
                    const matchName = !q || item.dataset.name.includes(q);
                    item.style.display = (matchLevel && matchName) ? '' : 'none';
                });
            }

            // ── Pilih kelas → load siswa & mapel ──────────────────────────────
            classRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const classId = this.value;
                    loadStudents(classId);
                    loadMapels(classId);
                });
            });

            function loadStudents(classId) {
                studentEmpty.classList.add('hidden');
                studentSection.classList.remove('hidden');
                studentLoading.classList.remove('hidden');
                studentSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                studentSelect.disabled = true;

                fetch(`{{ route('admin.graduation.studentsByClass') }}?class_id=${classId}`)
                    .then(r => r.json())
                    .then(students => {
                        studentLoading.classList.add('hidden');
                        studentSelect.disabled = false;
                        if (students.length === 0) {
                            studentSelect.innerHTML = '<option value="">Tidak ada siswa di kelas ini</option>';
                            return;
                        }
                        students.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id;
                            opt.textContent = `${s.full_name} (${s.student_number})`;
                            studentSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        studentLoading.classList.add('hidden');
                        studentSelect.disabled = false;
                        studentSelect.innerHTML = '<option value="">Gagal memuat siswa</option>';
                    });
            }

            function loadMapels(classId) {
                mapelEmpty.classList.add('hidden');
                mapelList.classList.add('hidden');
                mapelLoading.classList.remove('hidden');

                fetch(`{{ route('admin.graduation.mapelsByClass') }}?class_id=${classId}`)
                    .then(r => {
                        if (!r.ok) {
                            return r.json().then(err => {
                                throw new Error(err.error || 'HTTP ' + r.status);
                            });
                        }
                        return r.json();
                    })
                    .then(mapels => {
                        mapelLoading.classList.add('hidden');
                        mapelList.innerHTML = '';

                        if (mapels.length === 0) {
                            mapelEmpty.classList.remove('hidden');
                            mapelCountBadge.textContent = '0 Mapel';
                            return;
                        }

                        // Group by expertise_name
                        const groups = {};
                        mapels.forEach(m => {
                            const key = m.expertise_name || 'Umum';
                            if (!groups[key]) groups[key] = [];
                            groups[key].push(m);
                        });

                        Object.entries(groups).forEach(([expertiseName, items]) => {
                            const safeId = 'acc-' + expertiseName
                                .toLowerCase()
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-+|-+$/g, '');

                            const gridAttr = 'grid-' + safeId;

                            // ✅ Gunakan DOM API untuk header, bukan innerHTML dengan variabel langsung
                            const section = document.createElement('div');
                            section.className =
                                'group/accordion flex flex-col border border-gray-200 rounded-xl overflow-hidden';

                            // Buat elemen header pakai DOM — aman dari special chars
                            const headerLabel = document.createElement('label');
                            headerLabel.htmlFor = safeId;
                            headerLabel.className =
                                'flex items-center justify-between px-5 py-3 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-blue-100 cursor-pointer transition-all border-b border-gray-200';
                            headerLabel.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-blue-600 font-bold text-xs shadow-xs">
                                        ${items.length}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800"></span>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-300 group-has-[:checked]/accordion:rotate-180"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            `;
                            // ✅ Set teks nama jurusan secara aman (textContent, bukan innerHTML)
                            headerLabel.querySelector('span').textContent = expertiseName;

                            const toggleInput = document.createElement('input');
                            toggleInput.type = 'checkbox';
                            toggleInput.id = safeId;
                            toggleInput.className = 'peer hidden';
                            toggleInput.checked = true;

                            const collapseDiv = document.createElement('div');
                            collapseDiv.className =
                                'max-h-0 overflow-hidden transition-all duration-300 ease-in-out peer-checked:max-h-[2000px]';

                            const grid = document.createElement('div');
                            grid.className =
                                'grid grid-cols-1 md:grid-cols-2 gap-4 p-5 bg-white';
                            grid.dataset.grid = gridAttr;

                            collapseDiv.appendChild(grid);
                            section.appendChild(headerLabel);
                            section.appendChild(toggleInput);
                            section.appendChild(collapseDiv);
                            mapelList.appendChild(section);

                            // Render tiap mapel
                            items.forEach(m => {
                                const label = document.createElement('label');
                                label.className =
                                    'group flex items-start p-4 bg-white border border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50/50 transition-all cursor-pointer';

                                // Checkbox
                                const checkbox = document.createElement('input');
                                checkbox.name = 'mapel_ids[]';
                                checkbox.value = m.uuid;
                                checkbox.type = 'checkbox';
                                checkbox.className =
                                    'mapel-checkbox h-5 w-5 text-blue-600 border-gray-300 rounded-md focus:ring-blue-500 transition-all flex-shrink-0 mt-0.5';

                                // Wrapper teks
                                const textDiv = document.createElement('div');
                                textDiv.className = 'ml-3 flex-1';

                                const nameSpan = document.createElement('span');
                                nameSpan.className =
                                    'block font-semibold text-gray-800 group-hover:text-blue-700 transition-colors text-sm';
                                nameSpan.textContent = m.name; // ✅ textContent aman

                                const typeBadge = document.createElement('span');
                                typeBadge.className =
                                    'text-xs px-2 py-0.5 bg-gray-100 text-gray-700 rounded inline-block mt-1.5 mb-2 font-medium uppercase tracking-wide';
                                typeBadge.textContent = m.type; // ✅ textContent aman

                                const scoreWrapper = document.createElement('div');
                                scoreWrapper.className = 'score-wrapper hidden mt-4 pt-4 border-t border-gray-100';

                                scoreWrapper.innerHTML = `
                                    <div class="grid grid-cols-4 gap-2 mb-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S1</label>
                                            <input type="number" name="s1[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S2</label>
                                            <input type="number" name="s2[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S3</label>
                                            <input type="number" name="s3[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S4</label>
                                            <input type="number" name="s4[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S5</label>
                                            <input type="number" name="s5[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">S6</label>
                                            <input type="number" name="s6[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">NR</label>
                                            <input type="number" name="nr[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">NA</label>
                                            <input type="number" name="na[${m.uuid}]" min="0" max="100" step="0.01" class="w-full px-2 py-1.5 text-xs border border-blue-200 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                `;

                                // Stop propagation on all inputs inside wrapper
                                scoreWrapper.querySelectorAll('input').forEach(input => {
                                    input.addEventListener('click', e => e.stopPropagation());
                                });

                                textDiv.appendChild(nameSpan);
                                textDiv.appendChild(typeBadge);
                                textDiv.appendChild(scoreWrapper);
                                label.appendChild(checkbox);
                                label.appendChild(textDiv);
                                grid.appendChild(label);

                                // Toggle score input
                                checkbox.addEventListener('change', function() {
                                    scoreWrapper.classList.toggle('hidden', !this
                                        .checked);
                                    updateSelectedCount();
                                });
                            });
                        });

                        mapelList.classList.remove('hidden');
                        mapelCountBadge.textContent = `${mapels.length} Mapel`;

                        mapelList.querySelectorAll('.mapel-checkbox').forEach(cb => {
                            cb.addEventListener('change', updateSelectedCount);
                        });
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        mapelLoading.classList.add('hidden');
                        mapelEmpty.classList.remove('hidden');
                        mapelEmpty.querySelector('p').textContent = 'Gagal memuat mapel.';
                    });
            }

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.mapel-checkbox:checked').length;
                selectedCount.textContent = checked;
            }
        });
    </script>

    <style>
        .active-level {
            @apply bg-blue-100 text-blue-700 border-blue-300;
        }
    </style>
@endsection
