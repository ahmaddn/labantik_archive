{{-- ═══════════════════════════════════════════════════════════════════════════
     PENGGUNAAN DI HALAMAN MANAPUN:
     @include('components.signature-modal')
     Pastikan @stack('scripts') ada di bagian bawah layout sebelum </body>
═══════════════════════════════════════════════════════════════════════════ --}}

{{-- ── TOMBOL ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-center gap-3">

    {{-- Tombol Surat Pernyataan --}}
    <button
        type="button"
        onclick="document.getElementById('modalSuratPernyataan').classList.remove('hidden')"
        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-600">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        Surat Pernyataan
    </button>

    {{-- Tombol Surat Kelulusan — enabled jika sudah tanda tangan dan punya data kelulusan --}}
    @php
        $sudahTandaTangan = \App\Models\StudentSignature::find(auth()->id()) !== null && 
                            \App\Models\GoogleGraduation::where('user_id', auth()->id())->exists();
    @endphp

    @if($sudahTandaTangan)
        <a
            href="{{ route('drive.transkrip.show', auth()->user()->id) }}"
            class="inline-flex items-center gap-2 rounded-xl bg-[#1b84ff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 transition-colors hover:bg-[#1570e0]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Surat Kelulusan
        </a>
    @else
        <button
            type="button"
            disabled
            title="Selesaikan Surat Pernyataan terlebih dahulu"
            class="inline-flex items-center gap-2 rounded-xl bg-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-500 shadow-sm cursor-not-allowed select-none">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Surat Kelulusan
        </button>
    @endif

</div>

{{-- ── MODAL SURAT PERNYATAAN ──────────────────────────────────────────────── --}}
<div
    id="modalSuratPernyataan"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    role="dialog" aria-modal="true" aria-labelledby="modalTitle">

    <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[92vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <h2 id="modalTitle" class="text-lg font-bold text-gray-800">Surat Pernyataan Kelulusan</h2>
            <button
                type="button"
                onclick="closeModal()"
                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div
            id="scrollArea"
            onscroll="checkScroll()"
            class="overflow-y-auto px-8 py-6 text-sm text-gray-700 leading-relaxed grow"
            style="font-family: 'Times New Roman', Times, serif;">

            {{-- ── ISI SURAT PERNYATAAN ── --}}
            <div class="text-center mb-6">
                <p class="font-bold text-base uppercase">SURAT PERNYATAAN</p>
                <p class="text-sm">No: ___/TU.01.01/SMK-Tlg/CADISDIKWIL.IX/2025</p>
            </div>

            <p class="mb-4">Yang bertanda tangan di bawah ini:</p>

            <table class="mb-4 w-full text-sm" style="border-collapse:collapse;">
                <tr>
                    <td class="w-48 align-top">Nama</td>
                    <td class="w-4 align-top">:</td>
                    <td><strong>{{ auth()->user()->name }}</strong></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td>{{ auth()->user()->nisn ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Program Keahlian</td>
                    <td>:</td>
                    <td>{{ auth()->user()->program_keahlian ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Konsentrasi Keahlian</td>
                    <td>:</td>
                    <td>{{ auth()->user()->konsentrasi_keahlian ?? '—' }}</td>
                </tr>
            </table>

            <p class="mb-3">
                Dengan ini menyatakan dengan sesungguhnya bahwa saya:
            </p>

            <ol class="list-decimal list-outside pl-5 space-y-2 mb-4">
                <li>
                    Bersedia menerima dan mengakui keabsahan Transkrip Nilai yang diterbitkan oleh
                    <strong>SMKN 1 Talaga</strong> sebagai dokumen resmi hasil belajar saya selama mengikuti
                    pendidikan di sekolah tersebut.
                </li>
                <li>
                    Menyatakan bahwa seluruh data yang tercantum dalam transkrip nilai adalah benar dan sesuai
                    dengan data yang telah saya berikan kepada pihak sekolah.
                </li>
                <li>
                    Tidak akan melakukan pemalsuan, pengubahan, atau penyalahgunaan dokumen transkrip nilai
                    dalam bentuk apapun.
                </li>
                <li>
                    Bersedia bertanggung jawab secara hukum apabila dikemudian hari terbukti melakukan
                    tindakan pemalsuan dokumen sebagaimana dimaksud di atas.
                </li>
                <li>
                    Surat pernyataan ini dibuat dengan penuh kesadaran dan tanpa paksaan dari pihak manapun,
                    untuk dipergunakan sebagaimana mestinya.
                </li>
            </ol>

            <p class="mb-6">
                Demikian surat pernyataan ini saya buat untuk dipergunakan sebagaimana mestinya.
            </p>

            <p class="mb-1">
                Majalengka, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </p>
            <p class="mb-8">Yang menyatakan,</p>

            {{-- ── SIGNATURE PAD ── --}}
            <div id="signatureSection" class="mt-2">
                <p class="text-xs text-gray-500 mb-2 text-center">
                    <span id="signHint">↕ Scroll ke bawah untuk membaca seluruh surat, lalu tanda tangani di kotak berikut.</span>
                </p>

                <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 overflow-hidden" style="height:160px;">
                    <canvas
                        id="signatureCanvas"
                        class="w-full h-full touch-none"
                        style="display:block;"></canvas>
                </div>

                <div class="flex justify-between items-center mt-2">
                    <button
                        type="button"
                        onclick="clearSignature()"
                        class="text-xs text-gray-500 underline hover:text-gray-700">
                        Hapus Tanda Tangan
                    </button>
                    <p class="text-xs text-gray-400">{{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>

        {{-- Footer Tombol --}}
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0">
            <button
                type="button"
                onclick="closeModal()"
                class="rounded-xl border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button
                id="btnSimpan"
                type="button"
                onclick="saveSignature()"
                disabled
                class="rounded-xl bg-[#1b84ff] px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-[#1570e0] disabled:opacity-40 disabled:cursor-not-allowed">
                Simpan & Tandatangani
            </button>
        </div>
    </div>
</div>

@push('scripts')
{{-- Signature Pad Library --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
(function () {
    /* ── Inisialisasi Signature Pad ── */
    let signaturePad = null;

    function initPad() {
        const canvas = document.getElementById('signatureCanvas');
        if (!canvas || signaturePad) return;

        // Sesuaikan ukuran canvas dengan elemen
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);

        signaturePad = new SignaturePad(canvas, {
            minWidth: 0.8,
            maxWidth: 2.5,
            penColor: '#1a1a2e',
            backgroundColor: 'rgb(0,0,0,0)', // transparan
        });

        // Aktifkan tombol Simpan saat ada goresan
        signaturePad.addEventListener('endStroke', () => {
            document.getElementById('btnSimpan').disabled = signaturePad.isEmpty();
        });
    }

    /* ── Cek apakah sudah scroll ke bawah ── */
    window.checkScroll = function () {
        const el = document.getElementById('scrollArea');
        const reachedBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 40;
        if (reachedBottom) {
            document.getElementById('signHint').textContent =
                'Tanda tangan di kotak di bawah ini menggunakan mouse atau jari.';
            // Init pad saat sudah scroll
            initPad();
        }
    };

    /* ── Hapus tanda tangan ── */
    window.clearSignature = function () {
        if (signaturePad) {
            signaturePad.clear();
            document.getElementById('btnSimpan').disabled = true;
        }
    };

    /* ── Simpan ke server ── */
    window.saveSignature = async function () {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Mohon tanda tangani terlebih dahulu.');
            return;
        }

        const base64 = signaturePad.toDataURL('image/png'); // base64 string
        const btn    = document.getElementById('btnSimpan');

        btn.disabled    = true;
        btn.textContent = 'Menyimpan…';

        try {
            const res = await fetch('{{ route("drive.signature.store") }}', {
                method : 'POST',
                headers: {
                    'Content-Type'     : 'application/json',
                    'X-CSRF-TOKEN'     : '{{ csrf_token() }}',
                    'Accept'           : 'application/json',
                },
                body: JSON.stringify({ signature_data: base64 }),
            });

            const json = await res.json();

            if (res.ok && json.success) {
                // Tutup modal, reload halaman agar tombol Surat Kelulusan menjadi aktif
                closeModal();
                window.location.reload();
            } else {
                alert(json.message ?? 'Terjadi kesalahan. Coba lagi.');
                btn.disabled    = false;
                btn.textContent = 'Simpan & Tandatangani';
            }
        } catch (err) {
            console.error(err);
            alert('Koneksi gagal. Coba lagi.');
            btn.disabled    = false;
            btn.textContent = 'Simpan & Tandatangani';
        }
    };

    /* ── Tutup modal & reset ── */
    window.closeModal = function () {
        document.getElementById('modalSuratPernyataan').classList.add('hidden');
        // Reset scroll ke atas saat dibuka ulang
        document.getElementById('scrollArea').scrollTop = 0;
    };

    /* ── Re-init pad saat ukuran window berubah ── */
    window.addEventListener('resize', () => {
        if (signaturePad) {
            const canvas = document.getElementById('signatureCanvas');
            const data   = signaturePad.toData();
            const ratio  = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width  = canvas.offsetWidth  * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            signaturePad.fromData(data);
        }
    });

    /* ── Tutup saat klik backdrop ── */
    document.getElementById('modalSuratPernyataan')
        .addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
})();
</script>
@endpush
