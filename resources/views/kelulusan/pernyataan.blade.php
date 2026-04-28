<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transkrip Nilai - SMKN 1 Talaga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }

        /* TOMBOL ACTIONS */
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print { background-color: #10b981; color: white; }
        .btn-print:hover { background-color: #059669; }

        /* HALAMAN PORTRAIT */
        .page-portrait {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm 15mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* KOP SURAT */
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 5px;
            padding-left: 100px;
            margin-bottom: 10px;
            position: relative;
            min-height: 100px;
        }

        .header img.logo {
            position: absolute;
            left: 0;
            top: 0;
            width: auto;
            height: 130px;
        }

        .header h3, .header h2, .header h4, .header p { margin: 0; }
        .header h4 { font-size: 14pt; font-weight: bold; }
        .header h2 { font-size: 18pt; font-weight: bold; }

        .header .address {
            font-family: Tahoma;
            font-size: 9pt;
            font-weight: normal;
            text-align: center;
            line-height: 1.4;
            color: #000;
        }

        .header .address a { text-decoration: none; color: #000; }

        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; margin-bottom: 4px; }
        .doc-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 2px 0;
            letter-spacing: 1px;
        }
        .doc-title .nomor { font-size: 10pt; font-weight: bold; margin: 0 0 12px 0; }

        /* INFO SISWA */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10pt;
        }
        .info-table td { padding: 1px 2px; vertical-align: top; border: none; }
        .info-table .label { width: 200px; }
        .info-table .sep   { width: 14px; }

        /* TABEL NILAI */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid black;
            padding: 4px 8px;
            vertical-align: middle;
        }
        .nilai-table thead th { text-align: center; font-weight: bold; background-color: white; }
        .nilai-table .col-no    { width: 35px; text-align: center; }
        .nilai-table .col-nilai { width: 60px; text-align: center; }
        .nilai-table .section-header td {
            font-weight: bold;
            background-color: white;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }
        .nilai-table .rata-rata td { font-weight: bold; text-align: center; }

        /* TANDA TANGAN */
        .ttd-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            font-size: 10pt;
        }

        .ttd-block { text-align: center; width: 240px; }
        .ttd-block .nama { font-weight: bold; text-decoration: underline; }

        /* Area signature — tinggi cukup untuk menampung gambar tanda tangan */
        .ttd-space {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }

        .ttd-space img.sig-img {
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
        }

        /* PRINT */
        @media print {
            .action-buttons { display: none !important; }
            body { background: none; margin: 0; padding: 0; }
            .page-portrait {
                margin: 0;
                padding: 10mm 15mm;
                box-shadow: none;
                width: 100%;
            }
        }

        @page { size: A4 portrait; margin: 0; }
    </style>
</head>

<body>

    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    <div class="page-portrait">

        <!-- KOP SURAT -->
        <div class="header">
            <img class="logo"
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Coat_of_arms_of_West_Java.svg/500px-Coat_of_arms_of_West_Java.svg.png"
                alt="Logo" />
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h2>CABANG DINAS PENDIDIKAN WILAYAH IX</h2>
            <h4>SEKOLAH MENENGAH KEJURUAN NEGERI 1 TALAGA</h4>
            <div class="address">
                Bidang Keahlian: Teknologi dan Rekayasa, Teknologi Informasi Komunikasi, Bisnis dan Manajemen<br />
                Kampus 1: Jalan Sekolah Nomor 20 Desa Talagakulon Kecamatan Talaga Kabupaten Majalengka<br />
                Kampus 2: Jalan Talaga-Bantarujeg Desa Mekarraharja Kecamatan Talaga Kabupaten Majalengka<br />
                Telpon <i class="fa-solid fa-phone"></i> (0233) 319238 &nbsp;
                FAX <i class="fa-solid fa-fax"></i> (0233) 319238 &nbsp;
                POS <i class="fa-solid fa-envelope"></i> 45463 &nbsp; NPSN: 20213872<br />
                Website <i class="fa-solid fa-globe"></i>
                <a href="http://www.smkn1talaga.sch.id">www.smkn1talaga.sch.id</a>
                &nbsp;&#8211;&nbsp; Email <i class="fa-solid fa-envelope"></i>
                <a href="mailto:admin@smkn1talaga.sch.id">admin@smkn1talaga.sch.id</a>
            </div>
        </div>

        <!-- JUDUL -->
        <div class="doc-title">
            <h2>TRANSKRIP NILAI</h2>
            <p class="nomor">No : 891/TU.01.01/SMK-Tlg/CADISDIKWIL.IX/2025</p>
        </div>

        <!-- INFO SISWA — data dari model User / relasi yang sesuai -->
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
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $user->tempat_lahir ?? '—' }}, {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d-m-Y') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa Nasional</td>
                <td class="sep">:</td>
                <td>{{ $user->nisn ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kelulusan</td>
                <td class="sep">:</td>
                <td>5 Mei 2026</td>
            </tr>
            <tr>
                <td class="label">Program Keahlian</td>
                <td class="sep">:</td>
                <td>{{ $user->program_keahlian ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Konsentrasi Keahlian</td>
                <td class="sep">:</td>
                <td>{{ $user->konsentrasi_keahlian ?? '—' }}</td>
            </tr>
        </table>

        <!-- TABEL NILAI -->
        <table class="nilai-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Mata Pelajaran</th>
                    <th class="col-nilai">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <!-- KELOMPOK A -->
                <tr class="section-header">
                    <td colspan="3" style="padding-left:8px;">A. Kelompok Mata Pelajaran Umum</td>
                </tr>
                <tr><td class="col-no">1</td><td>Pendidikan Agama Islam dan Budi Pekerti</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">2</td><td>Pendidikan Pancasila</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">3</td><td>Bahasa Indonesia</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">4</td><td>Pendidikan Jasmani, Olahraga dan Kesehatan</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">5</td><td>Sejarah</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">6</td><td>Seni Budaya</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">7</td><td>Muatan Lokal</td><td class="col-nilai"></td></tr>

                <!-- KELOMPOK B -->
                <tr class="section-header">
                    <td colspan="3" style="padding-left:8px;">B. Kelompok Mata Pelajaran Kejuruan</td>
                </tr>
                <tr><td class="col-no">1</td><td>Matematika</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">2</td><td>Bahasa Inggris</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">3</td><td>Informatika</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">4</td><td>Projek Ilmu Pengetahuan Alam dan Sosial</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">5</td><td>Dasar-dasar Teknik Jaringan Komputer dan Telekomunikasi</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">6</td><td>Konsentrasi Keahlian Teknik Jaringan Komputer dan Telekomunikasi</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">7</td><td>Projek Kreatif dan Kewirausahaan</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">8</td><td>Mata Pelajaran Pilihan (Bahasa Inggris)</td><td class="col-nilai"></td></tr>
                <tr><td class="col-no">9</td><td>Praktik Kerja Lapangan</td><td class="col-nilai"></td></tr>

                <!-- RATA-RATA -->
                <tr class="rata-rata">
                    <td colspan="2" style="text-align:center; font-weight:bold;">Rata-rata</td>
                    <td class="col-nilai">0.00</td>
                </tr>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="ttd-section">
            <div class="ttd-block">
                Kabupaten Majalengka, 24 Desember 2025<br />
                Kepala Sekolah,
                <div class="ttd-space">
                    {{-- Tanda tangan digital siswa — diambil dari tabel student_signatures --}}
                    @if($signature)
                        <img
                            class="sig-img"
                            src="{{ $signature->signature_data }}"
                            alt="Tanda Tangan {{ $user->name }}" />
                    @endif
                </div>
                <div class="nama">Muchamad Eki S.A., S.Kom.</div>
                <div>NIP. 197610012006041011</div>
            </div>
        </div>

    </div>

</body>
</html>
