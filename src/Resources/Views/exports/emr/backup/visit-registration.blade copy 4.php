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
            line-height: 1.4;
            background: #fff;
        }
        @page { size: A4; margin: 10mm 12mm 10mm 12mm; }

        .rpt-wrap { width: 100%; }

        /* Header */
        .rpt-header { text-align: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #d0d0d0; }
        .rpt-header h1 { font-size: 13px; color: #333; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
        .rpt-header .rpt-subtitle { font-size: 9px; color: #555; }
        .rpt-header .rpt-meta { font-size: 7px; color: #777; margin-top: 4px; }

        /* Patient Info */
        .rpt-info-box { background: #f5f5f5; padding: 6px 8px; margin-bottom: 8px; border: 1px solid #e0e0e0; }
        .rpt-info-box h3 { font-size: 8px; color: #333; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.3px; }
        .rpt-info-table { width: 100%; border-collapse: collapse; }
        .rpt-info-table td { padding: 2px 4px; font-size: 7px; }
        .rpt-info-table .lbl { color: #666; width: 50px; text-transform: uppercase; }
        .rpt-info-table .val { color: #222; font-weight: 500; }

        /* 3-Column Grid */
        .rpt-grid { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .rpt-grid td { width: 33.33%; vertical-align: top; padding: 0 3px; }
        .rpt-grid td:first-child { padding-left: 0; }
        .rpt-grid td:last-child { padding-right: 0; }

        /* Section */
        .rpt-section { background: #fff; border: 1px solid #e0e0e0; }
        .rpt-sec-title { background: #e8e8e8; color: #333; padding: 3px 6px; font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #d0d0d0; }
        .rpt-sec-body { padding: 0; background: #fafafa; min-height: 35px; }

        /* SOAP */
        .rpt-soap-grid { width: 100%; border-collapse: collapse; }
        .rpt-soap-grid td { width: 50%; vertical-align: top; padding: 0; }
        .rpt-soap-item { padding: 4px 6px; background: #f8f8f8; border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; }
        .rpt-soap-item:last-child { border-right: none; }
        .rpt-soap-lbl { font-size: 7px; font-weight: bold; margin-bottom: 2px; color: #444; text-transform: uppercase; }
        .rpt-soap-txt { font-size: 7px; color: #555; }

        /* Pain Scale */
        .rpt-pain { margin-top: 0; padding: 4px 8px; background: #f0f0f0; border-top: 1px solid #e0e0e0; }
        .rpt-pain .lbl { font-size: 7px; color: #555; text-transform: uppercase; }
        .rpt-pain .val { font-size: 11px; font-weight: bold; color: #333; margin: 0 6px; }
        .rpt-pain .desc { font-size: 7px; color: #666; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #e8e8e8; color: #444; font-size: 6px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.2px; padding: 3px 4px; text-align: left; border-bottom: 1px solid #d0d0d0; }
        .data-table td { padding: 3px 4px; font-size: 7px; color: #333; border-bottom: 1px solid #eee; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table .col-label { width: 35%; color: #555; }
        .data-table .col-value { font-weight: 600; }
        .data-table .col-unit { width: 15%; color: #777; font-size: 6px; }
        .data-table .col-status { width: 20%; }

        /* Status Badge */
        .status-badge { display: inline-block; padding: 1px 4px; font-size: 6px; font-weight: 600; background: #e0e0e0; color: #555; }
        .status-normal { background: #e8e8e8; color: #444; }
        .status-warning { background: #ddd; color: #555; }
        .status-critical { background: #ccc; color: #333; }

        /* Tag */
        .tag { display: inline-block; padding: 1px 5px; font-size: 6px; font-weight: 600; background: #e5e5e5; color: #444; margin: 1px; }

        /* Type Badge */
        .type-badge { display: inline-block; padding: 1px 4px; font-size: 6px; font-weight: bold; background: #d8d8d8; color: #333; margin-right: 4px; }

        /* Footer */
        .rpt-footer { margin-top: 10px; padding-top: 8px; border-top: 1px solid #d0d0d0; }
        .rpt-footer table { width: 100%; }
        .sig-block { width: 120px; }
        .sig-line { border-bottom: 1px solid #333; height: 25px; margin-bottom: 3px; }
        .sig-name { font-size: 7px; font-weight: bold; color: #333; }
        .sig-title { font-size: 6px; color: #666; }
        .footer-note { font-size: 6px; color: #888; text-align: right; }

        .clearfix::after { content: ""; display: table; clear: both; }
        .text-center { text-align: center; }
        .text-muted { color: #aaa; font-size: 7px; padding: 8px; }
        .list-label { font-size: 6px; color: #555; font-weight: bold; text-transform: uppercase; padding: 4px 4px 2px 4px; background: #f0f0f0; border-bottom: 1px solid #e0e0e0; }
    </style>
</head>
<body>
<div class="rpt-wrap">

    {{-- HEADER --}}
    <div class="rpt-header">
        <h1>Hasil Pemeriksaan Medis</h1>
        <div class="rpt-subtitle">{{ $visit_registration['medic_service']['name'] ?? 'Pelayanan Umum' }}</div>
        <div class="rpt-meta">
            {{ $visit_registration['visit_date'] ?? '-' }} &bull;
            {{ $practitioner['name'] ?? '-' }} ({{ $practitioner['profession'] ?? '-' }})
            @if(!empty($practitioner['sip_number'])) &bull; SIP: {{ $practitioner['sip_number'] }}@endif
        </div>
    </div>

    {{-- PATIENT INFO --}}
    <div class="rpt-info-box">
        <h3>Informasi Pasien</h3>
        <table class="rpt-info-table">
            <tr>
                <td class="lbl">Nama</td><td class="val">{{ $patient['name'] ?? '-' }}</td>
                <td class="lbl">No. RM</td><td class="val">{{ $patient['medical_record_number'] ?? '-' }}</td>
                <td class="lbl">NIK</td><td class="val">{{ $patient['nik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Lahir</td><td class="val">{{ $patient['date_of_birth'] ?? '-' }}@if(!empty($patient['age'])) ({{ $patient['age'] }} th)@endif</td>
                <td class="lbl">Kelamin</td><td class="val">{{ $patient['gender'] ?? '-' }}</td>
                <td class="lbl">Darah</td><td class="val">{{ $patient['blood_type'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- SOAP --}}
    <div class="rpt-section" style="margin-bottom: 6px;">
        <div class="rpt-sec-title">Catatan Klinis (SOAsssP)</div>
        <div class="rpt-sec-body" style="padding: 0;">
            <table class="rpt-soap-grid">
                <tr>
                    <td><div class="rpt-soap-item"><div class="rpt-soap-lbl">S - Subjektif</div><div class="rpt-soap-txt">{{ $soap['subjective'] ?? '-' }}</div></div></td>
                    <td><div class="rpt-soap-item"><div class="rpt-soap-lbl">O - Objektif</div><div class="rpt-soap-txt">{{ $soap['objective'] ?? '-' }}</div></div></td>
                </tr>
                <tr>
                    <td><div class="rpt-soap-item"><div class="rpt-soap-lbl">A - Assessment</div><div class="rpt-soap-txt">{{ $soap['assessment'] ?? '-' }}</div></div></td>
                    <td><div class="rpt-soap-item"><div class="rpt-soap-lbl">P - Plan</div><div class="rpt-soap-txt">{{ $soap['plan'] ?? '-' }}</div></div></td>
                </tr>
            </table>
            @if(!empty($pain_scale))
                <div class="rpt-pain">
                    <span class="lbl">Skala Nyeri:</span>
                    <span class="val">{{ $pain_scale['value'] ?? '-' }}</span>
                    <span class="desc">{{ $pain_scale['interpretation'] ?? '' }}</span>
                </div>
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
                        @if(!empty($vital_signs))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>PARAMETER</th>
                                        <th>NILAI</th>
                                        <th>SATUAN</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vital_signs as $v)
                                        <tr>
                                            <td class="col-label">{{ $v['label'] ?? '-' }}</td>
                                            <td class="col-value">{{ $v['value'] ?? '-' }}</td>
                                            <td class="col-unit">{{ $v['unit'] ?? '' }}</td>
                                            <td class="col-status">
                                                @if(!empty($v['status']))
                                                    <span class="status-badge status-{{ $v['status_class'] ?? 'normal' }}">{{ $v['status'] }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Antropometri</div>
                    <div class="rpt-sec-body">
                        @if(!empty($anthropometry))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>PARAMETER</th>
                                        <th>NILAI</th>
                                        <th>SATUAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anthropometry as $a)
                                        <tr>
                                            <td class="col-label">{{ $a['label'] ?? '-' }}</td>
                                            <td class="col-value">{{ $a['value'] ?? '-' }}</td>
                                            <td class="col-unit">{{ $a['unit'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Keluhan</div>
                    <div class="rpt-sec-body">
                        @if(!empty($symptoms))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>NAMA KELUHAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($symptoms as $s)
                                        <tr>
                                            <td>{{ $s['name'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
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
                        @if(!empty($allergies))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>TIPE</th>
                                        <th>NAMA</th>
                                        <th>TINGKAT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allergies as $a)
                                        <tr>
                                            <td>{{ $a['allergy_type'] ?? '-' }}</td>
                                            <td class="col-value">{{ $a['name'] ?? '-' }}</td>
                                            <td>{{ $a['severity'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Riwayat Penyakit</div>
                    <div class="rpt-sec-body">
                        @if(!empty($history_illnesses) || !empty($family_illnesses))
                            @if(!empty($history_illnesses))
                                <div class="list-label">PRIBADI</div>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>KODE</th>
                                            <th>NAMA PENYAKIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($history_illnesses as $h)
                                            <tr>
                                                <td>{{ $h['code'] ?? '-' }}</td>
                                                <td class="col-value">{{ $h['name'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if(!empty($family_illnesses))
                                <div class="list-label">KELUARGA</div>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>KODE</th>
                                            <th>NAMA PENYAKIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($family_illnesses as $f)
                                            <tr>
                                                <td>{{ $f['code'] ?? '-' }}</td>
                                                <td class="col-value">{{ $f['name'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Diagnosa</div>
                    <div class="rpt-sec-body">
                        @if(!empty($diagnoses))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>TIPE</th>
                                        <th>KODE</th>
                                        <th>NAMA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($diagnoses as $d)
                                        <tr>
                                            <td><span class="type-badge">{{ $d['type_label'] ?? 'DX' }}</span></td>
                                            <td class="col-value">{{ $d['code'] ?? '-' }}</td>
                                            <td>{{ $d['name'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
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
                    <div class="rpt-sec-title">Resep Obat</div>
                    <div class="rpt-sec-body">
                        @if(!empty($prescriptions))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>NAMA OBAT</th>
                                        <th>JUMLAH</th>
                                        <th>FREKUENSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prescriptions as $rx)
                                        <tr>
                                            <td class="col-value">{{ $rx['name'] ?? '-' }}</td>
                                            <td>{{ $rx['qty'] ?? '-' }}</td>
                                            <td>{{ $rx['frequency'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Tindakan Klinis</div>
                    <div class="rpt-sec-body">
                        @if(!empty($treatments))
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>NAMA TINDAKAN</th>
                                        <th>QTY</th>
                                        <th>HASIL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($treatments as $t)
                                        <tr>
                                            <td class="col-value">{{ $t['name'] ?? '-' }}</td>
                                            <td>{{ $t['qty'] ?? '1' }}</td>
                                            <td>{{ $t['result'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted">Tidak ada data</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="rpt-section">
                    <div class="rpt-sec-title">Catatan Tambahan</div>
                    <div class="rpt-sec-body">
                        <div class="text-center text-muted">-</div>
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
                    <div class="sig-block">
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $practitioner['name'] ?? '-' }}</div>
                        <div class="sig-title">{{ $practitioner['profession'] ?? '-' }}</div>
                        @if(!empty($practitioner['sip_number']))<div class="sig-title">SIP: {{ $practitioner['sip_number'] }}</div>@endif
                    </div>
                </td>
                <td style="width:50%;vertical-align:bottom;">
                    <div class="footer-note">
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
