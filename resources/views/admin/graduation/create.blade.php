@extends('layouts.app')
@section('title', 'Input Kelulusan Siswa')
@section('page-title', 'Input Kelulusan')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Input Kelulusan</h1>
                <p class="text-gray-500 text-sm mt-1">Daftarkan kelulusan siswa beserta mata pelajaran yang ditempuh.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('admin.graduation.store') }}" method="POST" id="graduationForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: Informasi Surat --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-base font-semibold text-gray-800">Informasi Surat</h3>
                        </div>
                        <div class="p-6 space-y-5">

                            {{-- Filter Kelas --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Kelas <span class="text-red-500">*</span>
                                </label>

                                {{-- Filter academic level --}}
                                <div class="flex gap-2 mb-2 flex-wrap">
                                    <button type="button" data-level=""
                                        class="level-filter px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors active-level">
                                        Semua
                                    </button>
                                    @foreach ($classes->pluck('academic_level')->unique()->sort() as $level)
                                        <button type="button" data-level="{{ $level }}"
                                            class="level-filter px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">
                                            Kelas {{ $level }}
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Search kelas --}}
                                <input type="text" id="classSearch" placeholder="Cari kelas atau jurusan..."
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-400 mb-2">

                                {{-- List kelas --}}
                                <div id="classList" class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                                    @foreach ($classes as $class)
                                        <label data-level="{{ $class->academic_level }}"
                                            data-name="{{ strtolower($class->name) }}"
                                            class="class-item flex items-center gap-3 px-3 py-2.5 border border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50/50 transition-all">
                                            <input type="radio" name="_class_id" value="{{ $class->id }}"
                                                class="class-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <div class="flex-1 min-w-0">
                                                <span class="block text-sm font-semibold text-gray-800 truncate">
                                                    {{ $class->academic_level }} {{ $class->name }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $class->academic_year }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Pilih Siswa (muncul setelah kelas dipilih) --}}
                            <div id="studentSection" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Siswa <span class="text-red-500">*</span>
                                </label>
                                <div id="studentLoading" class="hidden text-xs text-gray-400 py-2">Memuat siswa...</div>
                                <select id="student_id" name="student_id"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('student_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Siswa --</option>
                                </select>
                                @error('student_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nomor Surat --}}
                            <div>
                                <label for="letter_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Surat <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="letter_number" name="letter_number" required
                                    value="{{ old('letter_number') }}" placeholder="Contoh: 421/001/SMK/2026"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('letter_number') border-red-500 @enderror">
                                @error('letter_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Kelulusan --}}
                            <div>
                                <label for="graduation_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Kelulusan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="graduation_date" name="graduation_date" required
                                    value="{{ old('graduation_date') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm @error('graduation_date') border-red-500 @enderror">
                                @error('graduation_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Daftar Mapel --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">Daftar Mata Pelajaran</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih kelas terlebih dahulu untuk melihat mapel</p>
                            </div>
                            <span id="mapelCountBadge"
                                class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                                0 Mapel
                            </span>
                        </div>

                        {{-- State: belum pilih kelas --}}
                        <div id="mapelEmpty" class="py-16 text-center text-gray-400">
                            <svg class="w-10 h-10 opacity-30 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">Pilih kelas untuk melihat daftar mapel</p>
                        </div>

                        {{-- State: loading --}}
                        <div id="mapelLoading" class="hidden py-16 text-center text-gray-400">
                            <svg class="w-6 h-6 animate-spin mx-auto mb-3 text-blue-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            <p class="text-sm">Memuat mapel...</p>
                        </div>

                        {{-- State: list mapel --}}
                        <div id="mapelList" class="hidden divide-y divide-gray-100"></div>

                        @error('mapel_ids')
                            <div class="px-6 py-3 bg-red-50 border-t border-red-100">
                                <p class="text-red-500 text-xs font-medium">{{ $message }}</p>
                            </div>
                        @enderror

                        {{-- Footer --}}
                        <div
                            class="px-6 py-5 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-3">
                            <p class="text-xs text-gray-500">
                                <span id="selectedCount" class="font-bold text-gray-700">0</span> mapel dipilih
                            </p>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.graduation.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200 text-sm">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-8 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-bold rounded-xl transition-all shadow-sm shadow-blue-200 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Data Kelulusan
                                </button>
                            </div>
                        </div>
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
            const studentSelect = document.getElementById('student_id');
            const studentLoading = document.getElementById('studentLoading');
            const mapelEmpty = document.getElementById('mapelEmpty');
            const mapelLoading = document.getElementById('mapelLoading');
            const mapelList = document.getElementById('mapelList');
            const mapelCountBadge = document.getElementById('mapelCountBadge');
            const selectedCount = document.getElementById('selectedCount');

            let activeLevel = '';

            // ── Filter level ──────────────────────────────────────────────────
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

            // ── Filter search ────────────────────────────────────────────────
            classSearch.addEventListener('input', filterClasses);

            function filterClasses() {
                const q = classSearch.value.toLowerCase().trim();
                classItems.forEach(item => {
                    const matchLevel = !activeLevel || item.dataset.level == activeLevel;
                    const matchName = !q || item.dataset.name.includes(q);
                    item.style.display = (matchLevel && matchName) ? '' : 'none';
                });
            }

            // ── Pilih kelas → load siswa & mapel ────────────────────────────
            classRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const classId = this.value;
                    loadStudents(classId);
                    loadMapels(classId);
                });
            });

            function loadStudents(classId) {
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
                            if (!groups[m.expertise_name]) groups[m.expertise_name] = [];
                            groups[m.expertise_name].push(m);
                        });

                        // Ganti bagian ini di loadMapels:

                        Object.entries(groups).forEach(([expertiseName, items]) => {
                            const section = document.createElement('div');
                            section.className = 'group/accordion flex flex-col';

                            // ← Sanitize: hapus semua karakter selain huruf, angka, dash
                            const safeId = 'acc-' + expertiseName
                                .toLowerCase()
                                .replace(/[^a-z0-9]+/g, '-') // ganti semua non-alphanumeric jadi dash
                                .replace(/^-+|-+$/g, ''); // trim dash di awal/akhir

                            // Gunakan data attribute, BUKAN querySelector dengan ID
                            const gridId = `grid-${safeId}`;

                            section.innerHTML = `
        <label for="${safeId}"
            class="flex items-center justify-between px-6 py-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                    ${items.length}
                </div>
                <span class="text-sm font-bold text-gray-700 uppercase tracking-wide">${expertiseName}</span>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-300 group-has-[:checked]/accordion:rotate-180"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </label>
        <input type="checkbox" id="${safeId}" class="peer hidden" checked>
        <div class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out peer-checked:max-h-[2000px] bg-gray-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-6 pt-2" data-grid="${gridId}"></div>
        </div>
    `;

                            mapelList.appendChild(section);

                            // ← Pakai querySelector dengan data attribute, bukan ID
                            const grid = section.querySelector(`[data-grid="${gridId}"]`);

                            items.forEach(m => {
                                const label = document.createElement('label');
                                label.className =
                                    'group relative flex items-start p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-300 transition-all cursor-pointer shadow-sm';
                                label.innerHTML = `
                                    <div class="flex items-center h-5">
                                        <input name="mapel_ids[]" value="${m.uuid}" type="checkbox"
                                            class="mapel-checkbox h-5 w-5 text-[#1b84ff] border-gray-300 rounded-lg focus:ring-blue-500 transition-all">
                                    </div>
                                    <div class="ml-4 text-sm">
                                        <span class="block font-bold text-gray-800 group-hover:text-blue-700 transition-colors">${m.name}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-bold uppercase">${m.type}</span>
                                        </div>
                                    </div>
                                `;
                                grid.appendChild(label);
                            });
                        });

                        mapelList.classList.remove('hidden');
                        mapelCountBadge.textContent = `${mapels.length} Mapel`;

                        // Update counter saat checkbox berubah
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
            background-color: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }
    </style>
@endsection
