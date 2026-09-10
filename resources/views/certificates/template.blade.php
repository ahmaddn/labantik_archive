<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $certificate->parsePlaceholder('{nama}', $user) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @page {
            size: A4 {{ $certificate->orientation == 'landscape' ? 'landscape' : 'portrait' }};
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #e5e7eb;
            color: #000000;
            line-height: 1.3;
        }

        /* ── ACTION TOOLBAR ── */
        .action-toolbar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 1100px;
            margin: 15px auto;
            border-radius: 8px;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .action-toolbar .title-info {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
        }

        .action-toolbar .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-print {
            background-color: #2563eb;
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-back {
            background-color: #6b7280;
            color: #ffffff;
        }

        .btn-back:hover {
            background-color: #4b5563;
        }

        /* ── CERTIFICATE CONTAINER ── */
        .cert-container {
            width: {{ $certificate->orientation == 'landscape' ? '297mm' : '210mm' }};
            min-height: {{ $certificate->orientation == 'landscape' ? '210mm' : '297mm' }};
            margin: 0 auto 30px auto;
            background: #ffffff;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            page-break-after: always;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Background Image or Golden Border Frame */
        .cert-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .cert-border-frame {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 3px double #c59b27;
            outline: 2px solid #eab308;
            outline-offset: -7px;
            pointer-events: none;
            z-index: 2;
        }

        .cert-inner {
            position: relative;
            z-index: 3;
            padding: 16mm 18mm;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* ── KOP SURAT (2 LOGO RAPI DAN DENGAN RENTANG PRESISI) ── */
        .header {
            text-align: center;
            padding-left: 95px;
            padding-right: 95px;
            position: relative;
            min-height: 96px;
        }

        .header img.logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: auto;
            height: 96px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .header img.logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
            height: 96px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .header .line1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        .header .line2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        .header .line3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        .header .address {
            font-size: 7.5pt;
            font-family: Arial, sans-serif;
            font-weight: normal;
            text-align: center;
            line-height: 1.25;
            color: #000;
            margin-top: 3px;
        }

        .header-border-top {
            border-top: 3px solid #000;
            margin-top: 5px;
        }

        .header-border-thin {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 16px;
        }

        /* ── BODY CONTENT ── */
        .cert-body {
            text-align: center;
            margin: auto 0;
            padding: 5px 0;
        }

        .main-title {
            font-size: 26pt;
            font-weight: 900;
            letter-spacing: 4px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .cert-number {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 16px;
            letter-spacing: 0.5px;
        }

        .sub-title {
            font-size: 12pt;
            font-style: italic;
            color: #374151;
            margin-bottom: 10px;
        }

        .user-name {
            font-size: 26pt;
            font-weight: bold;
            color: #000000;
            text-decoration: underline;
            margin: 10px 0 6px 0;
        }

        .role-caption {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 16px;
        }

        .content-narration {
            font-size: 11pt;
            line-height: 1.5;
            max-width: 88%;
            margin: 0 auto 16px auto;
            color: #000000;
        }

        /* ── SIGNATURE SECTION ── */
        .cert-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            min-width: 240px;
        }

        .signature-box .place-date {
            font-size: 11pt;
            margin-bottom: 4px;
        }

        .signature-box .signer-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 55px;
        }

        .signature-box .signer-name {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-box .signer-nip {
            font-size: 10pt;
            color: #000000;
        }

        /* ── BACK PAGE / STRUKTUR PROGRAM ── */
        .page-break {
            page-break-before: always;
        }

        .table-materi {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .table-materi th, .table-materi td {
            border: 1.5px solid #000000;
            padding: 7px 10px;
        }

        .table-materi th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .table-materi td.center {
            text-align: center;
        }

        /* PRINT MEDIA RULES */
        @media print {
            .action-toolbar {
                display: none !important;
            }

            body {
                background: none !important;
                padding: 0 !important;
            }

            .cert-container {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100vh !important;
            }
        }
    </style>
</head>
<body>

    <!-- ACTION TOOLBAR -->
    <div class="action-toolbar">
        <div class="title-info">
            <i class="fa-solid fa-award text-blue-600 mr-2"></i> {{ $certificate->title }}
        </div>
        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Download PDF
            </button>
        </div>
    </div>

    <!-- PAGE 1: DEPAN SERTIFIKAT -->
    <div class="cert-container">
        @if($certificate->background_image)
            <img src="{{ asset('storage/' . $certificate->background_image) }}" class="cert-bg" alt="Background Sertifikat">
        @else
            <div class="cert-border-frame"></div>
        @endif

        <div class="cert-inner">
            <!-- Kop Surat Resmi (2 Logo) -->
            @if($certificate->show_header)
                <div>
                    <div class="header">
                        @if($certificate->header_left_logo)
                            <img src="{{ asset('storage/' . $certificate->header_left_logo) }}" class="logo-left" alt="Logo Jawa Barat">
                        @else
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Coat_of_arms_of_West_Java.svg/500px-Coat_of_arms_of_West_Java.svg.png" class="logo-left" alt="Logo Jawa Barat">
                        @endif

                        <div class="line1">{{ $certificate->header_title ?? 'PEMERINTAH PROVINSI JAWA BARAT' }}</div>
                        <div class="line2">{{ $certificate->header_subtitle ?? 'CABANG DINAS PENDIDIKAN WILAYAH IX' }}</div>
                        <div class="line3">SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</div>
                        <div class="address">
                            Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi dan Komunikasi, Bisnis dan Manajemen<br />
                            Kampus 1 : Jalan Sekolah No.20 Desa Talagakulon Kecamatan Talaga Kabupaten Majalengka<br />
                            Kampus 2 : Jalan Talaga - Bantarujeg Desa Mekarraharja Kecamatan Talaga Kabupaten Majalengka<br />
                            Telepon (0233) 319238 &nbsp; Fax (0233) 319238 &nbsp; Kode Pos 45463 &nbsp; NPSN 20.21.38.72<br />
                            Website https://smkn1talaga.sch.id/ &nbsp; E-mail: mailsmkn1talaga@gmail.com
                        </div>

                        @if($certificate->header_right_logo)
                            <img src="{{ asset('storage/' . $certificate->header_right_logo) }}" class="logo-right" alt="Logo SMKN 1 Talaga">
                        @else
                            <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" class="logo-right" alt="Logo SMKN 1 Talaga">
                        @endif
                    </div>
                    <div class="header-border-top"></div>
                    <div class="header-border-thin"></div>
                </div>
            @endif

            <!-- Body Sertifikat -->
            <div class="cert-body">
                <div class="main-title">{{ $certificate->main_title }}</div>
                
                @if($certificate->show_number && $certificate->certificate_number_format)
                    <div class="cert-number">
                        Nomor : {{ $certificate->parsePlaceholder($certificate->certificate_number_format, $user) }}
                    </div>
                @endif

                <div class="sub-title">{{ $certificate->sub_title }}</div>

                <div class="user-name">
                    {{ $certificate->parsePlaceholder('{nama}', $user) }}
                </div>

                @if($certificate->role_caption)
                    <div class="role-caption">
                        {{ $certificate->parsePlaceholder($certificate->role_caption, $user) }}
                    </div>
                @endif

                @if($certificate->content_text)
                    <div class="content-narration">
                        {!! nl2br(e($certificate->parsePlaceholder($certificate->content_text, $user))) !!}
                    </div>
                @endif
            </div>

            <!-- Tanda Tangan Halaman 1 -->
            <div class="cert-footer">
                <div class="signature-box">
                    @if($certificate->place_date)
                        <div class="place-date">{{ $certificate->parsePlaceholder($certificate->place_date, $user) }}</div>
                    @endif
                    <div class="signer-title">{{ $certificate->signer_1_title }}</div>

                    <div class="signer-name">
                        {{ $certificate->signer1Employee ? $certificate->signer1Employee->full_name : '-----------------------' }}
                    </div>
                    @if($certificate->signer1Employee && $certificate->signer1Employee->nip)
                        <div class="signer-nip">NIP. {{ $certificate->signer1Employee->nip }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 2: HALAMAN BELAKANG (STRUKTUR PROGRAM) -->
    @if($certificate->show_back_page)
        <div class="cert-container page-break">
            @if($certificate->background_image)
                <img src="{{ asset('storage/' . $certificate->background_image) }}" class="cert-bg" alt="Background Sertifikat">
            @else
                <div class="cert-border-frame"></div>
            @endif

            <div class="cert-inner">
                <div class="cert-body" style="margin-top: 10px;">
                    <h3 style="font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px;">
                        {{ $certificate->back_page_title ?? 'STRUKTUR PROGRAM / DAFTAR MATERI' }}
                    </h3>

                    <!-- Tabel Struktur Program -->
                    <table class="table-materi">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th>Materi / Modul Kegiatan</th>
                                <th style="width: 25%;">Alokasi Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificate->structures as $index => $item)
                                <tr>
                                    <td class="center">{{ $index + 1 }}.</td>
                                    <td>{{ $item->materi_name }}</td>
                                    <td class="center">{{ $item->time_allocation }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="center">Belum ada materi kegiatan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tanda Tangan Halaman 2 -->
                <div class="cert-footer">
                    <div class="signature-box">
                        <div class="signer-title">{{ $certificate->signer_2_title }}</div>

                        <div class="signer-name">
                            {{ $certificate->signer2Employee ? $certificate->signer2Employee->full_name : '-----------------------' }}
                        </div>
                        @if($certificate->signer2Employee && $certificate->signer2Employee->nip)
                            <div class="signer-nip">NIP. {{ $certificate->signer2Employee->nip }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</body>
</html>
