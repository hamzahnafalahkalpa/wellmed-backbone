<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EMR - {{ $visit_registration['visit_registration_code'] ?? 'N/A' }}</title>
    <style type="text/css">
        @page {
            size: A4;
            margin: 22mm 12mm 18mm 12mm;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }

        /* ============================================
           CONTAINER
           ============================================ */
        .rpt-wrap {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* ============================================
           FIXED HEADER/FOOTER FOR PDF
           ============================================ */
        .pdf-header {
            position: fixed;
            top: -18mm;
            left: 0;
            right: 0;
            height: 14mm;
            font-size: 7px;
            color: #666;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2mm;
        }

        .pdf-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pdf-header-table td {
            vertical-align: bottom;
            padding: 0;
        }

        .pdf-footer {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            height: 10mm;
            font-size: 7px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
        }

        .pdf-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pdf-footer-table td {
            padding: 0;
        }

        /* ============================================
           HEADER - Document Header
           ============================================ */
        .rpt-header {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 5px;
            border-bottom: 2px double #003049;
        }

        .rpt-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-header-logo {
            width: 50px;
            vertical-align: middle;
        }

        .rpt-header-logo img {
            max-width: 45px;
            max-height: 45px;
        }

        .rpt-header-content {
            vertical-align: middle;
        }

        .rpt-header h1 {
            font-size: 12px;
            color: #003049;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0;
        }

        .rpt-subtitle {
            font-size: 9px;
            color: #555;
        }

        .rpt-meta {
            margin-top: 3px;
            font-size: 8px;
            color: #777;
        }

        .rpt-meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-meta-table td {
            text-align: center;
            padding: 0 6px;
        }

        /* ============================================
           INFO BOX - Patient Information
           ============================================ */
        .rpt-info-box {
            background: #f0f7ff;
            border: 1px solid #c7dff0;
            border-radius: 3px;
            padding: 4px 8px;
            margin-bottom: 5px;
        }

        .rpt-info-box h3 {
            font-size: 8px;
            color: #003049;
            margin-bottom: 3px;
            padding-left: 6px;
            border-left: 2px solid #003049;
        }

        .rpt-info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .rpt-info-row {
            font-size: 8px;
            line-height: 1.4;
        }

        .rpt-info-lbl {
            display: inline-block;
            font-weight: 600;
            color: #556070;
            min-width: 70px;
        }

        .rpt-info-val {
            color: #333;
        }

        /* ============================================
           ALLERGY ALERT
           ============================================ */
        .rpt-allergy-box {
            background: #fef2f2;
            border: 1px solid #f87171;
            border-radius: 3px;
            padding: 4px 8px;
            margin-bottom: 5px;
        }

        .rpt-allergy-header {
            color: #991b1b;
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 2px;
        }

        .rpt-allergy-row {
            padding: 1px 0;
        }

        .rpt-allergy-name {
            font-weight: 600;
            font-size: 8px;
            color: #991b1b;
        }

        .rpt-allergy-sub {
            font-size: 7px;
            color: #666;
        }

        /* ============================================
           SECTION STYLING
           ============================================ */
        .rpt-section {
            margin-bottom: 5px;
            page-break-inside: avoid;
        }

        .rpt-sec-title {
            background: #003049;
            color: #fff;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 600;
            border-radius: 2px 2px 0 0;
        }

        .rpt-sec-body {
            border: 1px solid #d0d5dd;
            border-top: none;
            padding: 4px;
            border-radius: 0 0 2px 2px;
        }

        /* ============================================
           SOAP NOTES - Compact 2x2 Grid
           ============================================ */
        .rpt-soap-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-soap-grid td {
            width: 50%;
            padding: 1px;
            vertical-align: top;
        }

        .rpt-soap-card {
            background: #fafafa;
            border: 1px solid #e4e7ec;
            border-radius: 2px;
            padding: 3px 5px;
        }

        .rpt-soap-lbl {
            font-weight: 700;
            font-size: 8px;
            margin-bottom: 1px;
        }

        .rpt-soap-S .rpt-soap-lbl { color: #2563eb; }
        .rpt-soap-O .rpt-soap-lbl { color: #7c3aed; }
        .rpt-soap-A .rpt-soap-lbl { color: #db2777; }
        .rpt-soap-P .rpt-soap-lbl { color: #16a34a; }

        .rpt-soap-txt {
            font-size: 7px;
            color: #444;
            font-style: italic;
        }

        /* Pain Scale Bar */
        .rpt-pain-bar {
            margin-top: 3px;
            padding: 3px 6px;
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 2px;
        }

        .rpt-pain-bar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pain-lbl {
            font-weight: 600;
            color: #555;
            font-size: 8px;
        }

        .pain-val {
            font-size: 12px;
            font-weight: 700;
            color: #db2777;
        }

        /* ============================================
           VITAL SIGNS - Compact Grid
           ============================================ */
        .rpt-vitals-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-vitals-grid td {
            width: 16.66%;
            padding: 1px;
            vertical-align: top;
        }

        .rpt-vital {
            background: #fafafa;
            border: 1px solid #e4e7ec;
            border-radius: 2px;
            padding: 3px 2px;
            text-align: center;
        }

        .rpt-vital-lbl {
            font-size: 6px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.1px;
        }

        .rpt-vital-val {
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
        }

        .rpt-vital-unit {
            font-size: 6px;
            color: #94a3b8;
        }

        .rpt-vital-status {
            font-size: 6px;
            font-weight: 600;
            margin-top: 1px;
            padding: 0 3px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-normal { background: #dcfce7; color: #166534; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-danger { background: #fee2e2; color: #991b1b; }

        /* ============================================
           ANTHROPOMETRY - Inline Compact
           ============================================ */
        .rpt-anthro-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-anthro-grid td {
            width: 25%;
            padding: 1px;
            vertical-align: top;
        }

        .rpt-anthro-item {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 2px;
            padding: 2px 4px;
            font-size: 8px;
        }

        .rpt-anthro-item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-anthro-lbl {
            color: #666;
            font-size: 7px;
        }

        .rpt-anthro-val {
            font-weight: 600;
            color: #333;
            text-align: right;
            font-size: 8px;
        }

        /* ============================================
           SYMPTOMS / TAGS
           ============================================ */
        .rpt-tag {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 6px;
            font-size: 7px;
            font-weight: 600;
            margin: 0 1px;
        }

        .rpt-tag-blue { background: #dbeafe; color: #1e40af; }
        .rpt-tag-red { background: #fee2e2; color: #991b1b; }
        .rpt-tag-yellow { background: #fef3c7; color: #92400e; }
        .rpt-tag-green { background: #dcfce7; color: #166534; }
        .rpt-tag-purple { background: #ede9fe; color: #6d28d9; }

        /* ============================================
           DIAGNOSES
           ============================================ */
        .rpt-dg-row {
            padding: 2px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .rpt-dg-row:last-child {
            border-bottom: none;
        }

        .rpt-dg-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-dg-badge {
            display: inline-block;
            min-width: 40px;
            text-align: center;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: 700;
        }

        .badge-initial { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #dcfce7; color: #166534; }
        .badge-secondary { background: #fef3c7; color: #92400e; }

        .rpt-dg-code {
            font-weight: 700;
            color: #333;
            font-size: 8px;
        }

        /* ============================================
           PRESCRIPTION - Compact Cards
           ============================================ */
        .rpt-rx-card {
            background: #faf5ff;
            border: 1px solid #ddd6fe;
            border-radius: 3px;
            padding: 4px 6px;
            margin-bottom: 3px;
            page-break-inside: avoid;
        }

        .rpt-rx-header {
            margin-bottom: 2px;
        }

        .rpt-rx-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-rx-badge {
            font-size: 8px;
            font-weight: 700;
            background: #7c3aed;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
        }

        .rpt-rx-name {
            font-size: 9px;
            font-weight: 700;
            color: #5b21b6;
            padding-left: 4px;
        }

        .rpt-rx-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .rpt-rx-details td {
            width: 50%;
            padding: 0;
            vertical-align: top;
        }

        .rpt-rx-details .d-lbl {
            color: #7c3aed;
            font-weight: 600;
        }

        .rpt-rx-details .d-val {
            color: #444;
        }

        .rpt-rx-indication {
            margin-top: 2px;
            padding: 2px 4px;
            background: #f3e8ff;
            border-radius: 2px;
            font-size: 7px;
            color: #5b21b6;
        }

        /* ============================================
           TREATMENTS
           ============================================ */
        .rpt-treatment-row {
            padding: 2px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 8px;
        }

        .rpt-treatment-row:last-child {
            border-bottom: none;
        }

        .rpt-treatment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-treatment-name {
            font-weight: 600;
            color: #333;
        }

        .rpt-treatment-qty {
            font-size: 7px;
            color: #999;
        }

        .rpt-treatment-result {
            color: #16a34a;
            font-weight: 600;
            font-size: 8px;
            text-align: right;
        }

        /* ============================================
           MEDICAL HISTORY
           ============================================ */
        .rpt-hist-label {
            font-weight: 600;
            font-size: 8px;
            color: #555;
            margin-bottom: 1px;
        }

        .rpt-hist-row {
            padding: 1px 0;
            font-size: 8px;
        }

        .rpt-hist-sub {
            font-size: 7px;
            color: #777;
        }

        /* ============================================
           TWO COLUMN LAYOUT
           ============================================ */
        .rpt-two-col {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-two-col > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .rpt-two-col > tbody > tr > td:first-child {
            padding-right: 4px;
        }

        .rpt-two-col > tbody > tr > td:last-child {
            padding-left: 4px;
        }

        /* ============================================
           SIGNATURE / FOOTER
           ============================================ */
        .rpt-sign-section {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
        }

        .rpt-sign-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-sig-block {
            text-align: center;
            width: 140px;
        }

        .rpt-sig-line {
            border-bottom: 1px solid #333;
            height: 35px;
            margin-bottom: 2px;
        }

        .rpt-sig-name {
            font-size: 8px;
            font-weight: 600;
            color: #333;
        }

        .rpt-sig-title {
            font-size: 7px;
            color: #777;
        }

        .rpt-footer-note {
            font-size: 7px;
            color: #aaa;
            text-align: center;
            line-height: 1.3;
        }

        /* ============================================
           VISIT INFO CARD
           ============================================ */
        .rpt-visit-card {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 3px;
            padding: 3px 6px;
        }

        .rpt-visit-card-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-visit-card-table td {
            padding: 0;
            font-size: 8px;
        }

        .rpt-visit-lbl {
            width: 75px;
            color: #556070;
            font-weight: 600;
        }

        .rpt-visit-val {
            color: #333;
        }

        /* ============================================
           PAGE BREAK
           ============================================ */
        .page-break {
            page-break-after: always;
        }

        /* ============================================
           UTILITIES
           ============================================ */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .mb-4 { margin-bottom: 4px; }
        .mb-5 { margin-bottom: 5px; }
        .mt-4 { margin-top: 4px; }
    </style>
</head>
<body>
    <!-- ============================================
         FIXED PDF HEADER
         ============================================ -->
    <div class="pdf-header">
        <table class="pdf-header-table">
            <tr>
                <td class="text-left"><strong>{{ $workspace['name'] ?? 'KLINIK' }}</strong></td>
                <td class="text-center">REKAM MEDIS ELEKTRONIK</td>
                <td class="text-right">No: {{ $visit_registration['visit_registration_code'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         FIXED PDF FOOTER
         ============================================ -->
    <div class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="text-left">{{ $visit_registration['visit_registration_code'] ?? '' }}</td>
                <td class="text-center">Dokumen elektronik - sah tanpa tanda tangan basah</td>
                <td class="text-right">Dicetak: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

<div class="rpt-wrap">

    <!-- ============================================
         HEADER
         ============================================ -->
    <div class="rpt-header">
        <table class="rpt-header-table">
            <tr>
                @if(isset($workspace['logo']) && $workspace['logo'])
                <td class="rpt-header-logo">
                    <img src="{{ $workspace['logo'] }}" alt="Logo">
                </td>
                @endif
                <td class="rpt-header-content">
                    <h1>{{ $workspace['name'] ?? 'KLINIK KESEHATAN' }}</h1>
                    <div class="rpt-subtitle">Hasil Pemeriksaan Medis</div>
                    <div class="rpt-meta">
                        <table class="rpt-meta-table">
                            <tr>
                                <td>No. RM: <strong>{{ $visit_registration['visit_registration_code'] ?? '-' }}</strong></td>
                                <td>Tanggal: <strong>{{ $visit_registration['visit_date'] ?? now()->format('d F Y') }}</strong></td>
                                <td>Layanan: <strong>{{ $visit_registration['medic_service']['name'] ?? '-' }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         PATIENT INFO BOX
         ============================================ -->
    <div class="rpt-info-box">
        <h3>Informasi Pasien</h3>
        <table class="rpt-info-grid">
            <tr>
                <td>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">Nama</span><span class="rpt-info-val">: <strong>{{ $patient['name'] ?? '-' }}</strong></span></div>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">Tgl Lahir</span><span class="rpt-info-val">: {{ $patient['date_of_birth'] ?? '-' }} @if(isset($patient['age']))({{ $patient['age'] }} Thn)@endif</span></div>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">Jenis Kelamin</span><span class="rpt-info-val">: {{ $patient['gender'] ?? '-' }}</span></div>
                </td>
                <td>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">No. RM</span><span class="rpt-info-val">: <strong>{{ $patient['medical_record_number'] ?? '-' }}</strong></span></div>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">NIK</span><span class="rpt-info-val">: {{ $patient['nik'] ?? '-' }}</span></div>
                    <div class="rpt-info-row"><span class="rpt-info-lbl">No. IHS</span><span class="rpt-info-val">: {{ $patient['ihs_number'] ?? '-' }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         PRACTITIONER INFO
         ============================================ -->
    <div class="rpt-visit-card mb-5">
        <table class="rpt-visit-card-table">
            <tr>
                <td class="rpt-visit-lbl">Dokter (DPJP)</td>
                <td class="rpt-visit-val">: <strong>{{ $practitioner['name'] ?? '-' }}</strong></td>
                <td class="rpt-visit-lbl" style="padding-left: 15px;">Profesi</td>
                <td class="rpt-visit-val">: {{ $practitioner['profession'] ?? '-' }}</td>
                @if(isset($practitioner['sip_number']))
                <td class="rpt-visit-lbl" style="padding-left: 15px;">SIP</td>
                <td class="rpt-visit-val">: {{ $practitioner['sip_number'] }}</td>
                @endif
            </tr>
        </table>
    </div>

    <!-- ============================================
         ALLERGY ALERT (if any)
         ============================================ -->
    @if(isset($allergies) && count($allergies) > 0)
    <div class="rpt-allergy-box">
        <div class="rpt-allergy-header">PERINGATAN ALERGI</div>
        @foreach($allergies as $allergy)
        <div class="rpt-allergy-row">
            <span class="rpt-tag rpt-tag-red">{{ $allergy['allergy_type'] ?? 'Alergi' }}</span>
            <span class="rpt-allergy-name">{{ $allergy['name'] ?? '-' }}</span>
            @if(isset($allergy['severity']))
            <span class="rpt-tag rpt-tag-yellow">{{ $allergy['severity'] }}</span>
            @endif
            @if(isset($allergy['allergen']))
            <div class="rpt-allergy-sub">Allergen: {{ $allergy['allergen'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- ============================================
         SOAP NOTES + Pain Scale
         ============================================ -->
    @if(isset($soap))
    <div class="rpt-section">
        <div class="rpt-sec-title">Catatan Klinis (SOAP)</div>
        <div class="rpt-sec-body">
            <table class="rpt-soap-grid">
                <tr>
                    <td>
                        <div class="rpt-soap-card rpt-soap-S">
                            <div class="rpt-soap-lbl">S - Subjektif</div>
                            <div class="rpt-soap-txt">{{ $soap['subjective'] ?: '-' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="rpt-soap-card rpt-soap-O">
                            <div class="rpt-soap-lbl">O - Objektif</div>
                            <div class="rpt-soap-txt">{{ $soap['objective'] ?: '-' }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="rpt-soap-card rpt-soap-A">
                            <div class="rpt-soap-lbl">A - Assessment</div>
                            <div class="rpt-soap-txt">{{ $soap['assessment'] ?: '-' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="rpt-soap-card rpt-soap-P">
                            <div class="rpt-soap-lbl">P - Plan</div>
                            <div class="rpt-soap-txt">{{ $soap['plan'] ?: '-' }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Pain Scale Bar -->
            @if(isset($pain_scale))
            <div class="rpt-pain-bar">
                <table class="rpt-pain-bar-table">
                    <tr>
                        <td style="width: 100px;">
                            <span class="pain-lbl">Skala Nyeri:</span>
                        </td>
                        <td style="width: 60px;">
                            <span class="pain-val">{{ $pain_scale['value'] ?? '0' }}</span>
                            <span style="color: #999; font-size: 10px;">/10</span>
                        </td>
                        <td>
                            <span class="rpt-tag {{ $pain_scale['badge_class'] == 'success' ? 'rpt-tag-green' : ($pain_scale['badge_class'] == 'warning' ? 'rpt-tag-yellow' : 'rpt-tag-red') }}">
                                {{ $pain_scale['interpretation'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ============================================
         CHIEF COMPLAINTS / SYMPTOMS
         ============================================ -->
    @if(isset($symptoms) && count($symptoms) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Keluhan / Simtom</div>
        <div class="rpt-sec-body">
            @foreach($symptoms as $symptom)
            <span class="rpt-tag rpt-tag-blue">{{ $symptom['name'] ?? '-' }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         VITAL SIGNS - Single Row 6 Columns
         ============================================ -->
    @if(isset($vital_signs) && count($vital_signs) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Tanda Vital</div>
        <div class="rpt-sec-body">
            <table class="rpt-vitals-grid">
                <tr>
                    @foreach($vital_signs as $vital)
                    <td>
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">{{ $vital['label'] ?? '-' }}</div>
                            <div class="rpt-vital-val">{{ $vital['value'] ?? '-' }}<span class="rpt-vital-unit">{{ $vital['unit'] ?? '' }}</span></div>
                            @if(isset($vital['status']))
                            <div class="rpt-vital-status status-{{ $vital['status_class'] ?? 'normal' }}">{{ $vital['status'] }}</div>
                            @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         ANTHROPOMETRY - Single Row 4 Columns
         ============================================ -->
    @if(isset($anthropometry) && count($anthropometry) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Antropometri</div>
        <div class="rpt-sec-body">
            <table class="rpt-anthro-grid">
                <tr>
                    @foreach($anthropometry as $anthro)
                    <td>
                        <div class="rpt-anthro-item">
                            <table class="rpt-anthro-item-table">
                                <tr>
                                    <td class="rpt-anthro-lbl">{{ $anthro['label'] ?? '-' }}</td>
                                    <td class="rpt-anthro-val">{{ $anthro['value'] ?? '-' }} {{ $anthro['unit'] ?? '' }}@if(isset($anthro['interpretation'])) <span style="font-size:6px;color:#888;">({{ $anthro['interpretation'] }})</span>@endif</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         DIAGNOSIS - Compact Inline
         ============================================ -->
    @if(isset($diagnoses) && count($diagnoses) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Diagnosa</div>
        <div class="rpt-sec-body">
            @foreach($diagnoses as $diagnosis)
            <div class="rpt-dg-row">
                <span class="rpt-dg-badge badge-{{ strtolower($diagnosis['type'] ?? 'secondary') }}">{{ $diagnosis['type_label'] ?? 'Dx' }}</span>
                <span class="rpt-dg-code">{{ $diagnosis['code'] ?? '-' }} - {{ $diagnosis['name'] ?? '-' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         PRESCRIPTIONS - Compact Cards
         ============================================ -->
    @if(isset($prescriptions) && count($prescriptions) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Resep</div>
        <div class="rpt-sec-body">
            @foreach($prescriptions as $index => $prescription)
            <div class="rpt-rx-card">
                <table class="rpt-rx-header-table">
                    <tr>
                        <td style="width: 22px;"><span class="rpt-rx-badge">Rx</span></td>
                        <td><span class="rpt-rx-name">{{ $prescription['name'] ?? '-' }}</span></td>
                        <td class="text-right" style="font-size:8px;">
                            <span class="d-lbl">Jml:</span> <span class="d-val">{{ $prescription['qty'] ?? '-' }}</span> |
                            <span class="d-lbl">Frek:</span> <span class="d-val">{{ $prescription['frequency'] ?? '-' }}</span>
                            @if(isset($prescription['timing']) && $prescription['timing'] != '-')
                            | <span class="d-lbl">Waktu:</span> <span class="d-val">{{ $prescription['timing'] }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if(isset($prescription['indication']) && $prescription['indication'] != '-')
                <div class="rpt-rx-indication">Indikasi: {{ $prescription['indication'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         CLINICAL TREATMENTS - Compact
         ============================================ -->
    @if(isset($treatments) && count($treatments) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Tindakan Klinis</div>
        <div class="rpt-sec-body">
            @foreach($treatments as $treatment)
            <div class="rpt-treatment-row">
                <span class="rpt-treatment-name">{{ $treatment['name'] ?? '-' }}</span>
                <span class="rpt-treatment-qty">x{{ $treatment['qty'] ?? '1' }}</span>
                @if(isset($treatment['result']) && $treatment['result'] != '-')
                <span class="rpt-treatment-result" style="float:right;">{{ $treatment['result'] }}</span>
                @endif
                @if(isset($treatment['note']) && $treatment['note'] != '-')
                <span style="font-size: 7px; color: #666;"> - {{ $treatment['note'] }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         MEDICAL HISTORY - Compact Two Columns
         ============================================ -->
    @if((isset($history_illnesses) && count($history_illnesses) > 0) || (isset($family_illnesses) && count($family_illnesses) > 0))
    <div class="rpt-section">
        <div class="rpt-sec-title">Riwayat Penyakit</div>
        <div class="rpt-sec-body">
            <table class="rpt-two-col">
                <tr>
                    @if(isset($history_illnesses) && count($history_illnesses) > 0)
                    <td>
                        <span class="rpt-hist-label">Pribadi:</span>
                        @foreach($history_illnesses as $illness)
                        <span class="rpt-tag rpt-tag-blue">{{ $illness['code'] ?? '-' }}</span>
                        @endforeach
                    </td>
                    @endif
                    @if(isset($family_illnesses) && count($family_illnesses) > 0)
                    <td>
                        <span class="rpt-hist-label">Keluarga:</span>
                        @foreach($family_illnesses as $illness)
                        <span class="rpt-tag rpt-tag-purple">{{ $illness['code'] ?? '-' }} ({{ $illness['family_name'] ?? '-' }})</span>
                        @endforeach
                    </td>
                    @endif
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         SIGNATURE SECTION
         ============================================ -->
    <div class="rpt-sign-section">
        <table class="rpt-sign-table">
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="rpt-footer-note">
                        Laporan dicetak secara elektronik<br>
                        Dokumen resmi dari sistem WELLMED
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="rpt-sig-block" style="display: inline-block;">
                        <div style="font-size: 8px; color: #555; margin-bottom: 2px;">
                            {{ $workspace['city'] ?? '' }}, {{ $visit_registration['visit_date'] ?? now()->format('d F Y') }}
                        </div>
                        <div class="rpt-sig-line"></div>
                        <div class="rpt-sig-name">{{ $practitioner['name'] ?? '-' }}</div>
                        <div class="rpt-sig-title">{{ $practitioner['profession'] ?? 'Dokter' }}@if(isset($practitioner['sip_number'])) | SIP: {{ $practitioner['sip_number'] }}@endif</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
