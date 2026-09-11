<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Keaslian Sertifikat — SMKN 1 Talaga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-slate-900 text-slate-100 flex items-center justify-center p-4 sm:p-6">

    <div class="max-w-md w-full bg-white text-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
        
        {{-- Header Card --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-center text-white relative">
            <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/20">
                <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" alt="Logo SMKN 1 Talaga" class="w-12 h-12 object-contain" />
            </div>
            <h1 class="text-lg font-extrabold tracking-tight">SMK NEGERI 1 TALAGA</h1>
            <p class="text-xs text-blue-100 mt-0.5">Sistem Verifikasi Keaslian Sertifikat Resmi</p>
        </div>

        {{-- Verification Status Badge --}}
        <div class="p-6 space-y-5">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Status Dokumen</span>
                    <span class="text-sm font-extrabold text-emerald-900">TERVERIFIKASI SAH &amp; RESMI</span>
                </div>
            </div>

            {{-- Certificate Info List --}}
            <div class="space-y-3.5 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Nama Sertifikat</span>
                    <span class="text-sm font-bold text-slate-800">{{ $certificate->title }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Penerima Sertifikat</span>
                    <span class="text-sm font-bold text-blue-600">
                        @if($certificate->recipient_type == 'narasumber' && !empty($certificate->custom_recipient_name))
                            {{ $certificate->custom_recipient_name }}
                        @else
                            {{ $user->name ?? 'Pengguna SMKN 1 Talaga' }}
                        @endif
                    </span>
                </div>

                @if($certificate->show_number && $certificate->certificate_number_format)
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Nomor Sertifikat</span>
                    <span class="text-xs font-bold text-slate-700">{{ $certificate->parsePlaceholder($certificate->certificate_number_format, $user) }}</span>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Penandatangan 1</span>
                        <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $certificate->signer1Employee ? $certificate->signer1Employee->full_name : '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $certificate->signer_1_title }}</span>
                    </div>
                    
                    @if($certificate->signer2Employee)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Penandatangan 2</span>
                        <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $certificate->signer2Employee->full_name }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $certificate->signer_2_title }}</span>
                    </div>
                    @endif
                </div>

                @if($certificate->place_date)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Tempat &amp; Tanggal Terbit</span>
                    <span class="text-xs font-semibold text-slate-700">{{ $certificate->parsePlaceholder($certificate->place_date, $user) }}</span>
                </div>
                @endif
            </div>

            {{-- Action Button --}}
            <div class="pt-2">
                <a href="{{ route('certificates.public_preview', $certificate->id) }}" target="_blank" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition shadow-lg shadow-blue-500/25">
                    <i class="fa-solid fa-file-invoice"></i> Lihat / Cetak Dokumen Sertifikat
                </a>
            </div>

            {{-- Footer Note --}}
            <div class="text-center border-t border-slate-100 pt-3">
                <p class="text-[10px] text-slate-400">Verifikasi otomatis oleh Sistem Dokumen SMKN 1 Talaga</p>
            </div>
        </div>

    </div>

</body>

</html>
