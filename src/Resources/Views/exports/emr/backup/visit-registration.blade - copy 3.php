<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hasil Pemeriksaan Medis</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            color: #333;
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        @page { size: A4; margin: 10mm 12mm 10mm 12mm; }

        .rpt-wrap { width: 100%; padding: 0; }

        /* Header */
        .rpt-header { text-align: center; margin-bottom: 6px; padding-bottom: 5px; border-bottom: 2px double #003049; }
        .rpt-header h1 { font-size: 12px; color: #003049; text-transform: uppercase; margin-bottom: 1px; }
        .rpt-header .rpt-subtitle { font-size: 9px; color: #555; }
        .rpt-header .rpt-meta { font-size: 7px; color: #666; margin-top: 3px; }

        /* Info box */
        .rpt-info-box { background: #f5f9ff; border: 1px solid #d0e0f0; padding: 4px 6px; margin-bottom: 5px; }
        .rpt-info-box h3 { font-size: 8px; color: #003049; margin-bottom: 3px; padding-left: 5px; border-left: 2px solid #003049; }
        .rpt-info-table { width: 100%; border-collapse: collapse; }
        .rpt-info-table td { padding: 1px 2px; font-size: 7px; vertical-align: top; }
        .rpt-info-table .lbl { font-weight: bold; color: #555; width: 50px; }

        /* 3-Column Row */
        .rpt-grid { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .rpt-grid td { width: 33.33%; vertical-align: top; padding: 0 2px; }
        .rpt-grid td:first-child { padding-left: 0; }
        .rpt-grid td:last-child { padding-right: 0; }

        /* Section */
        .rpt-section { page-break-inside: avoid; }
        .rpt-sec-title { background: #003049; color: #fff; padding: 2px 4px; font-size: 7px; font-weight: bold; }
        .rpt-sec-body { border: 1px solid #ccc; border-top: none; padding: 3px; min-height: 30px; }

        /* SOAP */
        .rpt-soap-table { width: 100%; border-collapse: collapse; }
        .rpt-soap-table td { width: 50%; vertical-align: top; padding: 1px; }
        .rpt-soap-card { background: #fafafa; border: 1px solid #ddd; padding: 2px 4px; }
        .rpt-soap-lbl { font-weight: bold; font-size: 7px; }
        .rpt-soap-S .rpt-soap-lbl { color: #2563eb; }
        .rpt-soap-O .rpt-soap-lbl { color: #7c3aed; }
        .rpt-soap-A .rpt-soap-lbl { color: #db2777; }
        .rpt-soap-P .rpt-soap-lbl { color: #16a34a; }
        .rpt-soap-txt { font-size: 7px; color: #444; }

        /* Pain Scale */
        .rpt-pain-bar { margin-top: 3px; padding: 2px 4px; background: #fff5f5; border: 1px solid #fcc; font-size: 7px; }
        .rpt-pain-bar .pain-val { font-size: 10px; font-weight: bold; color: #dc2626; margin: 0 4px; }

        /* Vitals */
        .rpt-vital-item { background: #fafafa; border: 1px solid #ddd; padding: 2px; margin-bottom: 2px; text-align: center; }
        .rpt-vital-lbl { font-size: 6px; color: #666; text-transform: uppercase; }
        .rpt-vital-val { font-size: 9px; font-weight: bold; color: #1e293b; }
        .rpt-vital-unit { font-size: 6px; color: #888; }
        .rpt-vital-status { font-size: 6px; font-weight: bold; padding: 1px 2px; }
        .status-normal { background: #d1fae5; color: #065f46; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-critical { background: #fee2e2; color: #991b1b; }

        /* Anthropometry */
        .rpt-anthro-item { background: #fafafa; border: 1px solid #ddd; padding: 2px 3px; margin-bottom: 2px; font-size: 7px; }
        .rpt-anthro-item .a-val { font-weight: bold; float: right; }

        /* Tags */
        .rpt-tag { display: inline-block; padding: 1px 3px; font-size: 6px; font-weight: bold; margin: 1px; border: 1px solid; }
        .rpt-tag-blue { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .rpt-tag-red { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .rpt-tag-yellow { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .rpt-tag-green { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .rpt-tag-purple { background: #ede9fe; color: #5b21b6; border-color: #c4b5fd; }

        /* List items */
        .rpt-list-item { padding: 1px 0; border-bottom: 1px solid #eee; font-size: 7px; }
        .rpt-list-item:last-child { border-bottom: none; }
        .item-name { font-weight: bold; }
        .item-sub { font-size: 6px; color: #666; }

        /* Diagnose badge */
        .rpt-dg-badge { display: inline-block; min-width: 30px; text-align: center; padding: 1px 2px; font-size: 6px; font-weight: bold; margin-right: 3px; }
        .badge-initial { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #d1fae5; color: #065f46; }
        .badge-secondary { background: #fef3c7; color: #92400e; }

        /* Prescription */
        .rpt-rx-item { background: #faf5ff; border: 1px solid #e9d5ff; padding: 2px 3px; margin-bottom: 2px; }
        .rpt-rx-name { font-size: 7px; font-weight: bold; color: #5b21b6; }
        .rpt-rx-detail { font-size: 6px; color: #666; }

        /* Treatment */
        .rpt-treatment-item { padding: 1px 0; border-bottom: 1px solid #eee; font-size: 7px; }
        .rpt-treatment-item:last-child { border-bottom: none; }
        .rpt-treatment-result { color: #059669; font-weight: bold; float: right; font-size: 6px; }

        /* Footer */
        .rpt-footer { margin-top: 8px; padding-top: 5px; border-top: 1px dashed #999; }
        .rpt-footer table { width: 100%; }
        .rpt-sig-block { text-align: center; width: 100px; }
        .rpt-sig-line { border-bottom: 1px solid #333; height: 20px; margin-bottom: 2px; }
        .rpt-sig-name { font-size: 7px; font-weight: bold; }
        .rpt-sig-title { font-size: 6px; color: #666; }
        .rpt-footer-note { font-size: 6px; color: #999; text-align: right; }

        .clearfix::after { content: ""; display: table; clear: both; }
        .text-center { text-align: center; }
        .text-muted { color: #999; }
    </style>
</head>
<body>
<div class="rpt-wrap">

    {{-- HEADER --}}
    <div class="rpt-header">
        <h1>Hasil Pemeriksaan Medis</h1>
        <div class="rpt-subtitle">{{ $visit_registration['medic_service']['name'] ?? 'Pelayanan Umum' }}</div>
        <div class="rpt-meta">
            Tanggal: {{ $visit_registration['visit_date'] ?? '-' }} |
            Dokter: {{ $practitioner['name'] ?? '-' }} ({{ $practitioner['profession'] ?? '-' }})
            @if(!empty($practitioner['sip_number'])) | SIP: {{ $practitioner['sip_number'] }}@endif
        </div>
    </div>

    {{-- PATIENT INFO --}}
    <div class="rpt-info-box">
        <h3>Informasi Pasien</h3>
        <table class="rpt-info-table">
            <tr>
                <td class="lbl">Nama</td><td>{{ $patient['name'] ?? '-' }}</td>
                <td class="lbl">No. RM</td><td>{{ $patient['medical_record_number'] ?? '-' }}</td>
                <td class="lbl">NIK</td><td>{{ $patient['nik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Tgl Lahir</td><td>{{ $patient['date_of_birth'] ?? '-' }}@if(!empty($patient['age'])) ({{ $patient['age'] }}Th)@endif</td>
                <td class="lbl">Kelamin</td><td>{{ $patient['gender'] ?? '-' }}</td>
                <td class="lbl">Gol Darah</td><td>{{ $patient['blood_type'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- SOAP --}}
    <div class="rpt-section" style="margin-bottom: 5px;">
        <div class="rpt-sec-title">Catatan Klinis (SOAP)</div>
        <div class="rpt-sec-body">
            <table class="rpt-soap-table">
                <tr>
                    <td><div class="rpt-soap-card rpt-soap-S"><div class="rpt-soap-lbl">S – Subjektif</div><div class="rpt-soap-txt">{{ $soap['subjective'] ?? '-' }}</div></div></td>
                    <td><div class="rpt-soap-card rpt-soap-O"><div class="rpt-soap-lbl">O – Objektif</div><div class="rpt-soap-txt">{{ $soap['objective'] ?? '-' }}</div></div></td>
                </tr>
                <tr>
                    <td><div class="rpt-soap-card rpt-soap-A"><div class="rpt-soap-lbl">A – Assessment</div><div class="rpt-soap-txt">{{ $soap['assessment'] ?? '-' }}</div></div></td>
                    <td><div class="rpt-soap-card rpt-soap-P"><div class="rpt-soap-lbl">P – Plan</div><div class="rpt-soap-txt">{{ $soap['plan'] ?? '-' }}</div></div></td>
                </tr>
            </table>
            @if(!empty($pain_scale))
                <div class="rpt-pain-bar">Skala Nyeri: <span class="pain-val">{{ $pain_scale['value'] ?? '-' }}</span> {{ $pain_scale['interpretation'] ?? '-' }}</div>
            @endif
        </div>
    </div>

    {{-- ROW 1: Vital Signs | Anthropometry | Symptoms --}}
    <table class="rpt-grid">
        <tr>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Tanda Vital</div>
                    <div class="rpt-sec-body">
                        @forelse($vital_signs ?? [] as $vital)
                            <div class="rpt-vital-item">
                                <div class="rpt-vital-lbl">{{ $vital['label'] ?? '-' }}</div>
                                <div class="rpt-vital-val">{{ $vital['value'] ?? '-' }} <span class="rpt-vital-unit">{{ $vital['unit'] ?? '' }}</span></div>
                                @if(!empty($vital['status']))<div class="rpt-vital-status status-{{ $vital['status_class'] ?? 'normal' }}">{{ $vital['status'] }}</div>@endif
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Antropometri</div>
                    <div class="rpt-sec-body">
                        @forelse($anthropometry ?? [] as $anthro)
                            <div class="rpt-anthro-item clearfix">
                                <span>{{ $anthro['label'] ?? '-' }}</span>
                                <span class="a-val">{{ $anthro['value'] ?? '-' }} {{ $anthro['unit'] ?? '' }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Keluhan</div>
                    <div class="rpt-sec-body">
                        @forelse($symptoms ?? [] as $s)
                            <span class="rpt-tag rpt-tag-blue">{{ $s['name'] ?? '-' }}</span>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ROW 2: Allergies | Medical History | Diagnoses --}}
    <table class="rpt-grid">
        <tr>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Alergi</div>
                    <div class="rpt-sec-body">
                        @forelse($allergies ?? [] as $a)
                            <div class="rpt-list-item">
                                @if(!empty($a['allergy_type']))<span class="rpt-tag rpt-tag-red">{{ $a['allergy_type'] }}</span>@endif
                                <span class="item-name">{{ $a['name'] ?? '-' }}</span>
                                @if(!empty($a['severity']))<span class="rpt-tag rpt-tag-yellow">{{ $a['severity'] }}</span>@endif
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Riwayat Penyakit</div>
                    <div class="rpt-sec-body">
                        @if(!empty($history_illnesses) || !empty($family_illnesses))
                            @if(!empty($history_illnesses))
                                <div style="font-weight:bold;font-size:6px;color:#555;">Pribadi:</div>
                                @foreach($history_illnesses as $h)
                                    <div class="rpt-list-item"><span class="rpt-tag rpt-tag-blue">{{ $h['code'] ?? '-' }}</span> {{ $h['name'] ?? '-' }}</div>
                                @endforeach
                            @endif
                            @if(!empty($family_illnesses))
                                <div style="font-weight:bold;font-size:6px;color:#555;margin-top:2px;">Keluarga:</div>
                                @foreach($family_illnesses as $f)
                                    <div class="rpt-list-item"><span class="rpt-tag rpt-tag-purple">{{ $f['code'] ?? '-' }}</span> {{ $f['name'] ?? '-' }}</div>
                                @endforeach
                            @endif
                        @else
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Diagnosa</div>
                    <div class="rpt-sec-body">
                        @forelse($diagnoses ?? [] as $dg)
                            @php
                                $bc = 'badge-secondary';
                                if (($dg['type'] ?? '') === 'Initial') $bc = 'badge-initial';
                                elseif (($dg['type'] ?? '') === 'Primary') $bc = 'badge-primary';
                            @endphp
                            <div class="rpt-list-item">
                                <span class="rpt-dg-badge {{ $bc }}">{{ $dg['type_label'] ?? 'Dx' }}</span>
                                <span class="item-name">{{ $dg['code'] ?? '-' }}</span> {{ $dg['name'] ?? '-' }}
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ROW 3: Prescriptions | Treatments | Notes --}}
    <table class="rpt-grid">
        <tr>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Resep</div>
                    <div class="rpt-sec-body">
                        @forelse($prescriptions ?? [] as $rx)
                            <div class="rpt-rx-item">
                                <div class="rpt-rx-name">{{ $rx['name'] ?? '-' }}</div>
                                <div class="rpt-rx-detail">{{ $rx['qty'] ?? '-' }} | {{ $rx['frequency'] ?? '-' }}</div>
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Tindakan Klinis</div>
                    <div class="rpt-sec-body">
                        @forelse($treatments ?? [] as $t)
                            <div class="rpt-treatment-item clearfix">
                                <span class="item-name">{{ $t['name'] ?? '-' }}</span> <span class="item-sub">x{{ $t['qty'] ?? '1' }}</span>
                                @if(!empty($t['result']) && $t['result'] !== '-')<span class="rpt-treatment-result">{{ $t['result'] }}</span>@endif
                            </div>
                        @empty
                            <div class="text-center text-muted" style="padding:8px;">-</div>
                        @endforelse
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Catatan</div>
                    <div class="rpt-sec-body">
                        <div class="text-center text-muted" style="padding:8px;">-</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="rpt-footer">
        <table>
            <tr>
                <td style="width:50%;vertical-align:bottom;">
                    <div class="rpt-sig-block">
                        <div class="rpt-sig-line"></div>
                        <div class="rpt-sig-name">{{ $practitioner['name'] ?? '-' }}</div>
                        <div class="rpt-sig-title">{{ $practitioner['profession'] ?? '-' }}</div>
                        @if(!empty($practitioner['sip_number']))<div class="rpt-sig-title">SIP: {{ $practitioner['sip_number'] }}</div>@endif
                    </div>
                </td>
                <td style="width:50%;vertical-align:bottom;">
                    <div class="rpt-footer-note">Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>Dokumen resmi WELLMED</div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
