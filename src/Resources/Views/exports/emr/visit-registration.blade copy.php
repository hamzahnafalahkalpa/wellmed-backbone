<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EMR - {{ $visit_registration['visit_registration_code'] ?? 'N/A' }}</title>
    <style type="text/css">
        @page {
            margin: 25mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }

        /* ============================================
           HEADER - Professional Medical Document
           ============================================ */
        .document-header {
            border-bottom: 2px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-top {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo {
            width: 80px;
            vertical-align: top;
            padding-right: 15px;
        }

        .header-logo img {
            max-width: 70px;
            max-height: 70px;
        }

        .header-clinic {
            vertical-align: top;
        }

        .clinic-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .clinic-tagline {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 6px;
        }

        .clinic-contact {
            font-size: 8pt;
            color: #4b5563;
            line-height: 1.4;
        }

        .header-doc-info {
            width: 180px;
            vertical-align: top;
            text-align: right;
        }

        .doc-type {
            background: #1e40af;
            color: #fff;
            font-size: 8pt;
            font-weight: bold;
            padding: 4px 12px;
            display: inline-block;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .doc-number {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 2px;
        }

        .doc-date {
            font-size: 8pt;
            color: #6b7280;
        }

        /* ============================================
           PATIENT IDENTIFIER BAR
           ============================================ */
        .patient-bar {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .patient-bar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-bar-table td {
            vertical-align: middle;
        }

        .patient-name-large {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        .patient-meta {
            font-size: 8pt;
            color: #3b82f6;
            margin-top: 2px;
        }

        .patient-ids {
            text-align: right;
        }

        .patient-id-item {
            display: inline-block;
            background: #fff;
            border: 1px solid #93c5fd;
            border-radius: 4px;
            padding: 4px 10px;
            margin-left: 8px;
            font-size: 8pt;
        }

        .patient-id-label {
            color: #6b7280;
            font-size: 7pt;
            display: block;
        }

        .patient-id-value {
            color: #1e40af;
            font-weight: bold;
            font-size: 9pt;
        }

        /* ============================================
           ALLERGY ALERT
           ============================================ */
        .allergy-alert {
            background: #fef2f2;
            border: 2px solid #f87171;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .allergy-header {
            color: #dc2626;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 6px;
        }

        .allergy-items {
            display: block;
        }

        .allergy-tag {
            display: inline-block;
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8pt;
            margin: 2px 4px 2px 0;
        }

        /* ============================================
           SECTION STYLING
           ============================================ */
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #f8fafc;
            border-left: 4px solid #1e40af;
            padding: 6px 12px;
            margin-bottom: 8px;
            font-size: 10pt;
            font-weight: bold;
            color: #1e40af;
        }

        .section-content {
            padding: 0 5px;
        }

        /* ============================================
           TWO COLUMN LAYOUT
           ============================================ */
        .two-column {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .two-column > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .two-column > tbody > tr > td:first-child {
            padding-right: 8px;
        }

        .two-column > tbody > tr > td:last-child {
            padding-left: 8px;
        }

        /* ============================================
           INFO LIST (Label: Value pairs)
           ============================================ */
        .info-list {
            width: 100%;
            border-collapse: collapse;
        }

        .info-list tr td {
            padding: 4px 0;
            font-size: 9pt;
            vertical-align: top;
        }

        .info-label {
            width: 110px;
            color: #6b7280;
            font-weight: 500;
        }

        .info-colon {
            width: 8px;
            color: #6b7280;
        }

        .info-value {
            color: #1f2937;
        }

        .info-value-bold {
            font-weight: bold;
            color: #111827;
        }

        /* ============================================
           VISIT INFO CARD
           ============================================ */
        .visit-card {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .visit-card .info-list tr td {
            padding: 3px 0;
        }

        /* ============================================
           VITAL SIGNS - Clinical Grid
           ============================================ */
        .vitals-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .vitals-grid td {
            width: 16.66%;
            text-align: center;
            padding: 8px 4px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .vital-icon {
            font-size: 8pt;
            color: #9ca3af;
            margin-bottom: 2px;
        }

        .vital-name {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .vital-reading {
            font-size: 13pt;
            font-weight: bold;
            color: #1e40af;
        }

        .vital-unit {
            font-size: 8pt;
            color: #9ca3af;
            font-weight: normal;
        }

        .vital-status {
            font-size: 7pt;
            padding: 2px 6px;
            border-radius: 8px;
            margin-top: 4px;
            display: inline-block;
        }

        .vital-normal { background: #d1fae5; color: #065f46; }
        .vital-warning { background: #fef3c7; color: #92400e; }
        .vital-danger { background: #fee2e2; color: #991b1b; }

        /* ============================================
           ANTHROPOMETRY - Compact Display
           ============================================ */
        .anthro-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .anthro-grid td {
            width: 25%;
            text-align: center;
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .anthro-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .anthro-value {
            font-size: 12pt;
            font-weight: bold;
            color: #059669;
        }

        .anthro-unit {
            font-size: 8pt;
            color: #9ca3af;
        }

        .anthro-status {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ============================================
           SYMPTOMS TAGS
           ============================================ */
        .symptom-list {
            padding: 8px 0;
        }

        .symptom-tag {
            display: inline-block;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 8pt;
            margin: 2px 4px 2px 0;
        }

        /* ============================================
           PAIN SCALE
           ============================================ */
        .pain-display {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 15px;
            margin: 8px 0;
        }

        .pain-display-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pain-score {
            width: 80px;
            text-align: center;
        }

        .pain-number {
            font-size: 24pt;
            font-weight: bold;
            color: #1e40af;
        }

        .pain-max {
            font-size: 10pt;
            color: #9ca3af;
        }

        .pain-bar-container {
            padding: 0 20px;
            vertical-align: middle;
        }

        .pain-scale-bar {
            height: 10px;
            background: linear-gradient(to right, #22c55e 0%, #fbbf24 50%, #ef4444 100%);
            border-radius: 5px;
            position: relative;
        }

        .pain-interpretation {
            width: 140px;
            text-align: right;
            vertical-align: middle;
        }

        /* ============================================
           SOAP NOTES - Professional Layout
           ============================================ */
        .soap-container {
            margin: 8px 0;
        }

        .soap-item {
            margin-bottom: 10px;
        }

        .soap-letter {
            display: inline-block;
            width: 24px;
            height: 24px;
            background: #1e40af;
            color: #fff;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
            font-size: 10pt;
            border-radius: 4px;
            margin-right: 8px;
        }

        .soap-label {
            font-weight: bold;
            color: #374151;
            font-size: 9pt;
        }

        .soap-content {
            margin-top: 4px;
            margin-left: 32px;
            padding: 8px 12px;
            background: #f9fafb;
            border-left: 3px solid #1e40af;
            font-size: 9pt;
            color: #1f2937;
            min-height: 30px;
        }

        /* ============================================
           DIAGNOSIS - Color Coded
           ============================================ */
        .diagnosis-list {
            margin: 8px 0;
        }

        .diagnosis-item {
            padding: 8px 12px;
            margin-bottom: 6px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .diagnosis-initial {
            background: #eff6ff;
            border-left-color: #3b82f6;
        }

        .diagnosis-primary {
            background: #fff7ed;
            border-left-color: #f97316;
        }

        .diagnosis-secondary {
            background: #fdf4ff;
            border-left-color: #d946ef;
        }

        .diagnosis-type-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .diagnosis-initial .diagnosis-type-label { color: #2563eb; }
        .diagnosis-primary .diagnosis-type-label { color: #ea580c; }
        .diagnosis-secondary .diagnosis-type-label { color: #c026d3; }

        .diagnosis-code {
            font-weight: bold;
            color: #1f2937;
            font-size: 9pt;
        }

        .diagnosis-name {
            color: #4b5563;
            font-size: 9pt;
        }

        /* ============================================
           DATA TABLES - Clean Design
           ============================================ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8pt;
        }

        .data-table thead th {
            background: #1e40af;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 8pt;
            border: none;
        }

        .data-table thead th:first-child {
            border-radius: 4px 0 0 0;
        }

        .data-table thead th:last-child {
            border-radius: 0 4px 0 0;
        }

        .data-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 4px;
        }

        .data-table tbody tr:last-child td:last-child {
            border-radius: 0 0 4px 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* ============================================
           MEDICATION TABLE - Enhanced
           ============================================ */
        .med-name {
            font-weight: bold;
            color: #1f2937;
        }

        .med-dosage {
            font-size: 8pt;
            color: #6b7280;
        }

        .med-instruction {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 7pt;
            color: #166534;
            display: inline-block;
        }

        /* ============================================
           HISTORY SECTION
           ============================================ */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8pt;
        }

        .history-table th {
            background: #f3f4f6;
            padding: 6px 10px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .history-table td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }

        /* ============================================
           BADGE UTILITIES
           ============================================ */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 7pt;
            font-weight: 600;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #4b5563; }

        /* ============================================
           SIGNATURE SECTION
           ============================================ */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-cell {
            width: 50%;
            padding: 10px 20px;
            vertical-align: top;
        }

        .signature-box {
            text-align: center;
        }

        .signature-location-date {
            font-size: 9pt;
            color: #4b5563;
            margin-bottom: 60px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 6px;
            display: inline-block;
            min-width: 200px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10pt;
            color: #1f2937;
        }

        .signature-title {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 2px;
        }

        .signature-license {
            font-size: 8pt;
            color: #6b7280;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .document-footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 7pt;
            color: #9ca3af;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
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
        .mb-0 { margin-bottom: 0; }
        .mb-8 { margin-bottom: 8px; }
        .mb-12 { margin-bottom: 12px; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
    </style>
</head>
<body>

    <!-- ============================================
         DOCUMENT HEADER
         ============================================ -->
    <div class="document-header">
        <table class="header-top">
            <tr>
                @if(isset($workspace['logo']) && $workspace['logo'])
                <td class="header-logo">
                    <img src="{{ $workspace['logo'] }}" alt="Logo">
                </td>
                @endif
                <td class="header-clinic">
                    <div class="clinic-name">{{ $workspace['name'] ?? 'KLINIK KESEHATAN' }}</div>
                    <div class="clinic-tagline">Healthcare Excellence</div>
                    <div class="clinic-contact">
                        @if(isset($workspace['address'])){{ $workspace['address'] }}@endif
                        @if(isset($workspace['phone']) || isset($workspace['email']))
                        <br>{{ $workspace['phone'] ?? '' }} {{ isset($workspace['phone']) && isset($workspace['email']) ? ' | ' : '' }} {{ $workspace['email'] ?? '' }}
                        @endif
                    </div>
                </td>
                <td class="header-doc-info">
                    <div class="doc-type">REKAM MEDIS</div>
                    <div class="doc-number">{{ $visit_registration['visit_registration_code'] ?? '-' }}</div>
                    <div class="doc-date">{{ $visit_registration['visit_date'] ?? now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         PATIENT IDENTIFIER BAR
         ============================================ -->
    <div class="patient-bar">
        <table class="patient-bar-table">
            <tr>
                <td>
                    <div class="patient-name-large">{{ $patient['name'] ?? '-' }}</div>
                    <div class="patient-meta">
                        {{ $patient['gender'] ?? '-' }} |
                        {{ $patient['date_of_birth'] ?? '-' }}
                        @if(isset($patient['age'])) ({{ $patient['age'] }} tahun) @endif |
                        {{ $patient['blood_type'] ?? '-' }}
                    </div>
                </td>
                <td class="patient-ids">
                    <span class="patient-id-item">
                        <span class="patient-id-label">No. RM</span>
                        <span class="patient-id-value">{{ $patient['medical_record_number'] ?? '-' }}</span>
                    </span>
                    @if(isset($patient['nik']) && $patient['nik'])
                    <span class="patient-id-item">
                        <span class="patient-id-label">NIK</span>
                        <span class="patient-id-value">{{ $patient['nik'] }}</span>
                    </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         ALLERGY ALERT (if any)
         ============================================ -->
    @if(isset($allergies) && count($allergies) > 0)
    <div class="allergy-alert">
        <div class="allergy-header">PERINGATAN ALERGI</div>
        <div class="allergy-items">
            @foreach($allergies as $allergy)
            <span class="allergy-tag">
                {{ $allergy['name'] ?? '-' }}
                @if(isset($allergy['severity'])) ({{ $allergy['severity'] }}) @endif
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============================================
         PATIENT & VISIT INFO (Two Columns)
         ============================================ -->
    <table class="two-column">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Data Pasien</div>
                    <div class="section-content">
                        <table class="info-list">
                            <tr>
                                <td class="info-label">No. IHS</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $patient['ihs_number'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">No. Telepon</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $patient['phone'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Alamat</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $patient['address'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Tipe Pasien</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $patient['patient_type'] ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Informasi Kunjungan</div>
                    <div class="section-content">
                        <div class="visit-card">
                            <table class="info-list">
                                <tr>
                                    <td class="info-label">Layanan</td>
                                    <td class="info-colon">:</td>
                                    <td class="info-value info-value-bold">{{ $visit_registration['medic_service']['name'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Dokter (DPJP)</td>
                                    <td class="info-colon">:</td>
                                    <td class="info-value info-value-bold">{{ $practitioner['name'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Profesi</td>
                                    <td class="info-colon">:</td>
                                    <td class="info-value">{{ $practitioner['profession'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Status</td>
                                    <td class="info-colon">:</td>
                                    <td class="info-value">
                                        <span class="badge badge-{{ ($visit_registration['status'] ?? '') == 'COMPLETED' ? 'success' : 'info' }}">
                                            {{ $visit_registration['status'] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ============================================
         CHIEF COMPLAINTS / SYMPTOMS
         ============================================ -->
    @if(isset($symptoms) && count($symptoms) > 0)
    <div class="section">
        <div class="section-title">Keluhan Utama</div>
        <div class="section-content">
            <div class="symptom-list">
                @foreach($symptoms as $symptom)
                <span class="symptom-tag">{{ $symptom['name'] ?? '-' }}</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================
         VITAL SIGNS
         ============================================ -->
    @if(isset($vital_signs) && count($vital_signs) > 0)
    <div class="section">
        <div class="section-title">Tanda-Tanda Vital</div>
        <div class="section-content">
            <table class="vitals-grid">
                <tr>
                    @foreach($vital_signs as $vital)
                    <td>
                        <div class="vital-name">{{ $vital['label'] ?? '-' }}</div>
                        <div class="vital-reading">
                            {{ $vital['value'] ?? '-' }}
                            <span class="vital-unit">{{ $vital['unit'] ?? '' }}</span>
                        </div>
                        @if(isset($vital['status']))
                        <div class="vital-status vital-{{ $vital['status_class'] ?? 'normal' }}">
                            {{ $vital['status'] }}
                        </div>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         ANTHROPOMETRY
         ============================================ -->
    @if(isset($anthropometry) && count($anthropometry) > 0)
    <div class="section">
        <div class="section-title">Antropometri</div>
        <div class="section-content">
            <table class="anthro-grid">
                <tr>
                    @foreach($anthropometry as $anthro)
                    <td>
                        <div class="anthro-label">{{ $anthro['label'] ?? '-' }}</div>
                        <div class="anthro-value">
                            {{ $anthro['value'] ?? '-' }}
                            <span class="anthro-unit">{{ $anthro['unit'] ?? '' }}</span>
                        </div>
                        @if(isset($anthro['interpretation']))
                        <div class="anthro-status">{{ $anthro['interpretation'] }}</div>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         PAIN SCALE
         ============================================ -->
    @if(isset($pain_scale))
    <div class="section">
        <div class="section-title">Skala Nyeri</div>
        <div class="section-content">
            <div class="pain-display">
                <table class="pain-display-table">
                    <tr>
                        <td class="pain-score">
                            <span class="pain-number">{{ $pain_scale['value'] ?? '0' }}</span>
                            <span class="pain-max">/10</span>
                        </td>
                        <td class="pain-bar-container">
                            <div class="pain-scale-bar"></div>
                        </td>
                        <td class="pain-interpretation">
                            <span class="badge badge-{{ $pain_scale['badge_class'] ?? 'warning' }}">
                                {{ $pain_scale['interpretation'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================
         SOAP NOTES
         ============================================ -->
    @if(isset($soap))
    <div class="section">
        <div class="section-title">Catatan SOAP</div>
        <div class="section-content">
            <div class="soap-container">
                @if(isset($soap['subjective']))
                <div class="soap-item">
                    <span class="soap-letter">S</span>
                    <span class="soap-label">Subjective (Keluhan)</span>
                    <div class="soap-content">{{ $soap['subjective'] ?: '-' }}</div>
                </div>
                @endif

                @if(isset($soap['objective']))
                <div class="soap-item">
                    <span class="soap-letter">O</span>
                    <span class="soap-label">Objective (Pemeriksaan)</span>
                    <div class="soap-content">{{ $soap['objective'] ?: '-' }}</div>
                </div>
                @endif

                @if(isset($soap['assessment']))
                <div class="soap-item">
                    <span class="soap-letter">A</span>
                    <span class="soap-label">Assessment (Penilaian)</span>
                    <div class="soap-content">{{ $soap['assessment'] ?: '-' }}</div>
                </div>
                @endif

                @if(isset($soap['plan']))
                <div class="soap-item">
                    <span class="soap-letter">P</span>
                    <span class="soap-label">Plan (Rencana)</span>
                    <div class="soap-content">{{ $soap['plan'] ?: '-' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================
         DIAGNOSIS
         ============================================ -->
    @if(isset($diagnoses) && count($diagnoses) > 0)
    <div class="section">
        <div class="section-title">Diagnosis</div>
        <div class="section-content">
            <div class="diagnosis-list">
                @foreach($diagnoses as $diagnosis)
                <div class="diagnosis-item diagnosis-{{ strtolower($diagnosis['type'] ?? 'secondary') }}">
                    <div class="diagnosis-type-label">{{ $diagnosis['type_label'] ?? 'Diagnosis' }}</div>
                    <span class="diagnosis-code">{{ $diagnosis['code'] ?? '-' }}</span>
                    <span class="diagnosis-name">- {{ $diagnosis['name'] ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================
         PAGE BREAK
         ============================================ -->
    <div class="page-break"></div>

    <!-- ============================================
         PRESCRIPTIONS
         ============================================ -->
    @if(isset($prescriptions) && count($prescriptions) > 0)
    <div class="section">
        <div class="section-title">Resep Obat</div>
        <div class="section-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 28%;">Nama Obat</th>
                        <th style="width: 10%;">Jumlah</th>
                        <th style="width: 20%;">Aturan Pakai</th>
                        <th style="width: 15%;">Waktu</th>
                        <th style="width: 22%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $index => $prescription)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <span class="med-name">{{ $prescription['name'] ?? '-' }}</span>
                            @if(isset($prescription['dosage']))
                            <br><span class="med-dosage">{{ $prescription['dosage'] }}</span>
                            @endif
                        </td>
                        <td class="text-center text-bold">{{ $prescription['qty'] ?? '-' }}</td>
                        <td>
                            @if(isset($prescription['frequency']))
                            <span class="med-instruction">{{ $prescription['frequency'] }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $prescription['timing'] ?? '-' }}</td>
                        <td>{{ $prescription['indication'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         TREATMENTS / PROCEDURES
         ============================================ -->
    @if(isset($treatments) && count($treatments) > 0)
    <div class="section">
        <div class="section-title">Tindakan Medis</div>
        <div class="section-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Tindakan</th>
                        <th style="width: 10%;">Jumlah</th>
                        <th style="width: 25%;">Hasil</th>
                        <th style="width: 25%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($treatments as $index => $treatment)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-bold">{{ $treatment['name'] ?? '-' }}</td>
                        <td class="text-center">{{ $treatment['qty'] ?? '-' }}</td>
                        <td>{{ $treatment['result'] ?? '-' }}</td>
                        <td>{{ $treatment['note'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ============================================
         MEDICAL HISTORY (Two Columns)
         ============================================ -->
    @if((isset($history_illnesses) && count($history_illnesses) > 0) || (isset($family_illnesses) && count($family_illnesses) > 0))
    <table class="two-column">
        <tr>
            @if(isset($history_illnesses) && count($history_illnesses) > 0)
            <td>
                <div class="section">
                    <div class="section-title">Riwayat Penyakit</div>
                    <div class="section-content">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Kode</th>
                                    <th style="width: 70%;">Penyakit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history_illnesses as $illness)
                                <tr>
                                    <td>{{ $illness['code'] ?? '-' }}</td>
                                    <td>{{ $illness['name'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
            @endif

            @if(isset($family_illnesses) && count($family_illnesses) > 0)
            <td>
                <div class="section">
                    <div class="section-title">Riwayat Penyakit Keluarga</div>
                    <div class="section-content">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Hubungan</th>
                                    <th style="width: 65%;">Penyakit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($family_illnesses as $illness)
                                <tr>
                                    <td>{{ $illness['family_name'] ?? '-' }}</td>
                                    <td>{{ $illness['name'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
            @endif
        </tr>
    </table>
    @endif

    <!-- ============================================
         SIGNATURE SECTION
         ============================================ -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <!-- Left side empty for balance -->
                </td>
                <td class="signature-cell">
                    <div class="signature-box">
                        <div class="signature-location-date">
                            {{ $workspace['city'] ?? '' }}, {{ $visit_registration['visit_date'] ?? now()->format('d F Y') }}
                            <br>Dokter Pemeriksa
                        </div>
                        <div class="signature-line">
                            <div class="signature-name">{{ $practitioner['name'] ?? '-' }}</div>
                            <div class="signature-title">{{ $practitioner['profession'] ?? 'Dokter' }}</div>
                            @if(isset($practitioner['sip_number']))
                            <div class="signature-license">SIP: {{ $practitioner['sip_number'] }}</div>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================
         DOCUMENT FOOTER
         ============================================ -->
    <div class="document-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    {{ $visit_registration['visit_registration_code'] ?? '' }}
                </td>
                <td class="footer-center">
                    Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah
                </td>
                <td class="footer-right">
                    Dicetak: {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
