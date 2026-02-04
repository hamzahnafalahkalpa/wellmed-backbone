<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EMR - {{ $visit_registration['visit_registration_code'] ?? 'N/A' }}</title>
    <style type="text/css">
        @page {
            size: A4;
            margin: 20mm 22mm;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ============================================
           CONTAINER
           ============================================ */
        .rpt-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 0;
        }

        /* ============================================
           HEADER - Professional Medical Document
           ============================================ */
        .rpt-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 3px double #003049;
        }

        .rpt-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-header-logo {
            width: 80px;
            vertical-align: middle;
        }

        .rpt-header-logo img {
            max-width: 70px;
            max-height: 70px;
        }

        .rpt-header-content {
            vertical-align: middle;
        }

        .rpt-header h1 {
            font-size: 18px;
            color: #003049;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .rpt-subtitle {
            font-size: 12px;
            color: #555;
            margin-bottom: 4px;
        }

        .rpt-meta {
            margin-top: 8px;
            font-size: 10px;
            color: #777;
        }

        .rpt-meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-meta-table td {
            text-align: center;
            padding: 0 10px;
        }

        /* ============================================
           INFO BOX - Patient Information
           ============================================ */
        .rpt-info-box {
            background: #f0f7ff;
            border: 1px solid #c7dff0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }

        .rpt-info-box h3 {
            font-size: 11px;
            color: #003049;
            margin-bottom: 8px;
            padding-left: 10px;
            border-left: 3px solid #003049;
        }

        .rpt-info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 2px 0;
        }

        .rpt-info-row {
            font-size: 10.5px;
            margin-bottom: 2px;
        }

        .rpt-info-lbl {
            display: inline-block;
            font-weight: 600;
            color: #556070;
            min-width: 95px;
        }

        .rpt-info-val {
            color: #333;
        }

        /* ============================================
           ALLERGY ALERT
           ============================================ */
        .rpt-allergy-box {
            background: #fef2f2;
            border: 2px solid #f87171;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 16px;
        }

        .rpt-allergy-header {
            color: #991b1b;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
        }

        .rpt-allergy-row {
            padding: 4px 0;
            border-bottom: 1px solid #fecaca;
        }

        .rpt-allergy-row:last-child {
            border-bottom: none;
        }

        .rpt-allergy-name {
            font-weight: 600;
            font-size: 11px;
            color: #991b1b;
        }

        .rpt-allergy-sub {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        /* ============================================
           SECTION STYLING
           ============================================ */
        .rpt-section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .rpt-sec-title {
            background: #003049;
            color: #fff;
            padding: 5px 10px;
            font-size: 10.5px;
            font-weight: 600;
            border-radius: 4px 4px 0 0;
        }

        .rpt-sec-body {
            border: 1px solid #d0d5dd;
            border-top: none;
            padding: 10px;
            border-radius: 0 0 4px 4px;
        }

        /* ============================================
           SOAP NOTES - 2x2 Grid Style
           ============================================ */
        .rpt-soap-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-soap-grid td {
            width: 50%;
            padding: 4px;
            vertical-align: top;
        }

        .rpt-soap-card {
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 4px;
            padding: 8px;
            height: 100%;
        }

        .rpt-soap-lbl {
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .rpt-soap-S .rpt-soap-lbl { color: #2563eb; }
        .rpt-soap-O .rpt-soap-lbl { color: #7c3aed; }
        .rpt-soap-A .rpt-soap-lbl { color: #db2777; }
        .rpt-soap-P .rpt-soap-lbl { color: #16a34a; }

        .rpt-soap-txt {
            font-size: 10px;
            color: #444;
            font-style: italic;
            min-height: 25px;
        }

        /* Pain Scale Bar */
        .rpt-pain-bar {
            margin-top: 8px;
            padding: 6px 10px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .rpt-pain-bar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pain-lbl {
            font-weight: 600;
            color: #555;
            font-size: 10.5px;
        }

        .pain-val {
            font-size: 18px;
            font-weight: 700;
            color: #db2777;
        }

        .pain-desc {
            font-size: 10.5px;
            color: #666;
        }

        /* ============================================
           VITAL SIGNS - Grid Style
           ============================================ */
        .rpt-vitals-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-vitals-grid td {
            width: 33.33%;
            padding: 4px;
            vertical-align: top;
        }

        .rpt-vital {
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }

        .rpt-vital-lbl {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }

        .rpt-vital-val {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
        }

        .rpt-vital-unit {
            font-size: 9px;
            color: #94a3b8;
        }

        .rpt-vital-status {
            font-size: 9px;
            font-weight: 600;
            margin-top: 3px;
            padding: 1px 6px;
            border-radius: 8px;
            display: inline-block;
        }

        .status-normal { background: #dcfce7; color: #166534; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-danger { background: #fee2e2; color: #991b1b; }

        /* ============================================
           ANTHROPOMETRY - Compact Grid
           ============================================ */
        .rpt-anthro-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-anthro-grid td {
            width: 33.33%;
            padding: 3px;
            vertical-align: top;
        }

        .rpt-anthro-item {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 3px;
            padding: 4px 8px;
            font-size: 10.5px;
        }

        .rpt-anthro-item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-anthro-lbl {
            color: #666;
        }

        .rpt-anthro-val {
            font-weight: 600;
            color: #333;
            text-align: right;
        }

        /* ============================================
           SYMPTOMS / TAGS
           ============================================ */
        .rpt-tag {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9.5px;
            font-weight: 600;
            margin: 1px 2px;
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
            padding: 5px 0;
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
            min-width: 60px;
            text-align: center;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-initial { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #dcfce7; color: #166534; }
        .badge-secondary { background: #fef3c7; color: #92400e; }

        .rpt-dg-code {
            font-weight: 700;
            color: #333;
            font-size: 11px;
        }

        .rpt-dg-name {
            font-size: 10px;
            color: #666;
            margin-top: 1px;
        }

        /* ============================================
           PRESCRIPTION CARDS
           ============================================ */
        .rpt-rx-card {
            background: #faf5ff;
            border: 1px solid #ddd6fe;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .rpt-rx-header {
            margin-bottom: 6px;
        }

        .rpt-rx-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-rx-badge {
            font-size: 11px;
            font-weight: 700;
            background: #7c3aed;
            color: #fff;
            padding: 1px 6px;
            border-radius: 3px;
        }

        .rpt-rx-name {
            font-size: 13px;
            font-weight: 700;
            color: #5b21b6;
            padding-left: 8px;
        }

        .rpt-rx-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .rpt-rx-details td {
            width: 50%;
            padding: 2px 0;
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
            margin-top: 6px;
            padding: 4px 8px;
            background: #f3e8ff;
            border-radius: 3px;
            font-size: 10.5px;
            color: #5b21b6;
        }

        /* ============================================
           TREATMENTS
           ============================================ */
        .rpt-treatment-row {
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
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
            font-size: 10px;
            color: #999;
        }

        .rpt-treatment-result {
            color: #16a34a;
            font-weight: 600;
            font-size: 10.5px;
            text-align: right;
        }

        /* ============================================
           MEDICAL HISTORY
           ============================================ */
        .rpt-hist-label {
            font-weight: 600;
            font-size: 10.5px;
            color: #555;
            margin-bottom: 4px;
        }

        .rpt-hist-row {
            padding: 3px 0;
            font-size: 10.5px;
        }

        .rpt-hist-sub {
            font-size: 10px;
            color: #777;
        }

        /* ============================================
           TWO COLUMN LAYOUT
           ============================================ */
        .rpt-two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .rpt-two-col > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .rpt-two-col > tbody > tr > td:first-child {
            padding-right: 8px;
        }

        .rpt-two-col > tbody > tr > td:last-child {
            padding-left: 8px;
        }

        /* ============================================
           SIGNATURE / FOOTER
           ============================================ */
        .rpt-footer {
            margin-top: 30px;
            padding-top: 16px;
            border-top: 1px dashed #ccc;
        }

        .rpt-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-sig-block {
            text-align: center;
            width: 180px;
        }

        .rpt-sig-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 4px;
        }

        .rpt-sig-name {
            font-size: 10.5px;
            font-weight: 600;
            color: #333;
        }

        .rpt-sig-title {
            font-size: 9.5px;
            color: #777;
        }

        .rpt-footer-note {
            font-size: 9px;
            color: #aaa;
            text-align: center;
            line-height: 1.6;
        }

        /* ============================================
           VISIT INFO CARD
           ============================================ */
        .rpt-visit-card {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .rpt-visit-card-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rpt-visit-card-table td {
            padding: 2px 0;
            font-size: 10.5px;
        }

        .rpt-visit-lbl {
            width: 100px;
            color: #556070;
            font-weight: 600;
        }

        .rpt-visit-val {
            color: #333;
        }

        .rpt-visit-val-bold {
            font-weight: bold;
            color: #111827;
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
        .mb-8 { margin-bottom: 8px; }
        .mt-8 { margin-top: 8px; }
    </style>
</head>
<body>
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
                                <td>No. Rekam Medis: <strong>{{ $visit_registration['visit_registration_code'] ?? '-' }}</strong></td>
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
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Nama</span>
                        <span class="rpt-info-val">: <strong>{{ $patient['name'] ?? '-' }}</strong></span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Tanggal Lahir</span>
                        <span class="rpt-info-val">: {{ $patient['date_of_birth'] ?? '-' }} @if(isset($patient['age']))({{ $patient['age'] }} Thn)@endif</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Jenis Kelamin</span>
                        <span class="rpt-info-val">: {{ $patient['gender'] ?? '-' }}</span>
                    </div>
                </td>
                <td>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">No. RM</span>
                        <span class="rpt-info-val">: <strong>{{ $patient['medical_record_number'] ?? '-' }}</strong></span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">NIK</span>
                        <span class="rpt-info-val">: {{ $patient['nik'] ?? '-' }}</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">No. IHS</span>
                        <span class="rpt-info-val">: {{ $patient['ihs_number'] ?? '-' }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">No. Telepon</span>
                        <span class="rpt-info-val">: {{ $patient['phone'] ?? '-' }}</span>
                    </div>
                </td>
                <td>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Tipe Pasien</span>
                        <span class="rpt-info-val">: {{ $patient['patient_type'] ?? '-' }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         PRACTITIONER INFO
         ============================================ -->
    <div class="rpt-visit-card mb-8">
        <table class="rpt-visit-card-table">
            <tr>
                <td class="rpt-visit-lbl">Dokter (DPJP)</td>
                <td class="rpt-visit-val">: <strong>{{ $practitioner['name'] ?? '-' }}</strong></td>
                <td class="rpt-visit-lbl" style="padding-left: 20px;">Profesi</td>
                <td class="rpt-visit-val">: {{ $practitioner['profession'] ?? '-' }}</td>
            </tr>
            @if(isset($practitioner['sip_number']))
            <tr>
                <td class="rpt-visit-lbl">No. SIP</td>
                <td class="rpt-visit-val" colspan="3">: {{ $practitioner['sip_number'] }}</td>
            </tr>
            @endif
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
         VITAL SIGNS
         ============================================ -->
    @if(isset($vital_signs) && count($vital_signs) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Tanda Vital</div>
        <div class="rpt-sec-body">
            <table class="rpt-vitals-grid">
                @foreach(array_chunk($vital_signs, 3) as $vitalRow)
                <tr>
                    @foreach($vitalRow as $vital)
                    <td>
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">{{ $vital['label'] ?? '-' }}</div>
                            <div class="rpt-vital-val">{{ $vital['value'] ?? '-' }}</div>
                            <div class="rpt-vital-unit">{{ $vital['unit'] ?? '' }}</div>
                            @if(isset($vital['status']))
                            <div class="rpt-vital-status status-{{ $vital['status_class'] ?? 'normal' }}">
                                {{ $vital['status'] }}
                            </div>
                            @endif
                        </div>
                    </td>
                    @endforeach
                    @for($i = count($vitalRow); $i < 3; $i++)
                    <td></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         ANTHROPOMETRY
         ============================================ -->
    @if(isset($anthropometry) && count($anthropometry) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Antropometri</div>
        <div class="rpt-sec-body">
            <table class="rpt-anthro-grid">
                @foreach(array_chunk($anthropometry, 3) as $anthroRow)
                <tr>
                    @foreach($anthroRow as $anthro)
                    <td>
                        <div class="rpt-anthro-item">
                            <table class="rpt-anthro-item-table">
                                <tr>
                                    <td class="rpt-anthro-lbl">{{ $anthro['label'] ?? '-' }}</td>
                                    <td class="rpt-anthro-val">{{ $anthro['value'] ?? '-' }} {{ $anthro['unit'] ?? '' }}</td>
                                </tr>
                            </table>
                            @if(isset($anthro['interpretation']))
                            <div style="font-size: 9px; color: #666; text-align: right;">({{ $anthro['interpretation'] }})</div>
                            @endif
                        </div>
                    </td>
                    @endforeach
                    @for($i = count($anthroRow); $i < 3; $i++)
                    <td></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         DIAGNOSIS
         ============================================ -->
    @if(isset($diagnoses) && count($diagnoses) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Diagnosa</div>
        <div class="rpt-sec-body">
            @foreach($diagnoses as $diagnosis)
            <div class="rpt-dg-row">
                <table class="rpt-dg-table">
                    <tr>
                        <td style="width: 70px; vertical-align: top;">
                            <span class="rpt-dg-badge badge-{{ strtolower($diagnosis['type'] ?? 'secondary') }}">{{ $diagnosis['type_label'] ?? 'Diagnosis' }}</span>
                        </td>
                        <td>
                            <div class="rpt-dg-code">{{ $diagnosis['code'] ?? '-' }} - {{ $diagnosis['name'] ?? '-' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         PRESCRIPTIONS
         ============================================ -->
    @if(isset($prescriptions) && count($prescriptions) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Resep</div>
        <div class="rpt-sec-body">
            @foreach($prescriptions as $index => $prescription)
            <div class="rpt-rx-card">
                <div class="rpt-rx-header">
                    <table class="rpt-rx-header-table">
                        <tr>
                            <td style="width: 30px;"><span class="rpt-rx-badge">Rx</span></td>
                            <td><span class="rpt-rx-name">{{ $prescription['name'] ?? '-' }}</span></td>
                        </tr>
                    </table>
                </div>
                <table class="rpt-rx-details">
                    <tr>
                        <td><span class="d-lbl">Jumlah:</span> <span class="d-val">{{ $prescription['qty'] ?? '-' }} unit</span></td>
                        <td><span class="d-lbl">Frekuensi:</span> <span class="d-val">{{ $prescription['frequency'] ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="d-lbl">Waktu:</span> <span class="d-val">{{ $prescription['timing'] ?? '-' }}</span></td>
                        <td></td>
                    </tr>
                </table>
                @if(isset($prescription['indication']) && $prescription['indication'] != '-')
                <div class="rpt-rx-indication">
                    Indikasi: {{ $prescription['indication'] }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         CLINICAL TREATMENTS
         ============================================ -->
    @if(isset($treatments) && count($treatments) > 0)
    <div class="rpt-section">
        <div class="rpt-sec-title">Tindakan Klinis</div>
        <div class="rpt-sec-body">
            @foreach($treatments as $treatment)
            <div class="rpt-treatment-row">
                <table class="rpt-treatment-table">
                    <tr>
                        <td>
                            <span class="rpt-treatment-name">{{ $treatment['name'] ?? '-' }}</span>
                            <span class="rpt-treatment-qty">x{{ $treatment['qty'] ?? '1' }}</span>
                        </td>
                        <td class="rpt-treatment-result">
                            @if(isset($treatment['result']) && $treatment['result'] != '-')
                            {{ $treatment['result'] }}
                            @endif
                        </td>
                    </tr>
                </table>
                @if(isset($treatment['note']) && $treatment['note'] != '-')
                <div style="font-size: 10px; color: #666; margin-top: 2px;">Catatan: {{ $treatment['note'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         MEDICAL HISTORY (Two Columns)
         ============================================ -->
    @if((isset($history_illnesses) && count($history_illnesses) > 0) || (isset($family_illnesses) && count($family_illnesses) > 0))
    <div class="rpt-section">
        <div class="rpt-sec-title">Riwayat Penyakit</div>
        <div class="rpt-sec-body">
            <table class="rpt-two-col">
                <tr>
                    @if(isset($history_illnesses) && count($history_illnesses) > 0)
                    <td>
                        <div class="rpt-hist-label">Pribadi:</div>
                        @foreach($history_illnesses as $illness)
                        <div class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-blue">{{ $illness['code'] ?? '-' }}</span>
                            <span>{{ $illness['name'] ?? '-' }}</span>
                        </div>
                        @endforeach
                    </td>
                    @endif

                    @if(isset($family_illnesses) && count($family_illnesses) > 0)
                    <td>
                        <div class="rpt-hist-label">Keluarga:</div>
                        @foreach($family_illnesses as $illness)
                        <div class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-purple">{{ $illness['code'] ?? '-' }}</span>
                            <span>{{ $illness['name'] ?? '-' }}</span>
                            <span class="rpt-hist-sub">({{ $illness['family_name'] ?? '-' }})</span>
                        </div>
                        @endforeach
                    </td>
                    @endif
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         SIGNATURE / FOOTER
         ============================================ -->
    <div class="rpt-footer">
        <table class="rpt-footer-table">
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="rpt-footer-note">
                        Laporan dicetak secara elektronik<br>
                        Dokumen resmi dari sistem WELLMED
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="rpt-sig-block" style="display: inline-block;">
                        <div style="font-size: 10px; color: #555; margin-bottom: 4px;">
                            {{ $workspace['city'] ?? '' }}, {{ $visit_registration['visit_date'] ?? now()->format('d F Y') }}
                        </div>
                        <div class="rpt-sig-line"></div>
                        <div class="rpt-sig-name">{{ $practitioner['name'] ?? '-' }}</div>
                        <div class="rpt-sig-title">{{ $practitioner['profession'] ?? 'Dokter' }}</div>
                        @if(isset($practitioner['sip_number']))
                        <div class="rpt-sig-title">SIP: {{ $practitioner['sip_number'] }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
