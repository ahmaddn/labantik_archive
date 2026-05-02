<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Export Surat Kelulusan - Semua Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            line-height: 1.2;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
            font-size: 10pt;
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
            padding: 8mm 15mm 40mm 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            position: relative;
        }

        /* ═══════════════════════════════════════════
           SURAT KETERANGAN LULUS
        ═══════════════════════════════════════════ */
        .header {
            text-align: center;
            padding-left: 90px;
            position: relative;
            min-height: 100px;
        }

        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: auto;
            height: 135px;
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

        .doc-title {
            text-align: center;
            margin: 0px 0 4px 0;
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

        .pembuka {
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 8px;
            text-align: justify;
        }

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
            font-weight: normal;
        }

        .nilai-table .section-header td {
            font-weight: bold;
        }

        .nilai-table .rata-rata td {
            font-weight: bold;
            text-align: center;
        }

        .ttd-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-top: 10px;
            font-size: 10pt;
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

        .qr-block {
            text-align: left;
            margin-bottom: 5px;
        }

        /* ── QR FOOTER ── */
        .doc-qr-footer {
            margin-top: 14px;
            padding: 10px 0;
            font-size: 7.5pt;
            font-family: Arial, sans-serif;
            position: absolute;
            
            left: 15mm;
            right: 15mm;
        }
        .doc-qr-footer-text {
            line-height: 1.5;
            color: #222;
        }
        .doc-qr-footer-text strong {
            display: block;
            font-size: 8pt;
            margin-bottom: 2px;
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
                padding: 8mm 15mm 0mm 15mm;
                page-break-after: always;
                break-after: page;
            }

            .page:last-child {
                page-break-after: auto;
                break-after: auto;
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
        <a href="{{ route('admin.graduation.index') }}" class="btn btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    {{-- Loop semua data kelulusan --}}
    @foreach ($data as $item)
        <div class="page">

            {{-- KOP SURAT --}}
            <div class="header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Coat_of_arms_of_West_Java.svg/500px-Coat_of_arms_of_West_Java.svg.png"
                    alt="Logo" />
                <div class="line1">PEMERINTAH PROVINSI JAWA BARAT</div>
                <div class="line2">CABANG DINAS PENDIDIKAN WILAYAH IX</div>
                <div class="line3">SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</div>
                <div class="address">
                    Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi dan Komunikasi, Bisnis dan
                    Manajemen<br />
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
                <div class="nomor">Nomor : {{ $item->letter->letter_number ?? '—' }}</div>
            </div>

            {{-- TEKS PEMBUKA --}}
            <div class="pembuka">
                @if ($item->letter)
                    @php
                        $displayStatement = str_replace(
                            '[TAHUN_PELAJARAN]',
                            $item->letter->academic_year ?? '',
                            $item->letter->statement,
                        );
                    @endphp
                    <p>{{ $displayStatement }}</p>
                    @php
                        $contentLines = array_filter(array_map('trim', explode("\n", $item->letter->content)));
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

            <div style="margin-bottom:6px; font-size:10pt;">Menerangkan bahwa :</div>

            {{-- INFO SISWA --}}
            <table class="info-table">
                <tr>
                    <td class="label" style="padding:0;">Nama</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ strtoupper($item->student->full_name ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Tempat, Tanggal Lahir</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ strtoupper($item->student->birth_place_date ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Nama Orang Tua/Wali</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ strtoupper($item->student->guardian_name ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Nomor Induk Siswa</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ $item->student->student_number ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Nomor Induk Siswa Nasional</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ $item->student->national_student_number ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Program Keahlian</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ strtoupper($item->program1->name ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Konsentrasi Keahlian</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">{{ strtoupper($item->program->name ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Tahun Pelajaran</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td style="padding:0;">
                        {{ $item->letter->academic_year ?? ($item->student->academicYears->first()->academic_year ?? '—') }}
                    </td>
                </tr>
                <tr>
                    <td class="label" style="padding:0;">Dinyatakan</td>
                    <td class="sep" style="padding:0;">:</td>
                    <td class="dinyatakan-lulus" style="padding:0;">LULUS</td>
                </tr>
            </table>

            <div style="font-size:10pt; margin-bottom:6px;">dengan nilai sebagai berikut :</div>

            {{-- TABEL NILAI --}}
            <table class="nilai-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Mata Pelajaran</th>
                        <th class="col-nilai">Nilai</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ── A. UMUM ──────────────────────────────── --}}
                    <tr class="section-header">
                        <td colspan="3" style="font-weight:bold;">
                            A. Kelompok Mata Pelajaran Umum Muatan Nasional
                        </td>
                    </tr>

                    @php
                        $noUmum = 1;
                        $groupedUmum = [];
                        foreach ($item->mapelUmum as $m) {
                            $joinVal = $m->mapel->join ?? 0;
                            $key = $joinVal == 0 ? 'solo_' . $m->id : 'grp_' . $joinVal;
                            $groupedUmum[$key][] = $m;
                        }
                    @endphp


                    @foreach ($groupedUmum as $key => $group)
                        @php
                            $rowspan = count($group);
                            $score = '-';
                            if ($group[0]->mapel->has_na) {
                                $foundScore = collect($group)->first(fn($m) => $m->score !== null)?->score;
                                $score = $foundScore !== null ? $foundScore : '';
                            }
                        @endphp
                        @foreach ($group as $idx => $mapel)
                            <tr>
                                @if ($idx === 0)
                                    <td class="col-no" style="padding:0px; vertical-align:middle;"
                                        @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                        {{ $noUmum }}
                                    </td>
                                @endif
                                <td style="padding:2px; font-weight: normal;">{{ $mapel->mapel->name }}</td>
                                @if ($idx === 0)
                                    <td class="col-nilai" style="padding:0px; vertical-align:middle;"
                                        @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                        {{ $score }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        @php $noUmum++; @endphp
                    @endforeach

                    {{-- ── B. KEJURUAN ─────────────────────────── --}}
                    <tr class="section-header">
                        <td colspan="3" style="font-weight:bold;">
                            B. Kelompok Mata Pelajaran Kejuruan
                        </td>
                    </tr>
                    @php
                        $noJurusan = 1;
                        $groupedJurusan = [];
                        foreach ($item->mapelJurusan as $m) {
                            $joinVal = $m->mapel->join ?? 0;
                            $key = $joinVal == 0 ? 'solo_' . $m->id : 'grp_' . $joinVal;
                            $groupedJurusan[$key][] = $m;
                        }
                    @endphp

                    @foreach ($groupedJurusan as $key => $group)
                        @php
                            $rowspan = count($group);
                            $score = '-';
                            if ($group[0]->mapel->has_na) {
                                $foundScore = collect($group)->first(fn($m) => $m->score !== null)?->score;
                                $score = $foundScore !== null ? $foundScore : '';
                            }
                        @endphp

                        @foreach ($group as $idx => $mapel)
                            <tr>
                                @if ($idx === 0)
                                    <td class="col-no" style="padding:0px; vertical-align:middle;"
                                        @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                        {{ $noJurusan }}
                                    </td>
                                @endif
                                <td style="padding:2px;">{{ $mapel->mapel->name }}</td>
                                @if ($idx === 0)
                                    <td class="col-nilai" style="padding:0px; vertical-align:middle;"
                                        @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                        {{ $score }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        @php $noJurusan++; @endphp
                    @endforeach

                    {{-- RATA-RATA --}}
                    <tr class="rata-rata">
                        <td colspan="2" style="text-align:center; font-weight:bold;">Rata-Rata</td>
                        <td class="col-nilai" style="padding:0px;">{{ $item->rataRata ?? '' }}</td>
                    </tr>

                </tbody>
            </table>

            {{-- TANDA TANGAN --}}
            <div class="ttd-section">
                <div class="qr-block">
                    @php
                        $verifyUrl = route('graduation.verify', $item->graduation->uuid);
                        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($verifyUrl) . '&size=100&margin=1&centerImageUrl=' . urlencode('https://smkn1talaga.sch.id/assets/images/logosmk.png');
                    @endphp
                    <img src="{{ $qrUrl }}" alt="QR Verifikasi" style="width: 80px; height: 80px;" />
                </div>
                <div class="ttd-block">
                    @if ($item->letter)
                        Talaga,
                        {{ \Carbon\Carbon::parse($item->letter->graduation_date)->translatedFormat('j F Y') }}<br />
                    @else
                        Talaga, ___________________<br />
                    @endif
                    Kepala SMK Negeri 1 Talaga,
                    <div class="ttd-space"></div>
                    <div class="nama">
                        {{ $item->principal->employee->full_name ?? ($item->principal->name ?? 'Muchamad Eki S.A., S.Kom.') }}
                    </div>
                    <div>{{ $item->principal->employee->functional_position ?? 'Penata Tingkat I/IIId' }}</div>
                    <div>NIP. {{ $item->principal->employee->nip ?? '197610012006041011' }}</div>
                </div>
            </div>

            {{-- QR CODE FOOTER --}}
            <div class="doc-qr-footer">
                <div class="doc-qr-footer-text">
                    <strong>Verifikasi Keaslian Dokumen</strong>
                    Scan QR Code ini untuk memverifikasi keaslian Surat Kelulusan atas nama
                    <strong style="display:inline; font-size:inherit;">{{ strtoupper($item->student->full_name ?? '—') }}</strong>.
                    Atau kunjungi: <em>{{ route('graduation.verify', $item->graduation->uuid) }}</em>
                </div>
            </div>

        </div>
    @endforeach

</body>

</html>
