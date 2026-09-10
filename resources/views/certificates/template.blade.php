<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $certificate->parsePlaceholder('{nama}', $user) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@700&family=Dancing+Script:wght@600&family=Great+Vibes&family=Montserrat:wght@500;700&family=Pacifico&family=Playfair+Display:ital,wght@0,600;1,400&family=Sacramento&display=swap" rel="stylesheet">
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
            padding-left: 115px;
            padding-right: 115px;
            position: relative;
            min-height: 94px;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }

        .header img.logo-left {
            position: absolute;
            left: 5px;
            top: 2px;
            width: auto;
            height: 92px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .header img.logo-right {
            position: absolute;
            right: 5px;
            top: 2px;
            width: auto;
            height: 92px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .header .line1 {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.15;
            letter-spacing: 0.2px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .header .line2 {
            font-size: 15.5pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.15;
            letter-spacing: 0.2px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .header .line3 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.15;
            letter-spacing: 0px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .header .address {
            font-size: 6.8pt;
            font-family: 'Times New Roman', Times, serif;
            font-weight: normal;
            text-align: center;
            line-height: 1.15;
            color: #000;
            margin-top: 2px;
        }

        .header-border-top {
            border-top: 2.5px solid #000;
            margin-top: 4px;
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

        /* ── BODY CONTENT & WORDART ── */
        .cert-body {
            text-align: center;
            margin: auto 0;
            padding: 5px 0;
        }

        .main-title {
            font-size: 28pt;
            font-weight: 900;
            letter-spacing: 4px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* WordArt Font Family Variants */
        .font-cinzel { font-family: 'Cinzel', serif !important; }
        .font-great-vibes { font-family: 'Great Vibes', cursive !important; }
        .font-dancing-script { font-family: 'Dancing Script', cursive !important; }
        .font-alex-brush { font-family: 'Alex Brush', cursive !important; }
        .font-sacramento { font-family: 'Sacramento', cursive !important; }
        .font-pacifico { font-family: 'Pacifico', cursive !important; }
        .font-playfair { font-family: 'Playfair Display', serif !important; }
        .font-montserrat { font-family: 'Montserrat', sans-serif !important; }
        .font-times { font-family: 'Times New Roman', Times, serif !important; }

        /* WordArt Style Variants */
        .main-title.wordart-gold-gradient {
            background: linear-gradient(180deg, #ffe57f 0%, #d4af37 40%, #aa7c11 75%, #593e00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 2px 2px rgba(0,0,0,0.35));
            font-weight: 900;
        }

        .main-title.wordart-blue-royal {
            background: linear-gradient(180deg, #60a5fa 0%, #1d4ed8 50%, #1e3a8a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 2px 3px rgba(30, 58, 138, 0.4));
            font-weight: 900;
        }

        .main-title.wordart-emboss-classic {
            color: #1e293b;
            text-shadow: -1px -1px 1px #ffffff, 1px 1px 2px rgba(0,0,0,0.5);
            font-weight: 900;
        }

        .main-title.wordart-emerald-lux {
            background: linear-gradient(180deg, #6ee7b7 0%, #059669 50%, #064e3b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 2px 3px rgba(6, 78, 59, 0.35));
            font-weight: 900;
        }

        .main-title.wordart-ruby-crimson {
            background: linear-gradient(180deg, #fca5a5 0%, #dc2626 50%, #7f1d1d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 2px 3px rgba(127, 29, 29, 0.35));
            font-weight: 900;
        }

        .main-title.wordart-silver-metallic {
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 40%, #64748b 75%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 2px 3px rgba(0,0,0,0.4));
            font-weight: 900;
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

        /* Diberikan Kepada (Recipient Name in Tahoma, smaller size, no underline) */
        .user-name {
            font-size: 19pt;
            font-weight: bold;
            font-family: Tahoma, Geneva, Verdana, sans-serif;
            color: #111827;
            text-decoration: none;
            margin: 8px 0 6px 0;
            letter-spacing: 0.2px;
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

        /* Quill font classes mapping */
        .ql-font-great-vibes { font-family: 'Great Vibes', cursive !important; }
        .ql-font-dancing-script { font-family: 'Dancing Script', cursive !important; }
        .ql-font-alex-brush { font-family: 'Alex Brush', cursive !important; }
        .ql-font-sacramento { font-family: 'Sacramento', cursive !important; }
        .ql-font-pacifico { font-family: 'Pacifico', cursive !important; }
        .ql-font-playfair { font-family: 'Playfair Display', serif !important; }
        .ql-font-cinzel { font-family: 'Cinzel', serif !important; }
        .ql-font-tahoma { font-family: 'Tahoma', sans-serif !important; }

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
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .table-materi td {
            text-align: left;
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

                        <div class="line1">{{ ($certificate->header_title && $certificate->header_title !== 'PEMERINTAH PROVINSI JAWA BARAT') ? $certificate->header_title : 'PEMERINTAH DAERAH PROVINSI JAWA BARAT' }}</div>
                        <div class="line2">{{ $certificate->header_subtitle ?? 'CABANG DINAS PENDIDIKAN WILAYAH IX' }}</div>
                        <div class="line3">SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</div>
                        <div class="address">
                            Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi komunikasi, Bisnis dan Manajemen<br />
                            Kampus 1: Jalan Sekolah Nomor 20 Desa Talagakulon Kecamatan Talaga Kabupaten Majalengka<br />
                            Kampus 2: Jalan Talaga-Bantarujeg Desa Mekarraharja Kecamatan Talaga Kabupaten Majalengka<br />
                            Telpon &#9742; (0233) 319238 FAX &#9993; (0233) 319238 POS &#9993; 45463 NPSN: 20213872<br />
                            Website &#128187; www.smkn1talaga.sch.id &ndash; Email &#9993; admin@smkn1talaga.sch.id
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
                <div class="main-title {{ $certificate->word_art_style && $certificate->word_art_style != 'none' ? 'wordart-' . $certificate->word_art_style : '' }} {{ $certificate->word_art_font ? 'font-' . $certificate->word_art_font : 'font-cinzel' }}">
                    {{ $certificate->main_title }}
                </div>
                
                @if($certificate->show_number && $certificate->certificate_number_format)
                    <div class="cert-number">
                        Nomor : {{ $certificate->parsePlaceholder($certificate->certificate_number_format, $user) }}
                    </div>
                @endif

                <div class="sub-title">{{ $certificate->sub_title }}</div>

                <div class="user-name">
                    @if($certificate->recipient_type == 'narasumber' && !empty($certificate->custom_recipient_name))
                        {{ $certificate->custom_recipient_name }}
                    @else
                        {{ $certificate->parsePlaceholder('{nama}', $user) }}
                    @endif
                </div>

                @if($certificate->role_caption)
                    <div class="role-caption">
                        {{ $certificate->parsePlaceholder($certificate->role_caption, $user) }}
                    </div>
                @endif

                @if($certificate->content_text)
                    <div class="content-narration">
                        {!! $certificate->parsePlaceholder($certificate->content_text, $user) !!}
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
                    @php
                        $totalHours = 0;
                        $hasParsedHours = false;
                        $suffixText = 'Jam';

                        foreach($certificate->structures as $item) {
                            if ($item->time_allocation) {
                                // Match pattern like "18 - Jam", "2 JP", "10 Jam"
                                if (preg_match('/^(\d+)\s*(?:-\s*|\s*)(.*)$/i', trim($item->time_allocation), $matches)) {
                                    $totalHours += (int)$matches[1];
                                    $hasParsedHours = true;
                                    if (!empty($matches[2])) {
                                        $suffixText = trim($matches[2]);
                                    }
                                }
                            }
                        }
                    @endphp

                    <table class="table-materi">
                        <thead>
                            <tr>
                                <th style="width: 8%;">NO</th>
                                <th>MATERI</th>
                                <th style="width: 25%;">ALOKASI WAKTU</th>
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
                        @if($hasParsedHours && count($certificate->structures) > 0)
                            <tfoot>
                                <tr style="font-weight: bold;">
                                    <td colspan="2" class="center" style="text-transform: uppercase;">JUMLAH TOTAL</td>
                                    <td class="center">{{ $totalHours }} {{ $suffixText }}</td>
                                </tr>
                            </tfoot>
                        @endif
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
