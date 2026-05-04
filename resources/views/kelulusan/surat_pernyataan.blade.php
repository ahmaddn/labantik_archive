<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Pernyataan - {{ $user->name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }

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
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-print {
            background: #10b981;
            color: white;
        }

        .btn-print:hover {
            background: #059669;
        }

        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 20mm 25mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2.judul {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11pt;
        }

        .info-table td:nth-child(3) {
            text-transform: uppercase;
        }

        .info-table td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .info-table .label {
            width: 220px;
        }

        .info-table .sep {
            width: 12px;
        }

        ol {
            margin: 8px 0 12px 0;
            padding-left: 20px;
        }

        ol li {
            margin-bottom: 4px;
        }

        .ttd-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .ttd-block {
            text-align: center;
            width: 280px;
        }

        .ttd-block .signature-img {
            width: 200px;
            height: 80px;
            object-fit: contain;
            display: block;
            margin: 8px auto;
        }

        .ttd-block .nama {
            font-weight: bold;
            text-decoration: underline;
        }

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
                padding: 15mm 15mm 15mm 20mm;
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
        <button onclick="window.print()" class="btn btn-print">🖨 Print</button>
    </div>

    <div class="page">

        <h2 class="judul">Surat Pernyataan/Fakta Integritas</h2>

        <p>Saya yang bertanda tangan di bawah ini:</p>

        <table class="info-table">
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
                <td>{{ $program->program_name ?? '—' }}</td>
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

        <p>Menyatakan secara sadar dan sungguh-sungguh apabila saya dinyatakan lulus tidak akan melakukan:</p>

        <ol>
            <li>Hal-hal yang tidak terpuji, seperti mencorat-coret baju atau saran dan prasarana fasilitas umum.</li>
            <li>Konvoi kendaraan sehingga mengganggu pengguna jalan lainnya.</li>
            <li>Kumpul-kumpul pada tempat tertentu dengan melakukan hal yang tidak terpuji yang akan merusak nama baik
                diri, keluarga dan lembaga.</li>
        </ol>

        <p>Bila lulus saya bersedia:</p>

        <ol>
            <li>Sujud Syukur sebagai ungkapan kebahagiaan saya.</li>
            <li>Menyumbangkan seragam kepada pihak yang memerlukan.</li>
        </ol>

        <p>Bila saya melanggar ketentuan di atas dan terjadi hal negatif yang melibatkan saya dengan pihak berwajib maka
            saya tidak akan membawa nama sekolah dan sepenuhnya menjadi tanggung jawab saya.</p>

        <p>Demikian pernyataan saya dibuat dengan sadar tanpa paksaan dari pihak mana pun.</p>

        {{-- Tanda Tangan di pojok kanan bawah --}}
        <div class="ttd-section">
            <div class="ttd-block">
                Majalengka, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Yang menyatakan,<br>
                <img src="{{ $signature->signature_data }}" alt="Tanda Tangan" class="signature-img" />
                <div class="nama">{{ $user->name }}</div>
                <div>NISN. {{ $student->national_student_number ?? '—' }}</div>
            </div>
        </div>

    </div>
</body>

</html>
