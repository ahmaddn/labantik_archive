<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Kelulusan - {{ $user->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }

        /* ── ACTION BUTTONS ── */
        .action-buttons {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            max-width: 210mm;
            margin: 0 auto 20px auto;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all .3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background-color: #10b981;
            color: white;
        }

        .btn-print:hover {
            background-color: #059669;
        }

        /* ── PAGE (A4) ── */
        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 20px auto;
            padding: 8mm 15mm 10mm 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        }

        /* ═══════════════════════════════════════════
           HALAMAN 1 — SURAT KETERANGAN LULUS
        ═══════════════════════════════════════════ */
        /* KOP SURAT */
        .header {
            text-align: center;
            padding-bottom: 5px;
            padding-left: 90px;
            margin-bottom: 10px;
            position: relative;
            min-height: 100px;
        }

        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: auto;
            height: 100px;
        }

        .header .line1 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
        }

        .header .line2 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
        }

        .header .line3 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
        }

        .header .address {
            font-size: 8.5pt;
            font-weight: normal;
            text-align: center;
            line-height: 1.2;
            color: #000;
            margin-top: 4px;
        }

        .header-border-top {
            border-top: 3px solid #000;
            margin-top: 5px;
        }

        .header-border-thin {
            border-top: 1px solid #000;
            margin-top: 2px;
        }

        /* JUDUL */
        .doc-title {
            text-align: center;
            margin: 12px 0 4px 0;
        }

        .doc-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 2px 0;
            letter-spacing: 1px;
        }

        .doc-title .nomor {
            font-size: 10pt;
            margin: 0 0 8px 0;
        }

        /* TEKS PEMBUKA */
        .pembuka {
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 8px;
            text-align: justify;
        }

        /* INFO SISWA */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        .info-table td {
            padding: 1px 2px;
            vertical-align: top;
            border: none;
        }

        .info-table .label {
            width: 210px;
        }

        .info-table .sep {
            width: 14px;
        }

        .dinyatakan-lulus {
            font-weight: bold;
        }

        /* TABEL NILAI */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid black;
            padding: 3px 7px;
            vertical-align: middle;
        }

        .nilai-table thead th {
            text-align: center;
            font-weight: bold;
        }

        .nilai-table .col-no {
            width: 35px;
            text-align: center;
        }

        .nilai-table .col-nilai {
            width: 70px;
            text-align: center;
        }

        .nilai-table .section-header td {
            font-weight: bold;
        }

        .nilai-table .rata-rata td {
            font-weight: bold;
            text-align: center;
        }

        /* TANDA TANGAN KELULUSAN */
        .ttd-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
            font-size: 10pt;
            margin-left: 480px;
        }

        .ttd-block {
            text-align: left;
            width: 260px;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-block .nama {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ═══════════════════════════════════════════
           HALAMAN 2 — SURAT PERNYATAAN/FAKTA INTEGRITAS
           PAGE BREAK sebelum halaman ini
        ═══════════════════════════════════════════ */
        .page-pernyataan {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 20mm 25mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        }

        h2.judul-pernyataan {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .pernyataan-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11pt;
        }

        .pernyataan-info-table td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .pernyataan-info-table .label {
            width: 220px;
        }

        .pernyataan-info-table .sep {
            width: 12px;
        }

        .pernyataan-text {
            font-size: 11pt;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .pernyataan-ol {
            margin: 8px 0 12px 0;
            padding-left: 20px;
            font-size: 11pt;
            line-height: 1.6;
        }

        .pernyataan-ol li {
            margin-bottom: 4px;
        }

        .ttd-pernyataan {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            padding-left: 400px;
        }

        .ttd-pernyataan-block {
            text-align: left;
            width: 280px;
            font-size: 11pt;
            line-height: 1.6;
        }

        .ttd-pernyataan-block .signature-img {
            width: 200px;
            height: 80px;
            object-fit: contain;
            display: block;
        }

        .ttd-pernyataan-block .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ── PRINT ── */
        @media print {
            .action-buttons {
                display: none !important;
            }

            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                padding: 8mm 15mm 10mm 15mm;
            }

            .page-pernyataan {
                margin: 0;
                box-shadow: none;
                width: 100%;
                padding: 20mm 20mm 20mm 25mm;
                /* Selalu mulai di halaman baru */
                page-break-before: always;
                break-before: page;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    {{-- ══════════════════════════════════════
         HALAMAN 1: SURAT KETERANGAN LULUS
    ══════════════════════════════════════ --}}
    <div class="page">

        {{-- KOP SURAT --}}
        <div class="header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Coat_of_arms_of_West_Java.svg/500px-Coat_of_arms_of_West_Java.svg.png"
                alt="Logo" />
            <div class="line1">PEMERINTAH PROVINSI JAWA BARAT</div>
            <div class="line2">CABANG DINAS PENDIDIKAN WILAYAH IX</div>
            <div class="line3">SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</div>
            <div class="address">
                Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi dan Komunikasi, Bisnis dan Manajemen<br />
                Kampus 1 : Jalan Sekolah No.20 Desa Talagakulon Kecamatan Talaga Kabupaten Majalengka<br />
                Kampus 2 : Jalan Talaga - Bantarujeg Desa Mekarraharja Kecamatan Talaga Kabupaten Majalengka<br />
                Telepon (0233) 319238 &nbsp; Fax (0233) 319238 &nbsp; Kode Pos 45463 &nbsp; NPSN 20.21.38.72<br />
                Website https://smkn1talaga.sch.id/ &nbsp; E-mail: mailsmkn1talaga@gmail.com
            </div>
        </div>
        <div class="header-border-top"></div>
        <div class="header-border-thin"></div>

        {{-- JUDUL --}}
        <div class="doc-title">
            <h2>SURAT KETERANGAN LULUS</h2>
            <div class="nomor">Nomor : {{ $letter->letter_number ?? '—' }}</div>
        </div>

        {{-- TEKS PEMBUKA (dari google_graduation_letters) --}}
        <div class="pembuka">
            @if ($letter)
                <p>{{ $letter->statement }}</p>
                @php
                    $contentLines = array_filter(array_map('trim', explode("\n", $letter->content)));
                @endphp
                @if (!empty($contentLines))
                    <ol style="margin: 6px 0 6px 20px; padding: 0;">
                        @foreach ($contentLines as $line)
                            <li style="margin-bottom: 3px;">{{ $line }}</li>
                        @endforeach
                    </ol>
                @endif
            @else
                <p>—</p>
            @endif
        </div>

        <div style="margin-bottom:6px; font-size:10pt;">Menerangkan Bahwa</div>

        {{-- INFO SISWA --}}
        <table class="info-table">
            <tr>
                <td class="label" style="padding:0;">Nama</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ strtoupper($student->full_name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Tempat, Tanggal Lahir</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ strtoupper($student->birth_place_date ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Nama Orang Tua/Wali</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ strtoupper($student->guardian_name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Nomor Induk Siswa</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ $student->student_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Nomor Induk Siswa Nasional</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ $student->national_student_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Program Keahlian</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ strtoupper($program1->program1_name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Konsentrasi Keahlian</td>
                <td class="sep" style="padding:0;">:</td>
                <td style="padding:0;">{{ strtoupper($program->program_name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label" style="padding:0;">Dinyatakan</td>
                <td class="sep" style="padding:0;">:</td>
                <td class="dinyatakan-lulus" style="padding:0;">LULUS</td>
            </tr>
        </table>

        <div style="font-size:10pt; margin-bottom:6px;">dengan nilai sebagai berikut :</div>

        {{-- ═══════════════════════════════════════════
         TABEL NILAI — dinamis dari google_mapel
         + google_graduation_mapel
    ═══════════════════════════════════════════ --}}
        <table class="nilai-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Mata Pelajaran (Kurikulum Merdeka)</th>
                    <th class="col-nilai">Nilai</th>
                </tr>
            </thead>
            <tbody>

                {{-- ── A. UMUM ─────────────────────────────── --}}
                <tr class="section-header">
                    <td colspan="3" style="font-weight:bold;">
                        A. Kelompok Mata Pelajaran Umum Muatan Nasional
                    </td>
                </tr>

                @php
                    $noUmum = 1;
                    $skipUmum = 0; // sisa baris yang dilewati karena rowspan
                @endphp

                @foreach ($mapelUmum as $mapel)
                    @php
                        $rowspan = intval($mapel->join ?? 1);
                        $score = $mapel->score !== null ? $mapel->score : '';
                    @endphp

                    @if ($skipUmum > 0)
                        {{-- Baris lanjutan rowspan: tidak tampilkan No & Nilai --}}
                        <tr>
                            <td style="padding:2px;">{{ $mapel->name }}</td>
                        </tr>
                        @php $skipUmum--; @endphp
                    @else
                        <tr>
                            <td class="col-no" style="padding:0px; vertical-align:middle;"
                                @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                {{ $noUmum++ }}
                            </td>
                            <td style="padding:2px;">{{ $mapel->name }}</td>
                            <td class="col-nilai" style="padding:0px; vertical-align:middle;"
                                @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                {{ $score }}
                            </td>
                        </tr>
                        @php $skipUmum = $rowspan - 1; @endphp
                    @endif
                @endforeach

                {{-- ── B. KEJURUAN ─────────────────────────── --}}
                <tr class="section-header">
                    <td colspan="3" style="font-weight:bold;">
                        B. Kelompok Mata Pelajaran Kejuruan
                    </td>
                </tr>

                @php
                    $noJurusan = 1;
                    $skipJurusan = 0;
                @endphp

                @foreach ($mapelJurusan as $mapel)
                    @php
                        $rowspan = intval($mapel->join ?? 1);
                        $score = $mapel->score !== null ? $mapel->score : '';
                    @endphp

                    @if ($skipJurusan > 0)
                        <tr>
                            <td style="padding:2px;">{{ $mapel->name }}</td>
                        </tr>
                        @php $skipJurusan--; @endphp
                    @else
                        <tr>
                            <td class="col-no" style="padding:0px; vertical-align:middle;"
                                @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                {{ $noJurusan++ }}
                            </td>
                            <td style="padding:2px;">{{ $mapel->name }}</td>
                            <td class="col-nilai" style="padding:0px; vertical-align:middle;"
                                @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                {{ $score }}
                            </td>
                        </tr>
                        @php $skipJurusan = $rowspan - 1; @endphp
                    @endif
                @endforeach

                {{-- ── RATA-RATA ─────────────────────────────── --}}
                <tr class="rata-rata">
                    <td colspan="2" style="text-align:center; font-weight:bold;">Rata-Rata</td>
                    <td class="col-nilai" style="padding:0px;">{{ $rataRata ?? '' }}</td>
                </tr>

            </tbody>
        </table>

        {{-- TANDA TANGAN KEPALA SEKOLAH --}}
        <div class="ttd-section">
            <div class="ttd-block">
                @if ($letter)
                    Talaga, {{ \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('j F Y') }}<br />
                @else
                    Talaga, ___________________<br />
                @endif
                Kepala SMK Negeri 1 Talaga,
                <div class="ttd-space"></div>
                <div class="nama">Muchamad Eki S.A., S.Kom.</div>
                <div>Pembina Tingkat I/IVb</div>
                <div>NIP. 197610012006041011</div>
            </div>
        </div>

    </div>{{-- end .page (halaman 1) --}}

    {{-- ══════════════════════════════════════
         HALAMAN 2: SURAT PERNYATAAN/FAKTA INTEGRITAS
         page-break-before: always (di CSS print)
    ══════════════════════════════════════ --}}
    <div class="page-pernyataan">

        <h2 class="judul-pernyataan">Surat Pernyataan/Fakta Integritas</h2>

        <p class="pernyataan-text">Saya yang bertanda tangan di bawah ini:</p>

        <table class="pernyataan-info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="sep">:</td>
                <td>{{ strtoupper($student->full_name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="label">Tempat/Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $student->birth_place_date ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="sep">:</td>
                <td>{{ $student->national_student_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">NPSN</td>
                <td class="sep">:</td>
                <td>20213872</td>
            </tr>
            <tr>
                <td class="label">Nama Sekolah</td>
                <td class="sep">:</td>
                <td>SMK Negeri 1 Talaga</td>
            </tr>
            <tr>
                <td class="label">Program Keahlian</td>
                <td class="sep">:</td>
                <td>{{ $program1->program1_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $student->address ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Orang Tua/Wali</td>
                <td class="sep">:</td>
                <td>{{ $student->guardian_name ?? '—' }}</td>
            </tr>
        </table>

        <p class="pernyataan-text">Menyatakan secara sadar dan sungguh-sungguh apabila saya dinyatakan lulus tidak akan
            melakukan:</p>

        <ol class="pernyataan-ol">
            <li>Hal-hal yang tidak terpuji, seperti mencorat-coret baju atau sarana dan prasarana fasilitas umum.</li>
            <li>Konvoi kendaraan sehingga mengganggu pengguna jalan lainnya.</li>
            <li>Kumpul-kumpul pada tempat tertentu dengan melakukan hal yang tidak terpuji yang akan merusak nama baik
                diri, keluarga dan lembaga.</li>
        </ol>

        <p class="pernyataan-text">Bila lulus saya bersedia:</p>

        <ol class="pernyataan-ol">
            <li>Sujud Syukur sebagai ungkapan kebahagiaan saya.</li>
            <li>Menyumbangkan seragam kepada pihak yang memerlukan.</li>
        </ol>

        <p class="pernyataan-text">
            Bila saya melanggar ketentuan di atas dan terjadi hal negatif yang melibatkan saya dengan pihak berwajib
            maka saya tidak akan membawa nama sekolah dan sepenuhnya menjadi tanggung jawab saya.
        </p>

        <p class="pernyataan-text">Demikian pernyataan saya dibuat dengan sadar tanpa paksaan dari pihak mana pun.</p>

        <div class="ttd-pernyataan">
            <div class="ttd-pernyataan-block">
                Majalengka, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Yang menyatakan,<br>
                <img src="{{ $signature->signature_data }}" alt="Tanda Tangan" class="signature-img" />
                <div class="nama-ttd">{{ $user->name }}</div>
                <div>NISN. {{ $student->national_student_number ?? '—' }}</div>
            </div>
        </div>

    </div>{{-- end .page-pernyataan (halaman 2) --}}

</body>

</html>
