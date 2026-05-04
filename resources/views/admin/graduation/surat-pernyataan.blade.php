<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Pernyataan/Fakta Integritas - {{ $user->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
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
        .page-pernyataan {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 20mm 25mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        }

        /* ═══════════════════════════════════════════
           SURAT PERNYATAAN/FAKTA INTEGRITAS
        ═══════════════════════════════════════════ */
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

            .page-pernyataan {
                margin: 0;
                box-shadow: none;
                width: 100%;
                padding: 20mm 20mm 20mm 25mm;
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

    {{-- ══════════════════════════════════════
         SURAT PERNYATAAN/FAKTA INTEGRITAS
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
                <td>{{ strtoupper(preg_replace('/\s*,\s*/', ', ', $student->birth_place_date ?? '—')) }}</td>
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
                <td>{{ $program1->name ?? '—' }}</td>
            </tr>
            {{-- <tr>
                <td class="label">Tahun Pelajaran</td>
                <td class="sep">:</td>
                <td>{{ $letter->academic_year ?? ($student->academicYears->first()->academic_year ?? '—') }}</td>
            </tr> --}}
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
                Majalengka, {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}<br>
                Yang menyatakan,<br>
                @if ($signature?->signature_data)
                    <img src="{{ $signature->signature_data }}" alt="Tanda Tangan" style="height: 60px;">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="nama-ttd">{{ $student->full_name }}</div>
                <div>NISN. {{ $student->national_student_number ?? '—' }}</div>
            </div>
        </div>

    </div>{{-- end .page-pernyataan --}}

</body>

</html>
