{{-- ═══════════════════════════════════════════════════════════════════════════
     PENGGUNAAN DI HALAMAN MANAPUN:
     @include('components.signature-modal')
     Pastikan @stack('scripts') ada di bagian bawah layout sebelum </body>
═══════════════════════════════════════════════════════════════════════════ --}}

@php
    $sudahTandaTangan = \App\Models\GoogleStatement::where('user_id', auth()->id())->exists();
    $studentData = \Illuminate\Support\Facades\DB::table('ref_students')->where('user_id', auth()->id())->first();
    $graduationToken = $studentData ? \Illuminate\Support\Facades\DB::table('google_graduation')->where('user_id', $studentData->id)->value('token') : null;
@endphp

<div class="flex items-center gap-3">

    @if ($sudahTandaTangan)
        {{-- Sudah TTD → 1 tombol saja: Surat Kelulusan (berisi kelulusan + pernyataan dalam 1 file) --}}
        <a href="{{ route('drive.transkrip.show', auth()->user()->id) }}"
            class="inline-flex items-center gap-2 rounded-xl bg-[#1b84ff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 transition-colors hover:bg-[#1570e0]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Surat Kelulusan
        </a>
    @else
        {{-- Belum TTD → tombol Surat Pernyataan membuka modal token --}}
        <button type="button" onclick="document.getElementById('modalToken').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Surat Pernyataan
        </button>

        {{-- Surat Kelulusan disabled sebelum TTD --}}
        
    @endif

</div>

{{-- ── MODAL SURAT PERNYATAAN/FAKTA INTEGRITAS ────────────────────────────── --}}
<div id="modalSuratPernyataan" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/60 p-4"
    role="dialog" aria-modal="true" aria-labelledby="modalTitle">

    <div class="relative flex max-h-[92vh] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-2xl">

        {{-- Header --}}
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-4">
            <h2 id="modalTitle" class="text-lg font-bold text-gray-800">Surat Pernyataan / Fakta Integritas</h2>
            <button type="button" onclick="closeModal()"
                class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div id="scrollArea" onscroll="checkScroll()"
            class="grow overflow-y-auto px-8 py-6 text-sm leading-relaxed text-gray-700"
            style="font-family: 'Times New Roman', Times, serif;">

            {{-- ── ISI SURAT PERNYATAAN/FAKTA INTEGRITAS (sesuai blade surat_pernyataan) ── --}}
            <div class="mb-4 text-center">
                <p class="text-base font-bold uppercase underline">Surat Pernyataan/Fakta Integritas</p>
            </div>

            <p class="mb-4">Saya yang bertanda tangan di bawah ini:</p>

            <table class="mb-4 w-full text-sm" style="border-collapse:collapse;">
                <tr>
                    <td class="w-56 align-top py-0.5">Nama Lengkap</td>
                    <td class="w-4 align-top py-0.5">:</td>
                    @php
                            $student = \Illuminate\Support\Facades\DB::table('ref_students')
                                ->where('user_id', auth()->id())
                                ->first();
                            $program = \Illuminate\Support\Facades\DB::table('ref_classes')
                                ->join('core_expertise_concentrations', 'ref_classes.expertise_concentration_id', '=', 'core_expertise_concentrations.id')
                                ->where('ref_classes.id', auth()->user()->class_id)
                                ->select('core_expertise_concentrations.name as program_name')
                                ->first();
                        @endphp
                    <td class="py-0.5"><strong>{{ $student->full_name ?? '—' }}</strong></td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Tempat/Tanggal Lahir</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">

                        {{ $student->birth_place_date ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">NISN</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">{{ $student->national_student_number ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">NPSN</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">20213872</td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Nama Sekolah</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">SMK Negeri 1 Talaga</td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Program Keahlian</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">{{ $program->program_name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Alamat</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">{{ $student->address ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="align-top py-0.5">Nama Orang Tua/Wali</td>
                    <td class="py-0.5">:</td>
                    <td class="py-0.5">{{ $student->guardian_name ?? '—' }}</td>
                </tr>
            </table>

            <p class="mb-3">Menyatakan secara sadar dan sungguh-sungguh apabila saya dinyatakan lulus tidak akan melakukan:</p>

            <ol class="mb-4 list-outside list-decimal space-y-1.5 pl-5">
                <li>Hal-hal yang tidak terpuji, seperti mencorat-coret baju atau sarana dan prasarana fasilitas umum.</li>
                <li>Konvoi kendaraan sehingga mengganggu pengguna jalan lainnya.</li>
                <li>Kumpul-kumpul pada tempat tertentu dengan melakukan hal yang tidak terpuji yang akan merusak nama baik diri, keluarga dan lembaga.</li>
            </ol>

            <p class="mb-3">Bila lulus saya bersedia:</p>

            <ol class="mb-4 list-outside list-decimal space-y-1.5 pl-5">
                <li>Sujud Syukur sebagai ungkapan kebahagiaan saya.</li>
                <li>Menyumbangkan seragam kepada pihak yang memerlukan.</li>
            </ol>

            <p class="mb-3">
                Bila saya melanggar ketentuan di atas dan terjadi hal negatif yang melibatkan saya dengan pihak berwajib maka saya tidak akan membawa nama sekolah dan sepenuhnya menjadi tanggung jawab saya.
            </p>

            <p class="mb-6">Demikian pernyataan saya dibuat dengan sadar tanpa paksaan dari pihak mana pun.</p>

            <p class="mb-1">Majalengka, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p class="mb-8">Yang menyatakan,</p>

            {{-- ── SIGNATURE PAD ── --}}
            <div id="signatureSection" class="mt-2">
                <p class="mb-2 text-center text-xs text-gray-500">
                    <span id="signHint">↕ Scroll ke bawah untuk membaca seluruh surat, lalu tanda tangani di kotak berikut.</span>
                </p>

                <div class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50"
                    style="height:160px;">
                    <canvas id="signatureCanvas" class="h-full w-full touch-none" style="display:block;"></canvas>
                </div>

                <div class="mt-2 flex items-center justify-between">
                    <button type="button" onclick="clearSignature()"
                        class="text-xs text-gray-500 underline hover:text-gray-700">
                        Hapus Tanda Tangan
                    </button>
                    <p class="text-xs text-gray-400">{{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>

        {{-- Footer Tombol --}}
        <div class="flex shrink-0 justify-end gap-3 border-t border-gray-200 px-6 py-4">
            <button type="button" onclick="closeModal()"
                class="rounded-xl border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                Batal
            </button>
            <button id="btnSimpan" type="button" onclick="saveSignature()" disabled
                class="rounded-xl bg-[#1b84ff] px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-[#1570e0] disabled:cursor-not-allowed disabled:opacity-40">
                Simpan & Tandatangani
            </button>
        </div>
    </div>
</div>

{{-- ── MODAL TOKEN ────────────────────────────── --}}
<div id="modalToken" class="fixed inset-0 z-[60] flex hidden items-center justify-center bg-black/60 p-4"
    role="dialog" aria-modal="true">
    <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <h3 class="mb-4 text-lg font-bold text-gray-800">Masukkan Token</h3>
        <p class="mb-4 text-sm text-gray-600">Silakan masukkan token yang diberikan oleh wali kelas/admin untuk melanjutkan ke penandatanganan Surat Pernyataan.</p>
        
        <input type="text" id="tokenInput" class="mb-4 w-full rounded-lg border border-gray-300 p-3 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Token" autocomplete="off">
        
        <p id="tokenError" class="mb-4 hidden text-sm text-red-500">Token salah! Silakan coba lagi.</p>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('modalToken').classList.add('hidden')"
                class="rounded-xl border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </button>
            <button type="button" onclick="verifyToken()"
                class="rounded-xl bg-[#1b84ff] px-5 py-2 text-sm font-semibold text-white hover:bg-[#1570e0]">
                Lanjut
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        (function() {
            let signaturePad = null;

            window.verifyToken = function() {
                const tokenInput = document.getElementById('tokenInput').value;
                const correctToken = "{{ $graduationToken }}";
                
                if (!correctToken) {
                    alert('Token belum di-generate oleh admin.');
                    return;
                }

                if (tokenInput === correctToken) {
                    document.getElementById('modalToken').classList.add('hidden');
                    document.getElementById('modalSuratPernyataan').classList.remove('hidden');
                    document.getElementById('tokenError').classList.add('hidden');
                    document.getElementById('tokenInput').value = '';
                } else {
                    document.getElementById('tokenError').classList.remove('hidden');
                }
            };

            function initPad() {
                const canvas = document.getElementById('signatureCanvas');
                if (!canvas || signaturePad) return;

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width  = canvas.offsetWidth  * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                signaturePad = new SignaturePad(canvas, {
                    minWidth: 0.8,
                    maxWidth: 2.5,
                    penColor: '#1a1a2e',
                    backgroundColor: 'rgba(0,0,0,0)',
                });

                signaturePad.addEventListener('endStroke', () => {
                    document.getElementById('btnSimpan').disabled = signaturePad.isEmpty();
                });
            }

            window.checkScroll = function() {
                const el = document.getElementById('scrollArea');
                const reachedBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 40;
                if (reachedBottom) {
                    document.getElementById('signHint').textContent =
                        'Tanda tangan di kotak di bawah ini menggunakan mouse atau jari.';
                    initPad();
                }
            };

            window.clearSignature = function() {
                if (signaturePad) {
                    signaturePad.clear();
                    document.getElementById('btnSimpan').disabled = true;
                }
            };

            window.saveSignature = async function() {
                if (!signaturePad || signaturePad.isEmpty()) {
                    alert('Mohon tanda tangani terlebih dahulu.');
                    return;
                }

                const base64 = signaturePad.toDataURL('image/png');
                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.textContent = 'Menyimpan…';

                try {
                    const res = await fetch('{{ route('drive.signature.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ signature_data: base64 }),
                    });

                    const json = await res.json();

                    if (res.ok && json.success) {
                        closeModal();
                        window.location.reload();
                    } else {
                        alert(json.message ?? 'Terjadi kesalahan. Coba lagi.');
                        btn.disabled = false;
                        btn.textContent = 'Simpan & Tandatangani';
                    }
                } catch (err) {
                    console.error(err);
                    alert('Koneksi gagal. Coba lagi.');
                    btn.disabled = false;
                    btn.textContent = 'Simpan & Tandatangani';
                }
            };

            window.closeModal = function() {
                document.getElementById('modalSuratPernyataan').classList.add('hidden');
                document.getElementById('scrollArea').scrollTop = 0;
            };

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

            document.getElementById('modalSuratPernyataan')
                .addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });
        })();
    </script>
@endpush
