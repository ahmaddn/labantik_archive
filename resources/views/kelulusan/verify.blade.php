<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Dokumen Kelulusan — SMKN 1 Talaga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #1b84ff;
            --primary-d: #0d6efd;
            --green:     #22c55e;
            --green-d:   #16a34a;
            --gray-50:   #f8fafc;
            --gray-100:  #f1f5f9;
            --gray-200:  #e2e8f0;
            --gray-400:  #94a3b8;
            --gray-500:  #64748b;
            --gray-700:  #334155;
            --gray-900:  #0f172a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 60%, #1a2744 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── CARD ── */
        .card {
            background: white;
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.05);
        }

        /* ── CARD HEADER ── */
        .card-header {
            background: linear-gradient(135deg, #1b84ff 0%, #0d6efd 100%);
            padding: 28px 28px 22px;
            position: relative;
            overflow: hidden;
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -30px; right: -30px;
            width: 120px; height: 120px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
        }
        .card-header::before {
            content: '';
            position: absolute;
            top: -20px; left: -20px;
            width: 80px; height: 80px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }

        .school-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }
        .school-logo {
            width: 52px; height: 52px;
            border-radius: 12px;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .school-logo svg { width: 28px; height: 28px; color: white; }
        .school-name { color: white; }
        .school-name .line1 { font-size: 11px; font-weight: 500; opacity: .8; letter-spacing: .5px; text-transform: uppercase; }
        .school-name .line2 { font-size: 15px; font-weight: 700; line-height: 1.2; }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(34,197,94,.2);
            border: 1px solid rgba(34,197,94,.4);
            color: #86efac;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 100px;
            letter-spacing: .3px;
        }
        .verified-badge .dot {
            width: 7px; height: 7px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(.85); }
        }

        /* ── AVATAR + NAME ── */
        .student-hero {
            padding: 28px 28px 0;
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .avatar {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #1b84ff, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(27,132,255,.3);
        }
        .student-name-block .label-tag {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 4px;
        }
        .student-name-block .full-name {
            font-size: 20px;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.2;
        }

        /* ── STATUS BANNER ── */
        .status-banner {
            margin: 20px 28px 0;
            background: linear-gradient(135deg, #dcfce7, #f0fdf4);
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .status-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .status-icon svg { width: 22px; height: 22px; color: white; }
        .status-text .status-label { font-size: 11px; font-weight: 600; color: #16a34a; text-transform: uppercase; letter-spacing: .5px; }
        .status-text .status-value { font-size: 18px; font-weight: 800; color: #15803d; margin-top: 1px; }

        /* ── INFO GRID ── */
        .info-section {
            padding: 22px 28px;
        }
        .info-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
            margin-bottom: 14px;
        }
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 14px;
            background: var(--gray-50);
            border-radius: 10px;
            border: 1px solid var(--gray-100);
        }
        .info-row .info-label {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 500;
            flex-shrink: 0;
            min-width: 120px;
        }
        .info-row .info-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-900);
            text-align: right;
            word-break: break-word;
        }
        .info-row .info-value.highlight {
            color: var(--primary);
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: #eff6ff;
            padding: 2px 8px;
            border-radius: 6px;
        }

        /* ── DIVIDER ── */
        .divider {
            height: 1px;
            background: var(--gray-100);
            margin: 0 28px;
        }

        /* ── FOOTER ── */
        .card-footer {
            padding: 18px 28px 24px;
            text-align: center;
        }
        .footer-note {
            font-size: 11px;
            color: var(--gray-400);
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--gray-500);
            font-weight: 500;
        }
        .footer-brand .dot { width: 4px; height: 4px; background: var(--gray-400); border-radius: 50%; }

        /* ── NOT FOUND ── */
        .not-found {
            text-align: center;
            color: white;
        }
        .not-found h1 { font-size: 80px; font-weight: 800; opacity: .3; }
        .not-found p { font-size: 18px; margin-top: 8px; opacity: .7; }
    </style>
</head>
<body>

@if(!$student)
    <div class="not-found">
        <h1>404</h1>
        <p>Dokumen tidak ditemukan.</p>
    </div>
@else
    <div class="card">

        {{-- HEADER --}}
        <div class="card-header">
            <div class="school-row">
                <div class="school-logo">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0-6L3 9m9 5l9-5"/>
                    </svg>
                </div>
                <div class="school-name">
                    <div class="line1">SMK Negeri 1 Talaga</div>
                    <div class="line2">Verifikasi Dokumen Kelulusan</div>
                </div>
            </div>
            <div class="verified-badge">
                <span class="dot"></span>
                Dokumen Terverifikasi
            </div>
        </div>

        {{-- STUDENT HERO --}}
        <div class="student-hero">
            <div class="avatar">{{ strtoupper(substr($student->full_name ?? 'S', 0, 2)) }}</div>
            <div class="student-name-block">
                <div class="label-tag">Nama Siswa</div>
                <div class="full-name">{{ strtoupper($student->full_name ?? '—') }}</div>
            </div>
        </div>

        {{-- STATUS BANNER --}}
        <div class="status-banner">
            <div class="status-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="status-text">
                <div class="status-label">Status Kelulusan</div>
                <div class="status-value">LULUS</div>
            </div>
        </div>

        {{-- INFO SISWA --}}
        <div class="info-section">
            <div class="info-section-title">Informasi Siswa</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Satuan Pendidikan</span>
                    <span class="info-value">SMKN 1 Talaga</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NPSN</span>
                    <span class="info-value highlight">20213872</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tempat, Tanggal Lahir</span>
                    <span class="info-value">{{ $student->birth_place_date ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NISN</span>
                    <span class="info-value highlight">{{ $student->national_student_number ?? '—' }}</span>
                </div>
                @if($student->diploma_number)
                <div class="info-row">
                    <span class="info-label">Nomor Ijazah</span>
                    <span class="info-value highlight">{{ $student->diploma_number }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Tanggal Kelulusan</span>
                    <span class="info-value">
                        {{ $letter ? \Carbon\Carbon::parse($letter->graduation_date)->translatedFormat('j F Y') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        {{-- INFO PROGRAM --}}
        <div class="info-section" style="padding-top: 18px;">
            <div class="info-section-title">Program Studi</div>
            <div class="info-grid">
                @if($program1)
                <div class="info-row">
                    <span class="info-label">Program Keahlian</span>
                    <span class="info-value">{{ $program1->name ?? $program1->program1_name ?? '—' }}</span>
                </div>
                @endif
                @if($program)
                <div class="info-row">
                    <span class="info-label">Konsentrasi Keahlian</span>
                    <span class="info-value">{{ $program->name ?? $program->program_name ?? '—' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        {{-- FOOTER --}}
        <div class="card-footer">
            <p class="footer-note">
                Dokumen ini diverifikasi secara digital oleh sistem<br>
                <strong>SMKN 1 Talaga</strong>. Scan QR Code pada dokumen untuk memastikan keasliannya.
            </p>
            <div class="footer-brand">
                <span>SMKN 1 Talaga</span>
                <span class="dot"></span>
                <span>Sistem Kelulusan Digital</span>
                <span class="dot"></span>
                <span>{{ now()->year }}</span>
            </div>
        </div>

    </div>
@endif

</body>
</html>
