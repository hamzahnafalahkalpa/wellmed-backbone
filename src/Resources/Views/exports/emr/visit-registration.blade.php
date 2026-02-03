<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hasil Pemeriksaan Medis</title>
    <style>
        /* Reset & Base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.4;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 12mm 15mm 12mm 15mm;
        }

        /* Container */
        .rpt-wrap { width: 100%; padding: 0; }

        /* Header */
        .rpt-header { text-align: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px double #003049; }
        .rpt-header h1 { font-size: 14px; color: #003049; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .rpt-header .rpt-subtitle { font-size: 10px; color: #555; margin-bottom: 4px; }
        .rpt-header .rpt-meta { font-size: 8px; color: #666; }
        .rpt-header .rpt-meta span { margin: 0 6px; }

        /* Info box */
        .rpt-info-box { background: #f5f9ff; border: 1px solid #d0e0f0; padding: 6px 8px; margin-bottom: 8px; }
        .rpt-info-box h3 { font-size: 9px; color: #003049; margin-bottom: 4px; padding-left: 6px; border-left: 2px solid #003049; }
        .rpt-info-table { width: 100%; border-collapse: collapse; }
        .rpt-info-table td { padding: 1px 4px; font-size: 8px; vertical-align: top; }
        .rpt-info-table .lbl { font-weight: bold; color: #555; width: 70px; }
        .rpt-info-table .val { color: #333; }

        /* Section */
        .rpt-section { margin-bottom: 8px; page-break-inside: avoid; }
        .rpt-sec-title { background: #003049; color: #fff; padding: 3px 6px; font-size: 9px; font-weight: bold; }
        .rpt-sec-body { border: 1px solid #ccc; border-top: none; padding: 6px; }

        /* SOAP Grid */
        .rpt-soap-table { width: 100%; border-collapse: collapse; }
        .rpt-soap-table td { width: 50%; vertical-align: top; padding: 2px; }
        .rpt-soap-card { background: #fafafa; border: 1px solid #ddd; padding: 4px 6px; }
        .rpt-soap-lbl { font-weight: bold; font-size: 9px; margin-bottom: 2px; }
        .rpt-soap-S .rpt-soap-lbl { color: #2563eb; }
        .rpt-soap-O .rpt-soap-lbl { color: #7c3aed; }
        .rpt-soap-A .rpt-soap-lbl { color: #db2777; }
        .rpt-soap-P .rpt-soap-lbl { color: #16a34a; }
        .rpt-soap-txt { font-size: 8px; color: #444; }

        /* Pain Scale */
        .rpt-pain-bar { margin-top: 4px; padding: 3px 6px; background: #fff5f5; border: 1px solid #fcc; }
        .rpt-pain-bar .pain-lbl { font-weight: bold; color: #555; font-size: 8px; }
        .rpt-pain-bar .pain-val { font-size: 12px; font-weight: bold; color: #dc2626; margin: 0 6px; }
        .rpt-pain-bar .pain-desc { font-size: 8px; color: #666; }

        /* Vitals Grid */
        .rpt-vitals-table { width: 100%; border-collapse: collapse; }
        .rpt-vitals-table td { width: 33.33%; vertical-align: top; padding: 2px; }
        .rpt-vital { background: #fafafa; border: 1px solid #ddd; padding: 4px; text-align: center; }
        .rpt-vital-lbl { font-size: 7px; color: #666; text-transform: uppercase; }
        .rpt-vital-val { font-size: 11px; font-weight: bold; color: #1e293b; }
        .rpt-vital-unit { font-size: 7px; color: #888; }
        .rpt-vital-status { font-size: 7px; font-weight: bold; margin-top: 2px; padding: 1px 4px; display: inline-block; }
        .status-normal { background: #d1fae5; color: #065f46; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-critical { background: #fee2e2; color: #991b1b; }

        /* Anthropometry Grid */
        .rpt-anthro-table { width: 100%; border-collapse: collapse; }
        .rpt-anthro-table td { width: 33.33%; vertical-align: top; padding: 2px; }
        .rpt-anthro-item { background: #fafafa; border: 1px solid #ddd; padding: 3px 5px; font-size: 8px; }
        .rpt-anthro-item .a-lbl { color: #666; }
        .rpt-anthro-item .a-val { font-weight: bold; color: #333; float: right; }

        /* Tags */
        .rpt-tag { display: inline-block; padding: 1px 5px; font-size: 7px; font-weight: bold; margin: 1px; border: 1px solid; }
        .rpt-tag-blue { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .rpt-tag-red { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .rpt-tag-yellow { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .rpt-tag-green { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .rpt-tag-purple { background: #ede9fe; color: #5b21b6; border-color: #c4b5fd; }

        /* Allergy */
        .rpt-allergy-row { padding: 2px 0; border-bottom: 1px solid #eee; font-size: 8px; }
        .rpt-allergy-row:last-child { border-bottom: none; }
        .rpt-allergy-row .a-name { font-weight: bold; margin: 0 4px; }
        .rpt-allergy-sub { font-size: 7px; color: #666; margin-top: 1px; }

        /* History */
        .rpt-hist-label { font-weight: bold; font-size: 8px; color: #555; margin-bottom: 2px; }
        .rpt-hist-row { padding: 1px 0; font-size: 8px; }
        .rpt-hist-sub { font-size: 7px; color: #777; }

        /* Diagnose */
        .rpt-dg-row { padding: 2px 0; border-bottom: 1px solid #eee; font-size: 8px; }
        .rpt-dg-row:last-child { border-bottom: none; }
        .rpt-dg-badge { display: inline-block; min-width: 40px; text-align: center; padding: 1px 4px; font-size: 7px; font-weight: bold; margin-right: 6px; }
        .badge-initial { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #d1fae5; color: #065f46; }
        .badge-secondary { background: #fef3c7; color: #92400e; }
        .rpt-dg-code { font-weight: bold; color: #333; }

        /* Prescription */
        .rpt-rx-card { background: #faf5ff; border: 1px solid #e9d5ff; padding: 5px 6px; margin-bottom: 4px; page-break-inside: avoid; }
        .rpt-rx-header { margin-bottom: 3px; }
        .rpt-rx-badge { font-size: 8px; font-weight: bold; background: #7c3aed; color: #fff; padding: 1px 4px; margin-right: 6px; }
        .rpt-rx-name { font-size: 10px; font-weight: bold; color: #5b21b6; }
        .rpt-rx-details { font-size: 8px; }
        .rpt-rx-details table { width: 100%; }
        .rpt-rx-details td { padding: 1px 3px; vertical-align: top; }
        .rpt-rx-details .d-lbl { color: #7c3aed; font-weight: bold; width: 60px; }
        .rpt-rx-details .d-val { color: #444; }

        /* Treatment */
        .rpt-treatment-row { padding: 2px 0; border-bottom: 1px solid #eee; font-size: 8px; }
        .rpt-treatment-row:last-child { border-bottom: none; }
        .rpt-treatment-name { font-weight: bold; color: #333; }
        .rpt-treatment-qty { font-size: 7px; color: #888; margin-left: 4px; }
        .rpt-treatment-result { color: #059669; font-weight: bold; float: right; }

        /* Footer */
        .rpt-footer { margin-top: 15px; padding-top: 8px; border-top: 1px dashed #999; }
        .rpt-footer table { width: 100%; }
        .rpt-sig-block { text-align: center; width: 140px; }
        .rpt-sig-line { border-bottom: 1px solid #333; height: 30px; margin-bottom: 2px; }
        .rpt-sig-name { font-size: 8px; font-weight: bold; color: #333; }
        .rpt-sig-title { font-size: 7px; color: #666; }
        .rpt-footer-note { font-size: 7px; color: #999; text-align: right; }

        /* Clearfix */
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
<div class="rpt-wrap">

    {{-- ====== HEADER ====== --}}
    <div class="rpt-header">
        <h1>Hasil Pemeriksaan Medis</h1>
        <div class="rpt-subtitle">{{ $visit_registration['medic_service']['name'] ?? 'Pelayanan Umum' }}</div>
        <div class="rpt-meta">
            <span>Tanggal: {{ $visit_registration['visit_date'] ?? '-' }}</span>
            <span>|</span>
            <span>Dokter: {{ $practitioner['name'] ?? '-' }} ({{ $practitioner['profession'] ?? '-' }})</span>
            @if(!empty($practitioner['sip_number']))
                <span>|</span>
                <span>SIP: {{ $practitioner['sip_number'] }}</span>
            @endif
        </div>
    </div>

    {{-- ====== PATIENT INFO ====== --}}
    <div class="rpt-info-box">
        <h3>Informasi Pasien</h3>
        <table class="rpt-info-table">
            <tr>
                <td class="lbl">Nama</td>
                <td class="val">{{ $patient['name'] ?? '-' }}</td>
                <td class="lbl">No. RM</td>
                <td class="val">{{ $patient['medical_record_number'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">NIK</td>
                <td class="val">{{ $patient['nik'] ?? '-' }}</td>
                <td class="lbl">No. IHS</td>
                <td class="val">{{ $patient['ihs_number'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Tgl Lahir</td>
                <td class="val">{{ $patient['date_of_birth'] ?? '-' }} @if(!empty($patient['age']))({{ $patient['age'] }} Thn)@endif</td>
                <td class="lbl">Kelamin</td>
                <td class="val">{{ $patient['gender'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Gol. Darah</td>
                <td class="val">{{ $patient['blood_type'] ?? '-' }}</td>
                <td class="lbl">Tipe</td>
                <td class="val">{{ $patient['patient_type'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- ====== SOAP + Pain Scale ====== --}}
    <div class="rpt-section">
        <div class="rpt-sec-title">Catatan Klinis (SOAP)</div>
        <div class="rpt-sec-body">
            <table class="rpt-soap-table">
                <tr>
                    <td>
                        <div class="rpt-soap-card rpt-soap-S">
                            <div class="rpt-soap-lbl">S – Subjektif</div>
                            <div class="rpt-soap-txt">{{ $soap['subjective'] ?? '-' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="rpt-soap-card rpt-soap-O">
                            <div class="rpt-soap-lbl">O – Objektif</div>
                            <div class="rpt-soap-txt">{{ $soap['objective'] ?? '-' }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="rpt-soap-card rpt-soap-A">
                            <div class="rpt-soap-lbl">A – Assessment</div>
                            <div class="rpt-soap-txt">{{ $soap['assessment'] ?? '-' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="rpt-soap-card rpt-soap-P">
                            <div class="rpt-soap-lbl">P – Plan</div>
                            <div class="rpt-soap-txt">{{ $soap['plan'] ?? '-' }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            @if(!empty($pain_scale))
                <div class="rpt-pain-bar">
                    <span class="pain-lbl">Skala Nyeri:</span>
                    <span class="pain-val">{{ $pain_scale['value'] ?? '-' }}</span>
                    <span class="pain-desc">{{ $pain_scale['interpretation'] ?? '-' }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ====== VITAL SIGNS ====== --}}
    @if(!empty($vital_signs) && count($vital_signs) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Tanda Vital</div>
            <div class="rpt-sec-body">
                <table class="rpt-vitals-table">
                    @foreach(array_chunk($vital_signs, 3) as $row)
                        <tr>
                            @foreach($row as $vital)
                                <td>
                                    <div class="rpt-vital">
                                        <div class="rpt-vital-lbl">{{ $vital['label'] ?? '-' }}</div>
                                        <div class="rpt-vital-val">{{ $vital['value'] ?? '-' }}</div>
                                        <div class="rpt-vital-unit">{{ $vital['unit'] ?? '' }}</div>
                                        @if(!empty($vital['status']))
                                            <div class="rpt-vital-status status-{{ $vital['status_class'] ?? 'normal' }}">
                                                {{ $vital['status'] }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            @for($i = count($row); $i < 3; $i++)
                                <td></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif

    {{-- ====== ANTHROPOMETRY ====== --}}
    @if(!empty($anthropometry) && count($anthropometry) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Antropometri</div>
            <div class="rpt-sec-body">
                <table class="rpt-anthro-table">
                    @foreach(array_chunk($anthropometry, 3) as $row)
                        <tr>
                            @foreach($row as $anthro)
                                <td>
                                    <div class="rpt-anthro-item clearfix">
                                        <span class="a-lbl">{{ $anthro['label'] ?? '-' }}</span>
                                        <span class="a-val">{{ $anthro['value'] ?? '-' }} {{ $anthro['unit'] ?? '' }}</span>
                                    </div>
                                </td>
                            @endforeach
                            @for($i = count($row); $i < 3; $i++)
                                <td></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif

    {{-- ====== SYMPTOMS ====== --}}
    @if(!empty($symptoms) && count($symptoms) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Keluhan / Simtom</div>
            <div class="rpt-sec-body">
                @foreach($symptoms as $symptom)
                    <span class="rpt-tag rpt-tag-blue">{{ $symptom['name'] ?? '-' }}</span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====== ALLERGIES ====== --}}
    @if(!empty($allergies) && count($allergies) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Alergi</div>
            <div class="rpt-sec-body">
                @foreach($allergies as $allergy)
                    <div class="rpt-allergy-row">
                        @if(!empty($allergy['allergy_type']))
                            <span class="rpt-tag rpt-tag-red">{{ $allergy['allergy_type'] }}</span>
                        @endif
                        <span class="a-name">{{ $allergy['name'] ?? '-' }}</span>
                        @if(!empty($allergy['severity']))
                            <span class="rpt-tag rpt-tag-yellow">{{ $allergy['severity'] }}</span>
                        @endif
                        @if(!empty($allergy['allergen']))
                            <span class="rpt-allergy-sub">- Allergen: {{ $allergy['allergen'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====== MEDICAL HISTORY ====== --}}
    @if((!empty($history_illnesses) && count($history_illnesses) > 0) || (!empty($family_illnesses) && count($family_illnesses) > 0))
        <div class="rpt-section">
            <div class="rpt-sec-title">Riwayat Penyakit</div>
            <div class="rpt-sec-body">
                @if(!empty($history_illnesses) && count($history_illnesses) > 0)
                    <div class="rpt-hist-label">Pribadi:</div>
                    @foreach($history_illnesses as $illness)
                        <div class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-blue">{{ $illness['code'] ?? '-' }}</span>
                            {{ $illness['name'] ?? '-' }}
                        </div>
                    @endforeach
                @endif
                @if(!empty($family_illnesses) && count($family_illnesses) > 0)
                    <div class="rpt-hist-label" style="margin-top: 4px;">Keluarga:</div>
                    @foreach($family_illnesses as $illness)
                        <div class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-purple">{{ $illness['code'] ?? '-' }}</span>
                            {{ $illness['name'] ?? '-' }}
                            <span class="rpt-hist-sub">({{ $illness['family_name'] ?? '-' }})</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    {{-- ====== DIAGNOSES ====== --}}
    @if(!empty($diagnoses) && count($diagnoses) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Diagnosa</div>
            <div class="rpt-sec-body">
                @foreach($diagnoses as $diagnosis)
                    @php
                        $badgeClass = 'badge-secondary';
                        if (($diagnosis['type'] ?? '') === 'Initial') $badgeClass = 'badge-initial';
                        elseif (($diagnosis['type'] ?? '') === 'Primary') $badgeClass = 'badge-primary';
                    @endphp
                    <div class="rpt-dg-row">
                        <span class="rpt-dg-badge {{ $badgeClass }}">{{ $diagnosis['type_label'] ?? 'Dx' }}</span>
                        <span class="rpt-dg-code">{{ $diagnosis['code'] ?? '-' }}</span> – {{ $diagnosis['name'] ?? '-' }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====== PRESCRIPTIONS ====== --}}
    @if(!empty($prescriptions) && count($prescriptions) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Resep</div>
            <div class="rpt-sec-body">
                @foreach($prescriptions as $rx)
                    <div class="rpt-rx-card">
                        <div class="rpt-rx-header">
                            <span class="rpt-rx-badge">Rx</span>
                            <span class="rpt-rx-name">{{ $rx['name'] ?? '-' }}</span>
                        </div>
                        <div class="rpt-rx-details">
                            <table>
                                <tr>
                                    <td class="d-lbl">Jumlah:</td>
                                    <td class="d-val">{{ $rx['qty'] ?? '-' }}</td>
                                    <td class="d-lbl">Frekuensi:</td>
                                    <td class="d-val">{{ $rx['frequency'] ?? '-' }}</td>
                                </tr>
                                @if(!empty($rx['timing']) && $rx['timing'] !== '-')
                                <tr>
                                    <td class="d-lbl">Waktu:</td>
                                    <td class="d-val" colspan="3">{{ $rx['timing'] }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====== CLINICAL TREATMENTS ====== --}}
    @if(!empty($treatments) && count($treatments) > 0)
        <div class="rpt-section">
            <div class="rpt-sec-title">Tindakan Klinis</div>
            <div class="rpt-sec-body">
                @foreach($treatments as $treatment)
                    <div class="rpt-treatment-row clearfix">
                        <span class="rpt-treatment-name">{{ $treatment['name'] ?? '-' }}</span>
                        <span class="rpt-treatment-qty">x{{ $treatment['qty'] ?? '1' }}</span>
                        @if(!empty($treatment['result']) && $treatment['result'] !== '-')
                            <span class="rpt-treatment-result">{{ $treatment['result'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ====== FOOTER / SIGNATURE ====== --}}
    <div class="rpt-footer">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="rpt-sig-block">
                        <div class="rpt-sig-line"></div>
                        <div class="rpt-sig-name">{{ $practitioner['name'] ?? '-' }}</div>
                        <div class="rpt-sig-title">{{ $practitioner['profession'] ?? '-' }}</div>
                        @if(!empty($practitioner['sip_number']))
                            <div class="rpt-sig-title">SIP: {{ $practitioner['sip_number'] }}</div>
                        @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: bottom;">
                    <div class="rpt-footer-note">
                        Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
                        Dokumen resmi WELLMED
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
