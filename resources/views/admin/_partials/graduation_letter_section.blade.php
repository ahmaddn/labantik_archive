{{--
    ============================================================
    PARTIAL: _partials/graduation_letter_section.blade.php
    Include di graduation/index.blade.php dengan:
    @include('admin._partials.graduation_letter_section', ['letters' => $letters])
    ============================================================
--}}

<div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-800">Template Surat Keterangan Lulus</h2>
            <p class="text-xs text-gray-400 mt-0.5">Kelola template surat yang digunakan untuk kelulusan siswa.</p>
        </div>
        <button onclick="openLetterModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Template
        </button>
    </div>

    <div class="p-6">
        @if ($letters->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Belum ada template surat.</p>
                <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Template" untuk membuat yang pertama.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                No</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Nomor Surat</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Tahun Pelajaran</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Kepala Sekolah</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Tanggal Kelulusan</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Pernyataan</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3 pr-4">
                                Konten</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pb-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($letters as $index => $letter)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="py-3.5 pr-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 pr-4">
                                    <span class="font-semibold text-gray-800">{{ $letter->letter_number }}</span>
                                </td>
                                <td class="py-3.5 pr-4 text-gray-600">
                                    {{ $letter->academic_year ?? '-' }}
                                </td>
                                <td class="py-3.5 pr-4 text-gray-600">
                                    @php
                                        $hm = $headmasters->firstWhere('id', $letter->headmaster_id);
                                    @endphp
                                    {{ $hm->employee->full_name ?? ($hm->name ?? '-') }}
                                </td>
                                <td class="py-3.5 pr-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-3.5 pr-4 text-gray-600 max-w-xs">
                                    <p class="truncate" title="{{ $letter->statement }}">{{ $letter->statement }}</p>
                                </td>
                                <td class="py-3.5 pr-4 text-gray-600 max-w-xs">
                                    @php
                                        $contentLines = array_filter(
                                            array_map('trim', explode("\n", $letter->content)),
                                        );
                                        $previewLine = collect($contentLines)->first() ?? '-';
                                        $totalLines = count($contentLines);
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-xs" title="{{ $letter->content }}">{{ $previewLine }}
                                        </p>
                                        @if ($totalLines > 1)
                                            <span
                                                class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-500">
                                                +{{ $totalLines - 1 }} lagi
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{-- Tombol Preview --}}
                                        <button
                                            onclick="previewLetter('{{ $letter->uuid }}', {{ json_encode($letter->letter_number) }}, {{ json_encode($letter->graduation_date) }}, {{ json_encode($letter->statement) }}, {{ json_encode($letter->content) }}, {{ json_encode($letter->academic_year) }}, {{ json_encode($letter->headmaster_id) }})"
                                            class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                            title="Preview">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        {{-- Tombol Edit --}}
                                        <button
                                            onclick="editLetter('{{ $letter->uuid }}', {{ json_encode($letter->letter_number) }}, {{ json_encode($letter->graduation_date) }}, {{ json_encode($letter->statement) }}, {{ json_encode($letter->content) }}, {{ json_encode($letter->academic_year) }}, {{ json_encode($letter->headmaster_id) }})"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Tombol Delete --}}
                                        <button
                                            onclick="deleteLetter('{{ $letter->uuid }}', {{ json_encode($letter->letter_number) }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL: Tambah / Edit Template Surat                        --}}
{{-- ========================================================== --}}
<div id="letterModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeLetterModal()"></div>

        {{-- Modal Panel --}}
        <div
            class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            {{-- Modal Header --}}
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 id="letterModalTitle" class="text-lg font-semibold text-gray-900">Tambah Template Surat</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Template surat keterangan lulus untuk siswa.</p>
                </div>
                <button onclick="closeLetterModal()"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="letterForm" method="POST" action="{{ route('admin.graduation.letter.store') }}">
                @csrf
                <input type="hidden" name="_method" id="letterFormMethod" value="POST">
                <input type="hidden" name="letter_uuid" id="letterUuid" value="">

                <div class="px-6 py-5 space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nomor Surat --}}
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nomor Surat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="letter_number" name="letter_number"
                                placeholder="Contoh: 260/TU.01.02/SMK-Tig.CADISDIKWIL.IX/V/2025"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                                required>
                        </div>

                        {{-- Tahun Pelajaran --}}
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tahun Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="academic_year" name="academic_year"
                                placeholder="Contoh: 2025/2026"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                                required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tanggal Kelulusan --}}
                        <div>
                            <label for="graduation_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tanggal Kelulusan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="graduation_date" name="graduation_date"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                        </div>

                        {{-- Kepala Sekolah --}}
                        <div>
                            <label for="headmaster_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kepala Sekolah <span class="text-red-500">*</span>
                            </label>
                            <select id="headmaster_id" name="headmaster_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                                <option value="">Pilih Kepala Sekolah</option>
                                @foreach ($headmasters as $hm)
                                    <option value="{{ $hm->id }}">
                                        {{ $hm->employee->full_name ?? ($hm->name ?? 'Tanpa Nama') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Statement (Kepala Sekolah sampai Berdasarkan) --}}
                    <div>
                        <label for="statement" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Pernyataan Kepala Sekolah <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-400 mb-2">
                            Isi bagian pembuka: dari "Kepala SMK..." hingga "...berdasarkan:". Contoh:
                            <em class="text-gray-500">Kepala SMK Negeri 1 Talaga Selaku Ketua Penyelenggara Ujian
                                Sekolah
                                Tahun Pelajaran [TAHUN_PELAJARAN] berdasarkan:</em>
                        </p>
                        <textarea id="statement" name="statement" rows="4"
                            placeholder="Kepala SMK Negeri 1 Talaga Selaku Ketua Penyelenggara Ujian Sekolah Tahun Pelajaran [TAHUN_PELAJARAN] berdasarkan:"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400 resize-none"
                            required></textarea>
                    </div>

                    {{-- Content (Butir-butir / poin 1, 2, 3) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Isi / Konten Surat <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-400 mb-2">
                            Tekan <kbd class="bg-gray-100 border border-gray-300 rounded px-1 text-xs">Enter</kbd>
                            sekali untuk lanjut baris (masih satu poin),
                            tekan <kbd class="bg-gray-100 border border-gray-300 rounded px-1 text-xs">Enter</kbd> dua
                            kali untuk poin baru.
                        </p>

                        {{-- Editor visual --}}
                        <div id="contentEditor" contenteditable="true"
                            class="w-full min-h-[140px] px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                            style="font-family: inherit; line-height: 1.6;"></div>

                        {{-- Hidden textarea untuk submit form --}}
                        <textarea id="content" name="content" class="hidden" required></textarea>

                        <p class="text-xs text-gray-400 mt-1.5">
                            <span class="font-medium text-gray-500">Tips:</span> Satu poin bisa terdiri dari beberapa
                            baris. Enter 2x untuk poin berikutnya.
                        </p>
                    </div>

                    {{-- Preview poin --}}
                    <div id="contentPreviewContainer" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Preview
                            Poin</label>
                        <div id="contentPreview"
                            class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-700 space-y-1.5"></div>
                    </div>

                    {{-- Preview poin --}}
                    <div id="contentPreviewContainer" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Preview
                            Poin</label>
                        <div id="contentPreview"
                            class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-700 space-y-1.5"></div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeLetterModal()"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit" id="letterSubmitBtn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span id="letterSubmitText">Simpan Template</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL: Preview Surat                                       --}}
{{-- ========================================================== --}}
<div id="letterPreviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePreviewModal()"></div>

        <div
            class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Preview Surat</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tampilan surat keterangan lulus.</p>
                </div>
                <button onclick="closePreviewModal()"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                {{-- Tampilan mirip surat resmi --}}
                <div class="border border-gray-200 rounded-xl p-6 bg-gray-50 font-serif">
                    <div class="text-center mb-4">
                        <h4 class="text-base font-bold text-gray-900 underline tracking-widest">SURAT KETERANGAN LULUS
                        </h4>
                        <p id="previewLetterNumber" class="text-xs text-gray-600 mt-1">Nomor : -</p>
                    </div>

                    <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                        <p id="previewStatement" class="text-gray-800"></p>

                        <ol id="previewContent" class="list-decimal list-inside space-y-1.5 pl-2 text-gray-700"></ol>

                        <div class="pt-4 text-right">
                            <p id="previewDate" class="text-sm text-gray-600"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                <button onclick="closePreviewModal()"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL: Konfirmasi Hapus                                    --}}
{{-- ========================================================== --}}
<div id="deleteLetterModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteLetterModal()">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Hapus Template Surat</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Anda yakin ingin menghapus template surat
                        <strong id="deleteLetterName" class="text-gray-700"></strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeDeleteLetterModal()"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">
                    Batal
                </button>
                <form id="deleteLetterForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors text-sm">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --------------------------------------------------------
    // Modal: Tambah / Edit
    // --------------------------------------------------------
    function openLetterModal() {
        document.getElementById('letterModalTitle').textContent = 'Tambah Template Surat';
        document.getElementById('letterSubmitText').textContent = 'Simpan Template';
        document.getElementById('letterFormMethod').value = 'POST';
        document.getElementById('letterForm').action = '{{ route('admin.graduation.letter.store') }}';
        document.getElementById('letterUuid').value = '';
        document.getElementById('letter_number').value = '';
        document.getElementById('academic_year').value = '';
        document.getElementById('headmaster_id').value = '';
        document.getElementById('graduation_date').value = '';
        document.getElementById('statement').value = '';
        // Reset editor
        document.getElementById('contentEditor').innerHTML = '<div><br></div>';
        document.getElementById('content').value = '';
        updateContentPreview('');
        document.getElementById('letterModal').classList.remove('hidden');
    }

    function closeLetterModal() {
        document.getElementById('letterModal').classList.add('hidden');
    }

    function editLetter(uuid, letterNumber, graduationDate, statement, content, academicYear, headmasterId) {
        document.getElementById('letterModalTitle').textContent = 'Edit Template Surat';
        document.getElementById('letterSubmitText').textContent = 'Perbarui Template';
        document.getElementById('letterFormMethod').value = 'PUT';
        document.getElementById('letterForm').action = `/admin/graduation/letter/${uuid}`;
        document.getElementById('letterUuid').value = uuid;
        document.getElementById('letter_number').value = letterNumber;
        document.getElementById('academic_year').value = academicYear || '';
        document.getElementById('headmaster_id').value = headmasterId || '';
        document.getElementById('graduation_date').value = graduationDate;
        document.getElementById('statement').value = statement;
        // Load ke editor
        textToEditor(content);
        document.getElementById('content').value = content;
        updateContentPreview(content);
        document.getElementById('letterModal').classList.remove('hidden');
    }

    // --------------------------------------------------------
    // CONTENT EDITOR — Enter 1x = baris baru (lanjutan poin)
    //                  Enter 2x = poin baru
    // --------------------------------------------------------
    const editor = document.getElementById('contentEditor');
    const hiddenTextarea = document.getElementById('content');

    // Teks → editor (saat load edit)
    function textToEditor(text) {
        editor.innerHTML = '';
        if (!text) {
            editor.innerHTML = '<div><br></div>';
            return;
        }
        text.split('\n').forEach(line => {
            const div = document.createElement('div');
            if (line === '') {
                div.innerHTML = '<br>';
            } else {
                div.textContent = line;
            }
            editor.appendChild(div);
        });
    }

    // Editor → teks (untuk disimpan & preview)
    function editorToText() {
        const lines = [];
        editor.childNodes.forEach(node => {
            if (node.nodeName === 'DIV' || node.nodeName === 'P') {
                const text = node.innerText ?? node.textContent ?? '';
                // innerText pada div kosong (hanya <br>) = '\n' atau '', normalize ke ''
                lines.push(text === '\n' ? '' : text);
            } else if (node.nodeName === 'BR') {
                lines.push('');
            } else if (node.nodeType === Node.TEXT_NODE) {
                lines.push(node.textContent);
            }
        });
        return lines.join('\n');
    }

    function syncAndPreview() {
        const text = editorToText();
        hiddenTextarea.value = text;
        updateContentPreview(text);
    }

    // Preview: baris kosong = pemisah antar poin
    function updateContentPreview(rawText) {
        const text = rawText !== undefined ? rawText : editorToText();
        const container = document.getElementById('contentPreviewContainer');
        const preview = document.getElementById('contentPreview');

        if (!text.trim()) {
            container.classList.add('hidden');
            return;
        }

        // Split berdasarkan 1+ baris kosong → tiap grup = 1 poin
        const groups = text.split(/\n{2,}/).map(g => g.trim()).filter(g => g !== '');
        if (groups.length === 0) {
            container.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');
        preview.innerHTML = groups.map((group, i) => {
            const htmlLines = group.split('\n').map(l => `<span>${l}</span>`).join('<br>');
            return `<p class="flex gap-2">
                <span class="font-medium text-gray-500 flex-shrink-0 w-5 text-right">${i + 1}.</span>
                <span class="leading-relaxed">${htmlLines}</span>
            </p>`;
        }).join('');
    }

    // Set default paragraph separator to div
    document.execCommand('defaultParagraphSeparator', false, 'div');

    // Handle Enter natively, just sync
    editor.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            // Let the browser handle the Enter key natively (it splits the text and creates a new div)
            setTimeout(syncAndPreview, 10);
        }
    });

    // Also sync on input for regular typing
    editor.addEventListener('input', function() {
        syncAndPreview();
    });

    function getCurrentBlock() {
        const sel = window.getSelection();
        if (!sel.rangeCount) return null;
        let node = sel.getRangeAt(0).startContainer;
        while (node && node.parentNode !== editor) node = node.parentNode;
        return node;
    }

    editor.addEventListener('input', syncAndPreview);

    // --------------------------------------------------------
    // Modal: Preview Surat
    // --------------------------------------------------------
    function previewLetter(uuid, letterNumber, graduationDate, statement, content) {
        document.getElementById('previewLetterNumber').textContent = 'Nomor : ' + letterNumber;
        document.getElementById('previewStatement').textContent = statement;

        const dateObj = new Date(graduationDate);
        document.getElementById('previewDate').textContent = dateObj.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Poin: pisah berdasarkan baris kosong
        const groups = content.split(/\n{2,}/).map(g => g.trim()).filter(g => g !== '');
        const olEl = document.getElementById('previewContent');
        if (groups.length > 0) {
            olEl.innerHTML = groups.map(g => `<li>${g.split('\n').join('<br>')}</li>`).join('');
        } else {
            // fallback: tiap baris = 1 poin
            const lines = content.split('\n').map(l => l.trim()).filter(l => l !== '');
            olEl.innerHTML = lines.map(line => `<li>${line}</li>`).join('');
        }

        document.getElementById('letterPreviewModal').classList.remove('hidden');
    }

    function closePreviewModal() {
        document.getElementById('letterPreviewModal').classList.add('hidden');
    }

    // --------------------------------------------------------
    // Modal: Hapus
    // --------------------------------------------------------
    function deleteLetter(uuid, letterNumber) {
        document.getElementById('deleteLetterName').textContent = letterNumber;
        document.getElementById('deleteLetterForm').action = `/admin/graduation/letter/${uuid}`;
        document.getElementById('deleteLetterModal').classList.remove('hidden');
    }

    function closeDeleteLetterModal() {
        document.getElementById('deleteLetterModal').classList.add('hidden');
    }

    // --------------------------------------------------------
    // Escape key
    // --------------------------------------------------------
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLetterModal();
            closePreviewModal();
            closeDeleteLetterModal();
        }
    });
</script>

{{-- ========================================================== --}}
{{-- MODAL: Konfirmasi Apply Template                           --}}
{{-- ========================================================== --}}
<div id="applyTemplateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeApplyTemplateModal()">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">Terapkan Template ke Semua?</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Anda akan menerapkan template surat <strong id="confirmTemplateNumber"
                            class="text-gray-800"></strong>
                        ke <strong id="confirmGraduationCount" class="text-gray-800">-</strong> data kelulusan.
                    </p>
                    <p class="text-xs text-gray-500 mt-2 italic">
                        ⚠️ Tindakan ini akan mengisi field <code class="bg-gray-100 px-1 rounded">letter_id</code>
                        untuk semua data kelulusan.
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeApplyTemplateModal()"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Batal
                </button>
                <button onclick="confirmApplyTemplate()" id="confirmApplyBtn"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="confirmApplyText">Terapkan</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL: Success Notification                                --}}
{{-- ========================================================== --}}
<div id="successNotificationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSuccessNotification()">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10 animate-in">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">Berhasil!</h3>
                    <p class="text-sm text-gray-600 mt-2" id="successMessage">-</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeSuccessNotification(); location.reload();"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Muat Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const templateSelectDropdown = document.getElementById('templateSelectDropdown');
        const applyTemplateBtn = document.getElementById('applyTemplateBtn');

        if (templateSelectDropdown && applyTemplateBtn) {
            templateSelectDropdown.addEventListener('change', function() {
                const hasSelection = this.value !== '';
                applyTemplateBtn.disabled = !hasSelection;
                if (hasSelection) {
                    applyTemplateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    applyTemplateBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        window.openApplyTemplateModal = async function() {
            if (!templateSelectDropdown) return;
            const selectedId = templateSelectDropdown.value;
            if (!selectedId) {
                alert('Silakan pilih template surat terlebih dahulu');
                return;
            }

            try {
                const response = await fetch(`/admin/graduation/letter/${selectedId}`);
                const letter = await response.json();
                const graduationCount = document.querySelectorAll('table tbody tr').length;
                document.getElementById('confirmTemplateNumber').textContent = letter.letter_number;
                document.getElementById('confirmGraduationCount').textContent = graduationCount || '?';
                document.getElementById('applyTemplateModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error fetching template:', error);
                alert('Gagal memuat detail template');
            }
        };

        window.closeApplyTemplateModal = function() {
            document.getElementById('applyTemplateModal').classList.add('hidden');
        };

        window.confirmApplyTemplate = async function() {
            if (!templateSelectDropdown) return;
            const selectedId = templateSelectDropdown.value;
            if (!selectedId) {
                alert('Silakan pilih template surat terlebih dahulu');
                return;
            }

            const confirmApplyBtn = document.getElementById('confirmApplyBtn');
            const confirmApplyText = document.getElementById('confirmApplyText');
            confirmApplyBtn.disabled = true;
            confirmApplyText.textContent = 'Sedang memproses...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error(
                        'CSRF token tidak ditemukan. Pastikan tag meta csrf-token ada di layout.');
                }

                const response = await fetch('{{ route('admin.graduation.applyTemplateToAll') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // <-- WAJIB agar Laravel return JSON bukan redirect
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        letter_id: selectedId
                    }),
                });

                // Cek apakah response benar-benar JSON sebelum parse
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 500));
                    throw new Error(
                        'Server mengembalikan response bukan JSON. Kemungkinan session expired atau error server. Coba refresh halaman.'
                    );
                }

                const result = await response.json();

                if (result.success) {
                    window.closeApplyTemplateModal();
                    document.getElementById('successMessage').textContent = result.message;
                    document.getElementById('successNotificationModal').classList.remove('hidden');
                } else {
                    alert('Error: ' + result.message);
                    confirmApplyBtn.disabled = false;
                    confirmApplyText.textContent = 'Terapkan';
                }
            } catch (error) {
                console.error('Error applying template:', error);
                alert('Gagal menerapkan template: ' + error.message);
                confirmApplyBtn.disabled = false;
                confirmApplyText.textContent = 'Terapkan';
            }
        };

        window.closeSuccessNotification = function() {
            document.getElementById('successNotificationModal').classList.add('hidden');
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeApplyTemplateModal();
                window.closeSuccessNotification();
            }
        });

        @if ($errors->any() && old('letter_number')) document.addEventListener('DOMContentLoaded', function() {
                openLetterModal();
                document.getElementById('letter_number').value = '{{ old('letter_number') }}';
                document.getElementById('graduation_date').value = '{{ old('graduation_date') }}';
                document.getElementById('statement').value = `{{ old('statement') }}`;
                document.getElementById('content').value = `{{ old('content') }}`;
                updateContentPreview();
            }); @endif
    })();
</script>
