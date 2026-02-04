<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hasil Pemeriksaan Medis</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Calibri, 'Segoe UI', Tahoma, sans-serif;
            font-size: 8px;
            color: #2d3748;
            line-height: 1.35;
            background: #fff;
        }
        @page { size: A4; margin: 10mm 12mm 10mm 12mm; }

        .rpt-wrap { width: 100%; }

        /* Color Palette - Medical Blue Theme */
        /* Primary: #1a365d (dark navy) */
        /* Secondary: #2c5282 (medium blue) */
        /* Accent: #3182ce (bright blue) */
        /* Light: #ebf4ff (very light blue) */
        /* Border: #bee3f8 (soft blue) */

        /* Header */
        .rpt-header { margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #2c5282; }
        .header-table { width: 100%; }
        .header-left { width: 70px; vertical-align: top; }
        .header-right { vertical-align: top; padding-left: 10px; }
        .photo-box { width: 60px; height: 75px; border: 1px solid #bee3f8; background: #f7fafc; text-align: center; }
        .photo-box .placeholder { font-size: 6px; color: #a0aec0; padding-top: 30px; }
        .rpt-header h1 { font-size: 14px; color: #1a365d; margin-bottom: 3px; font-weight: bold; }
        .rpt-header .rpt-subtitle { font-size: 9px; color: #2c5282; margin-bottom: 6px; }

        /* Patient Info - Compact on right */
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table td { padding: 1px 0; font-size: 7.5px; }
        .patient-table .lbl { color: #4a5568; width: 55px; }
        .patient-table .val { color: #1a202c; font-weight: 600; }
        .patient-table .sep { width: 8px; color: #a0aec0; }

        /* Section Caption */
        .section-caption { font-size: 8px; font-weight: bold; color: #2c5282; margin-bottom: 3px; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.3px; }

        /* 3-Column Grid */
        .rpt-grid { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .rpt-grid > tbody > tr > td { width: 33.33%; vertical-align: top; padding: 0 4px 6px 0; }
        .rpt-grid > tbody > tr > td:last-child { padding-right: 0; }

        /* 2-Column Grid */
        .rpt-grid-2 { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .rpt-grid-2 > tbody > tr > td { width: 50%; vertical-align: top; padding: 0 4px 6px 0; }
        .rpt-grid-2 > tbody > tr > td:last-child { padding-right: 0; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; border: 1px solid #bee3f8; }
        .data-table th {
            background: #ebf4ff;
            color: #2c5282;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            padding: 3px 6px;
            text-align: left;
            border-bottom: 1px solid #bee3f8;
        }
        .data-table td {
            padding: 2px 6px;
            font-size: 7px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table .col-value { font-weight: 600; color: #1a202c; }
        .data-table .col-muted { color: #718096; font-size: 6.5px; }

        /* SOAP Table */
        .soap-table { width: 100%; border-collapse: collapse; border: 1px solid #bee3f8; }
        .soap-table th {
            background: #ebf4ff;
            color: #2c5282;
            font-size: 7px;
            font-weight: bold;
            padding: 3px 6px;
            text-align: left;
            border-bottom: 1px solid #bee3f8;
            width: 25%;
        }
        .soap-table td {
            padding: 3px 6px;
            font-size: 7px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .soap-table tr:last-child td { border-bottom: none; }

        /* Footer */
        .rpt-footer { margin-top: 12px; padding-top: 6px; border-top: 1px solid #bee3f8; }
        .footer-note { font-size: 6px; color: #718096; text-align: right; }

        .text-center { text-align: center; }
        .text-muted { color: #a0aec0; font-size: 7px; padding: 6px; font-style: italic; }
        .sub-label { font-size: 6.5px; color: #4a5568; font-weight: 600; margin: 4px 0 2px 0; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="rpt-wrap">

    {{-- HEADER WITH PHOTO SPACE AND PATIENT INFO --}}
    <div class="rpt-header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <div class="photo-box">
                        <div class="placeholder">FOTO<br>PASIEN</div>
                    </div>
                </td>
                <td class="header-right">
                    <h1>HASIL PEMERIKSAAN MEDIS</h1>
                    <div class="rpt-subtitle">{{ $visit_registration['medic_service']['name'] ?? 'Pelayanan Umum' }} &bull; {{ $visit_registration['visit_date'] ?? '-' }}</div>
                    <table class="patient-table">
                        <tr>
                            <td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $patient['name'] ?? '-' }}</td>
                            <td style="width:15px;"></td>
                            <td class="lbl">No. RM</td><td class="sep">:</td><td class="val">{{ $patient['medical_record_number'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $patient['nik'] ?? '-' }}</td>
                            <td></td>
                            <td class="lbl">Tgl Lahir</td><td class="sep">:</td><td class="val">{{ $patient['date_of_birth'] ?? '-' }}@if(!empty($patient['age'])) ({{ $patient['age'] }} th)@endif</td>
                        </tr>
                        <tr>
                            <td class="lbl">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ $patient['gender'] ?? '-' }}</td>
                            <td></td>
                            <td class="lbl">Gol. Darah</td><td class="sep">:</td><td class="val">{{ $patient['blood_type'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Dokter</td><td class="sep">:</td><td class="val" colspan="5">{{ $practitioner['name'] ?? '-' }} ({{ $practitioner['profession'] ?? '-' }})@if(!empty($practitioner['sip_number'])) - SIP: {{ $practitioner['sip_number'] }}@endif</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- SOAP + PAIN SCALE --}}
    <div class="section-caption">Catatan Klinis (SOAP)</div>
    <table class="soap-table">
        <thead>
            <tr>
                <th>S - SUBJEKTIF</th>
                <th>O - OBJEKTIF</th>
                <th>A - ASSESSMENT</th>
                <th>P - PLAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $soap['subjective'] ?? '-' }}</td>
                <td>{{ $soap['objective'] ?? '-' }}</td>
                <td>{{ $soap['assessment'] ?? '-' }}</td>
                <td>{{ $soap['plan'] ?? '-' }}</td>
            </tr>
            @if(!empty($pain_scale))
            <tr>
                <td colspan="4" style="background: #f7fafc;">
                    <strong style="color: #2c5282;">Skala Nyeri:</strong>
                    <span style="font-weight: bold; color: #1a202c;">{{ $pain_scale['value'] ?? '-' }}</span>
                    @if(!empty($pain_scale['interpretation']))
                        <span style="color: #718096;">({{ $pain_scale['interpretation'] }})</span>
                    @endif
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ROW 1: Vital Signs | Anthropometry | Symptoms --}}
    <table class="rpt-grid">
        <tr>
            <td>
                <div class="section-caption">Tanda Vital</div>
                @if(!empty($vital_signs))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Nilai</th>
                                <th>Satuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vital_signs as $v)
                                <tr>
                                    <td>{{ $v['label'] ?? '-' }}</td>
                                    <td class="col-value">{{ $v['value'] ?? '-' }}</td>
                                    <td class="col-muted">{{ $v['unit'] ?? '' }}</td>
                                    <td>{{ $v['status'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted">Tidak ada data</div>
                @endif
            </td>
            <td>
                <div class="section-caption">Antropometri</div>
                @if(!empty($anthropometry))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Nilai</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anthropometry as $a)
                                <tr>
                                    <td>{{ $a['label'] ?? '-' }}</td>
                                    <td class="col-value">{{ $a['value'] ?? '-' }}</td>
                                    <td class="col-muted">{{ $a['unit'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted">Tidak ada data</div>
                @endif
            </td>
            <td>
                <div class="section-caption">Keluhan</div>
                @if(!empty($symptoms))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Keluhan</th>
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
            </td>
        </tr>
    </table>

    {{-- ROW 2: Allergies | Medical History | Diagnoses --}}
    <table class="rpt-grid">
        <tr>
            <td>
                <div class="section-caption">Alergi</div>
                @if(!empty($allergies))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>Nama</th>
                                <th>Tingkat</th>
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
            </td>
            <td>
                <div class="section-caption">Riwayat Penyakit</div>
                @if(!empty($history_illnesses) || !empty($family_illnesses))
                    @if(!empty($history_illnesses))
                        <div class="sub-label">Pribadi</div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Penyakit</th>
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
                        <div class="sub-label">Keluarga</div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Penyakit</th>
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
            </td>
            <td>
                <div class="section-caption">Diagnosa</div>
                @if(!empty($diagnoses))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>Kode</th>
                                <th>Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnoses as $d)
                                <tr>
                                    <td>{{ $d['type_label'] ?? '-' }}</td>
                                    <td class="col-value">{{ $d['code'] ?? '-' }}</td>
                                    <td>{{ $d['name'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted">Tidak ada data</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ROW 3: Prescriptions | Treatments --}}
    <table class="rpt-grid-2">
        <tr>
            <td>
                <div class="section-caption">Resep Obat</div>
                @if(!empty($prescriptions))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Frekuensi</th>
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
            </td>
            <td>
                <div class="section-caption">Tindakan Klinis</div>
                @if(!empty($treatments))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Tindakan</th>
                                <th>Qty</th>
                                <th>Hasil</th>
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
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="rpt-footer">
        <div class="footer-note">
            Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} &bull; Dokumen resmi WELLMED
        </div>
    </div>

</div>
</body>
</html>
