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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @page { size: A4; margin: 15mm 18mm; }

        /* Container */
        .rpt-wrap { max-width: 760px; margin: 0 auto; padding: 10px; }

        /* Header */
        .rpt-header { text-align: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 3px double #003049; }
        .rpt-header h1 { font-size: 18px; color: #003049; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .rpt-header .rpt-subtitle { font-size: 12px; color: #555; margin-bottom: 8px; }
        .rpt-header .rpt-meta { font-size: 10px; color: #777; }
        .rpt-header .rpt-meta span { margin: 0 10px; }

        /* Info box */
        .rpt-info-box { background: #f0f7ff; border: 1px solid #c7dff0; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; }
        .rpt-info-box h3 { font-size: 11px; color: #003049; margin-bottom: 8px; padding-left: 10px; border-left: 3px solid #003049; }
        .rpt-info-table { width: 100%; border-collapse: collapse; }
        .rpt-info-table td { padding: 2px 5px; font-size: 10.5px; vertical-align: top; }
        .rpt-info-table .lbl { font-weight: 600; color: #556070; width: 100px; }
        .rpt-info-table .val { color: #333; }

        /* Section */
        .rpt-section { margin-bottom: 16px; page-break-inside: avoid; }
        .rpt-sec-title { background: #003049; color: #fff; padding: 5px 10px; font-size: 10.5px; font-weight: 600; border-radius: 4px 4px 0 0; }
        .rpt-sec-body { border: 1px solid #d0d5dd; border-top: none; padding: 10px; border-radius: 0 0 4px 4px; }

        /* SOAP Grid */
        .rpt-soap-table { width: 100%; border-collapse: separate; border-spacing: 4px; }
        .rpt-soap-table td { width: 50%; vertical-align: top; }
        .rpt-soap-card { background: #fff; border: 1px solid #e4e7ec; border-radius: 4px; padding: 8px; }
        .rpt-soap-lbl { font-weight: 700; font-size: 12px; margin-bottom: 3px; }
        .rpt-soap-S .rpt-soap-lbl { color: #2563eb; }
        .rpt-soap-O .rpt-soap-lbl { color: #7c3aed; }
        .rpt-soap-A .rpt-soap-lbl { color: #db2777; }
        .rpt-soap-P .rpt-soap-lbl { color: #16a34a; }
        .rpt-soap-txt { font-size: 10.5px; color: #444; font-style: italic; }

        /* Pain Scale */
        .rpt-pain-bar { margin-top: 8px; padding: 6px 10px; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; }
        .rpt-pain-bar .pain-lbl { font-weight: 600; color: #555; font-size: 10.5px; }
        .rpt-pain-bar .pain-val { font-size: 18px; font-weight: 700; color: #db2777; margin: 0 8px; }
        .rpt-pain-bar .pain-desc { font-size: 10.5px; color: #666; }

        /* Vitals Grid */
        .rpt-vitals-table { width: 100%; border-collapse: separate; border-spacing: 4px; }
        .rpt-vitals-table td { width: 33.33%; vertical-align: top; }
        .rpt-vital { background: #fff; border: 1px solid #e4e7ec; border-radius: 6px; padding: 8px; text-align: center; }
        .rpt-vital-lbl { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 3px; }
        .rpt-vital-val { font-size: 15px; font-weight: 700; color: #1e293b; }
        .rpt-vital-unit { font-size: 9px; color: #94a3b8; }
        .rpt-vital-status { font-size: 9px; font-weight: 600; margin-top: 3px; padding: 1px 6px; border-radius: 8px; display: inline-block; }
        .status-normal { background: #dcfce7; color: #166534; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-critical { background: #fee2e2; color: #991b1b; }

        /* Anthropometry Grid */
        .rpt-anthro-table { width: 100%; border-collapse: separate; border-spacing: 3px; }
        .rpt-anthro-table td { width: 33.33%; vertical-align: top; }
        .rpt-anthro-item { background: #fff; border: 1px solid #eee; border-radius: 3px; padding: 4px 8px; font-size: 10.5px; }
        .rpt-anthro-item .a-lbl { color: #666; }
        .rpt-anthro-item .a-val { font-weight: 600; color: #333; float: right; }

        /* Tags */
        .rpt-tag { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9.5px; font-weight: 600; margin: 1px; }
        .rpt-tag-blue { background: #dbeafe; color: #1e40af; }
        .rpt-tag-red { background: #fee2e2; color: #991b1b; }
        .rpt-tag-yellow { background: #fef3c7; color: #92400e; }
        .rpt-tag-green { background: #dcfce7; color: #166534; }
        .rpt-tag-purple { background: #ede9fe; color: #6d28d9; }

        /* Allergy */
        .rpt-allergy-row { padding: 5px 0; border-bottom: 1px solid #f3f4f6; }
        .rpt-allergy-row:last-child { border-bottom: none; }
        .rpt-allergy-row .a-name { font-weight: 600; font-size: 11px; margin: 0 5px; }
        .rpt-allergy-sub { font-size: 10px; color: #666; margin-top: 2px; }

        /* History */
        .rpt-hist-label { font-weight: 600; font-size: 10.5px; color: #555; margin-bottom: 4px; }
        .rpt-hist-row { padding: 3px 0; font-size: 10.5px; }
        .rpt-hist-sub { font-size: 10px; color: #777; }

        /* Diagnose */
        .rpt-dg-row { padding: 5px 0; border-bottom: 1px solid #f3f4f6; }
        .rpt-dg-row:last-child { border-bottom: none; }
        .rpt-dg-badge { display: inline-block; min-width: 50px; text-align: center; padding: 2px 5px; border-radius: 3px; font-size: 9.5px; font-weight: 700; margin-right: 8px; }
        .badge-initial { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #dcfce7; color: #166534; }
        .badge-secondary { background: #fef3c7; color: #92400e; }
        .rpt-dg-code { font-weight: 700; color: #333; font-size: 11px; display: inline; }
        .rpt-dg-name { font-size: 10px; color: #666; margin-top: 1px; margin-left: 58px; }

        /* Prescription */
        .rpt-rx-card { background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 6px; padding: 10px; margin-bottom: 8px; page-break-inside: avoid; }
        .rpt-rx-header { margin-bottom: 6px; }
        .rpt-rx-badge { font-size: 11px; font-weight: 700; background: #7c3aed; color: #fff; padding: 1px 6px; border-radius: 3px; margin-right: 8px; }
        .rpt-rx-name { font-size: 13px; font-weight: 700; color: #5b21b6; }
        .rpt-rx-details { font-size: 10.5px; }
        .rpt-rx-details table { width: 100%; }
        .rpt-rx-details td { padding: 1px 5px; vertical-align: top; }
        .rpt-rx-details .d-lbl { color: #7c3aed; font-weight: 600; width: 80px; }
        .rpt-rx-details .d-val { color: #444; }
        .rpt-rx-indication { margin-top: 6px; padding: 4px 8px; background: #f3e8ff; border-radius: 3px; font-size: 10.5px; color: #5b21b6; }

        /* Treatment */
        .rpt-treatment-row { padding: 5px 0; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .rpt-treatment-row:last-child { border-bottom: none; }
        .rpt-treatment-name { font-weight: 600; color: #333; }
        .rpt-treatment-qty { font-size: 10px; color: #999; margin-left: 5px; }
        .rpt-treatment-result { color: #16a34a; font-weight: 600; font-size: 10.5px; float: right; }

        /* Footer */
        .rpt-footer { margin-top: 30px; padding-top: 16px; border-top: 1px dashed #ccc; }
        .rpt-footer table { width: 100%; }
        .rpt-sig-block { text-align: center; width: 170px; }
        .rpt-sig-line { border-bottom: 1px solid #333; height: 40px; margin-bottom: 4px; }
        .rpt-sig-name { font-size: 10.5px; font-weight: 600; color: #333; }
        .rpt-sig-title { font-size: 9.5px; color: #777; }
        .rpt-footer-note { font-size: 9px; color: #aaa; text-align: right; }

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
                <td class="lbl">Tanggal Lahir</td>
                <td class="val">{{ $patient['date_of_birth'] ?? '-' }} @if(!empty($patient['age']))({{ $patient['age'] }} Thn)@endif</td>
                <td class="lbl">Jenis Kelamin</td>
                <td class="val">{{ $patient['gender'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Golongan Darah</td>
                <td class="val">{{ $patient['blood_type'] ?? '-' }}</td>
                <td class="lbl">Tipe Pasien</td>
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

            {{-- Pain Scale bar --}}
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
                                        <span class="a-val">
                                            {{ $anthro['value'] ?? '-' }} {{ $anthro['unit'] ?? '' }}
                                            @if(!empty($anthro['interpretation']))
                                                ({{ $anthro['interpretation'] }})
                                            @endif
                                        </span>
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
                        <div>
                            @if(!empty($allergy['allergy_type']))
                                <span class="rpt-tag rpt-tag-red">{{ $allergy['allergy_type'] }}</span>
                            @endif
                            <span class="a-name">{{ $allergy['name'] ?? '-' }}</span>
                            @if(!empty($allergy['severity']))
                                <span class="rpt-tag rpt-tag-yellow">{{ $allergy['severity'] }}</span>
                            @endif
                        </div>
                        @if(!empty($allergy['allergen']))
                            <div class="rpt-allergy-sub">Allergen: {{ $allergy['allergen'] }}</div>
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
                    <div>
                        <div class="rpt-hist-label">Pribadi:</div>
                        @foreach($history_illnesses as $illness)
                            <div class="rpt-hist-row">
                                <span class="rpt-tag rpt-tag-blue">{{ $illness['code'] ?? '-' }}</span>
                                <span>{{ $illness['name'] ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($family_illnesses) && count($family_illnesses) > 0)
                    <div style="margin-top: 8px;">
                        <div class="rpt-hist-label">Keluarga:</div>
                        @foreach($family_illnesses as $illness)
                            <div class="rpt-hist-row">
                                <span class="rpt-tag rpt-tag-purple">{{ $illness['code'] ?? '-' }}</span>
                                <span>{{ $illness['name'] ?? '-' }}</span>
                                <span class="rpt-hist-sub">({{ $illness['family_name'] ?? '-' }})</span>
                            </div>
                        @endforeach
                    </div>
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
                        <span class="rpt-dg-badge {{ $badgeClass }}">{{ $diagnosis['type_label'] ?? 'Diagnosis' }}</span>
                        <span class="rpt-dg-code">{{ $diagnosis['code'] ?? '-' }} – {{ $diagnosis['name'] ?? '-' }}</span>
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
                                <tr>
                                    <td class="d-lbl">Waktu:</td>
                                    <td class="d-val">{{ $rx['timing'] ?? '-' }}</td>
                                    @if(!empty($rx['indication']) && $rx['indication'] !== '-')
                                        <td class="d-lbl">Indikasi:</td>
                                        <td class="d-val">{{ $rx['indication'] }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
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
                        Laporan dicetak pada {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }}<br>
                        Dokumen resmi dari sistem WELLMED
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
