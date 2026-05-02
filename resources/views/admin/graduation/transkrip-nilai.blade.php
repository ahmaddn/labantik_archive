<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transkrip Nilai - {{ $student->full_name }}</title>
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
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
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
            padding: 10mm 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            position: relative;
        }

        /* KOP SURAT */
        .header {
            text-align: center;
            padding-bottom: 5px;
            padding-left: 80px;
            margin-bottom: 5px;
            position: relative;
            min-height: 90px;
        }

        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: auto;
            height: 110px;
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
            margin-bottom: 10px;
        }

        .doc-title h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-title .nomor {
            font-size: 10pt;
            margin: 0;
        }

        /* INFO SISWA TABLE */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 1px 2px;
            vertical-align: top;
        }

        .info-table .label {
            width: 180px;
        }

        .info-table .sep {
            width: 10px;
        }

        /* NILAI TABLE */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        .nilai-table th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .nilai-table .col-no {
            width: 30px;
            text-align: center;
        }

        .nilai-table .col-mapel {
            text-align: left;
        }

        .nilai-table .col-semester {
            width: 25px;
            text-align: center;
        }

        .nilai-table .col-nr {
            width: 30px;
            text-align: center;
        }

        .nilai-table .col-na {
            width: 40px;
            text-align: center;
            font-weight: bold;
        }

        .nilai-table .group-header {
            font-weight: bold;
            font-style: italic;
        }

        .nilai-table .rata-rata {
            font-weight: bold;
            text-align: right;
        }

        /* TANDA TANGAN */
        .ttd-section {
            margin-top: 15px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-block {
            width: 250px;
            text-align: left;
        }

        .ttd-space {
            height: 50px;
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* PRINT STYLES */
        @media print {
            .action-buttons {
                display: none !important;
            }

            body {
                background: none;
                padding: 0;
            }

            .page {
                margin: 0;
                box-shadow: none;
                padding: 10mm 15mm;
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
        <a href="{{ route('admin.graduation.index') }}" class="btn btn-back"
            style="background-color: #6b7280; color: white;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Coat_of_arms_of_West_Java.svg/500px-Coat_of_arms_of_West_Java.svg.png"
                alt="Logo" />
            <div class="line1">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
            <div class="line2">CABANG DINAS PENDIDIKAN WILAYAH IX</div>
            <div class="line3">SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</div>
            <div class="address">
                Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi dan Komunikasi, Bisnis dan Manajemen<br />
                Kampus 1: Jalan Sekolah Nomor 20 Desa Talagakulon Kecamatan Talaga Kabupaten Majalengka<br />
                Kampus 2: Jalan Talaga-Bantarujeg Desa Mekarraharja Kecamatan Talaga Kabupaten Majalengka<br />
                Telpon ☎ (0233) 319238 FAX ☎ (0233) 319238 POS ✉ 45463 NPSN: 20213872<br />
                Website www.smkn1talaga.sch.id - Email ✉ admin@smkn1talaga.sch.id
            </div>
        </div>
        <div class="header-border-top"></div>
        <div class="header-border-thin"></div>
        {{-- JUDUL --}}
        <div class="doc-title">
            <h2>TRANSKRIP NILAI</h2>
            <div class="nomor">{{ $letter->transcript_letter_number ?? ($letter->letter_number ?? '—') }}</div>
        </div>

        {{-- INFO SISWA --}}
        <table class="info-table">
            <tr>
                <td class="label">Satuan Pendidikan</td>
                <td class="sep">:</td>
                <td>SMKN 1 Talaga</td>
            </tr>
            <tr>
                <td class="label">Nomor Pokok Sekolah Nasional</td>
                <td class="sep">:</td>
                <td>20213872</td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="sep">:</td>
                <td style="font-weight:bold;">{{ strtoupper($student->full_name) }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $student->birth_place_date ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa Nasional</td>
                <td class="sep">:</td>
                <td>{{ $student->national_student_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Ijazah</td>
                <td class="sep">:</td>
                <td>{{ $student->diploma_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kelulusan</td>
                <td class="sep">:</td>
                <td>{{ $letter ? \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('j F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Program Keahlian</td>
                <td class="sep">:</td>
                <td>{{ $program1->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Konsentrasi Keahlian</td>
                <td class="sep">:</td>
                <td>{{ $program->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Pelajaran</td>
                <td class="sep">:</td>
                <td>{{ $letter->academic_year ?? ($student->academicYears->first()->academic_year ?? '—') }}</td>
            </tr>
        </table>

        {{-- NILAI TABLE --}}
        <table class="nilai-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">No</th>
                    <th rowspan="2">Mata Pelajaran</th>
                    <th colspan="6">Nilai Rapor</th>
                    <th rowspan="2" class="col-nr">NR</th>
                </tr>
                <tr>
                    <th class="col-semester">1</th>
                    <th class="col-semester">2</th>
                    <th class="col-semester">3</th>
                    <th class="col-semester">4</th>
                    <th class="col-semester">5</th>
                    <th class="col-semester">6</th>
                </tr>
            </thead>
            <tbody>
                {{-- A. Kelompok Mata Pelajaran Umum --}}
                <tr class="group-header">
                    <td colspan="9">A. Kelompok Mata Pelajaran Umum</td>
                </tr>
                @php
                    $noUmum = 1;
                    $groupedUmum = [];
                    foreach ($mapelUmum as $m) {
                        $joinVal = $m->mapel->join ?? 0;
                        $key = $joinVal == 0 ? 'solo_' . $m->id : 'grp_' . $joinVal;
                        $groupedUmum[$key][] = $m;
                    }
                @endphp

                @foreach ($groupedUmum as $key => $group)
                    @php
                        $rowspan = count($group);
                        $g =
                            collect($group)->first(
                                fn($m) => $m->score !== null || $m->nr !== null || $m->sem_1 !== null,
                            ) ?? $group[0];
                    @endphp
                    @foreach ($group as $idx => $m)
                        <tr>
                            @if ($idx === 0)
                                <td class="col-no"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $noUmum }}
                                </td>
                            @endif
                            <td class="col-mapel">{{ $m->mapel->name }}</td>
                            @if ($idx === 0)
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_1 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_2 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_3 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_4 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_5 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_6 }}</td>
                                <td class="col-nr"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->nr }}</td>
                            @endif
                        </tr>
                    @endforeach
                    @php $noUmum++; @endphp
                @endforeach

                {{-- B. Kelompok Mata Pelajaran Kejuruan --}}
                <tr class="group-header">
                    <td colspan="9">B. Kelompok Mata Pelajaran Kejuruan</td>
                </tr>
                @php
                    $noJurusan = 1;
                    $groupedJurusan = [];
                    foreach ($mapelJurusan as $m) {
                        $joinVal = $m->mapel->join ?? 0;
                        $key = $joinVal == 0 ? 'solo_' . $m->id : 'grp_' . $joinVal;
                        $groupedJurusan[$key][] = $m;
                    }
                @endphp

                @foreach ($groupedJurusan as $key => $group)
                    @php
                        $rowspan = count($group);
                        $g =
                            collect($group)->first(
                                fn($m) => $m->score !== null || $m->nr !== null || $m->sem_1 !== null,
                            ) ?? $group[0];
                    @endphp
                    @foreach ($group as $idx => $m)
                        <tr>
                            @if ($idx === 0)
                                <td class="col-no"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $noJurusan }}
                                </td>
                            @endif
                            <td class="col-mapel">{{ $m->mapel->name }}</td>
                            @if ($idx === 0)
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_1 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_2 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_3 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_4 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_5 }}</td>
                                <td class="col-semester"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->sem_6 }}</td>
                                <td class="col-nr"
                                    @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $g->nr }}</td>
                            @endif
                        </tr>
                    @endforeach
                    @php $noJurusan++; @endphp
                @endforeach

                <tr class="rata-rata">
                    <td colspan="8" style="text-align: center;">Rata-rata</td>
                    <td class="col-nr">{{ $rataRata }}</td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 8pt; margin-top: 5px; font-style: italic;">
            * NR = Rata-rata Nilai Rapor
        </div>

        {{-- TANDA TANGAN --}}
        <div class="ttd-section">
            <div class="ttd-block">
                Talaga,
                {{ $letter ? \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('j F Y') : '-' }}<br />
                Kepala SMK Negeri 1 Talaga,
                <div class="ttd-space"></div>
                <div class="ttd-name">
                    {{ $principal->employee->full_name ?? ($principal->name ?? 'Muchamad Eki S.A., S.Kom.') }}</div>
                <div>{{ $principal->employee->functional_position ?? 'Penata Tingkat I/IIId' }}</div>
                <div>NIP. {{ $principal->employee->nip ?? '197610012006041011' }}</div>
            </div>
        </div>
    </div>
</body>

</html>
