<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EMR - {{ $data['visit_registration']['visit_registration_code'] ?? 'N/A' }}</title>
    <style type="text/css">
        @page {
            size: letter;
            margin: 22mm 20mm 20mm 20mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2e3c52;
            margin: 5px 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── FIXED PAGE HEADER / FOOTER ──────────────────────────── */
        .phdr {
            position: fixed;
            top: -17mm; left: 0; right: 0;
            height: 10mm;
            border-bottom: 0.5pt solid #dde3ec;
            padding-bottom: 2pt;
        }
        .phdr table { width: 100%; border-collapse: collapse; }
        .phdr td    { font-size: 7px; color: #9aa5b4; vertical-align: bottom; padding: 0; }

        .pftr {
            position: fixed;
            bottom: -12mm; left: 0; right: 0;
            height: 9mm;
            border-top: 0.5pt solid #dde3ec;
            padding-top: 2pt;
        }
        .pftr table { width: 100%; border-collapse: collapse; }
        .pftr td    { font-size: 7px; color: #9aa5b4; padding: 0; }

        /* ── DOCUMENT HEADER ──────────────────────────────────────── */
        .doc-header    { background-color: #053046; padding: 18px 20px 14px; }
        .dh-clinic     { font-size: 17px; font-weight: 700; color: #eae1b7; letter-spacing: 0.1px; }
        .dh-sub        { font-size: 9px; color: #9db8c4; text-transform: uppercase; letter-spacing: 0.14em; margin-top: 2px; }
        .dh-rm-lbl     { font-size: 8px; color: #9db8c4; letter-spacing: 0.13em; text-transform: uppercase; }
        .dh-rm-num     { font-size: 13px; font-weight: 700; color: #eae1b7; letter-spacing: 0.06em; margin-top: 1px; }
        .dh-rm-date    { font-size: 9.5px; color: #9db8c4; margin-top: 2px; }
        .dh-divider    { height: 1px; background-color: #1a5470; margin: 10px 0; }
        .dh-pf-lbl     { font-size: 8px; color: #9db8c4; text-transform: uppercase; letter-spacing: 0.13em; margin-bottom: 2px; }
        .dh-pf-val     { font-size: 11.5px; color: #d4c99a; font-weight: 600; }
        .dh-pf-val-lg  { font-size: 15px; color: #eae1b7; font-weight: 700; }

        /* ── ALERT BAR ────────────────────────────────────────────── */
        .alert-bar {
            background-color: #fdf0f0;
            border-bottom: 1px solid #f5c5c5;
            padding: 6px 20px;
            font-size: 10px;
            color: #d04040;
            font-weight: 600;
        }

        /* ── DOCTOR STRIP ─────────────────────────────────────────── */
        .doc-strip     { background-color: #eaf3f6; border-bottom: 1px solid #b8d4e0; padding: 7px 20px; }
        .ds-lbl        { font-size: 8px; color: #7a9aaa; text-transform: uppercase; letter-spacing: 0.12em; }
        .ds-name       { font-size: 11.5px; color: #053046; font-weight: 600; }
        .svc-badge     { display: inline-block; font-size: 8.5px; letter-spacing: 0.1em; text-transform: uppercase; background-color: #b8d4e0; color: #053046; padding: 3px 10px; border-radius: 20px; }

        /* ── BODY WRAPPER ─────────────────────────────────────────── */
        .body { padding: 0 0 20px; }

        /* ── SECTION ──────────────────────────────────────────────── */
        .section { margin-top: 16px; }

        /* Section header backgrounds — all derived from #053046 */
        .sec-hdr-s { background-color: #eaf3f6; padding: 5px 10px; border-radius: 4px; margin-bottom: 9px; }
        .sec-hdr-o { background-color: #e8f1f5; padding: 5px 10px; border-radius: 4px; margin-bottom: 9px; }
        .sec-hdr-a { background-color: #eaf4f1; padding: 5px 10px; border-radius: 4px; margin-bottom: 9px; }
        .sec-hdr-p { background-color: #eff5ea; padding: 5px 10px; border-radius: 4px; margin-bottom: 9px; }

        .sec-badge {
            display: inline-block;
            width: 22px; height: 22px;
            border-radius: 11px;
            text-align: center;
            line-height: 18px;
            font-size: 11px; font-weight: 700;
            margin-right: 6px;
            vertical-align: middle;
        }
        /* S/O/A/P badges — navy variants, gold text */
        .sb-s { background-color: #053046; color: #eae1b7; }
        .sb-o { background-color: #0b4f72; color: #eae1b7; }
        .sb-a { background-color: #0a5040; color: #eae1b7; }
        .sb-p { background-color: #3a5010; color: #eae1b7; }

        .sec-title { font-size: 12px; font-weight: 700; color: #053046; vertical-align: middle; }
        .sec-sub   { font-size: 8px; color: #7a9aaa; text-transform: uppercase; letter-spacing: 0.1em; }

        /* ── CARDS ────────────────────────────────────────────────── */
        .scard         { background-color: #f7f9fc; border: 1px solid #dde3ec; border-radius: 4px; padding: 9px 13px; margin-bottom: 8px; }
        .scard-title   { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.13em; margin-bottom: 6px; }
        .scard-title-r { font-size: 8px; color: #d04040; text-transform: uppercase; letter-spacing: 0.13em; margin-bottom: 6px; }

        /* ── SOAP NOTE TEXT ───────────────────────────────────────── */
        .soap-note       { background-color: #fff; border: 1px solid #dde3ec; border-radius: 4px; padding: 9px 13px; margin-bottom: 8px; font-size: 11px; color: #374151; }
        .soap-note-title { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.13em; margin-bottom: 5px; }

        /* ── LABEL (small uppercase) ──────────────────────────────── */
        .lbl { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 6px; margin-top: 4px; }

        /* ── SYMPTOMS — pill tags ─────────────────────────────────── */
        .sym-tag {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #dde3ec;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            margin: 2px 2px;
        }

        /* ── ALLERGY BLOCKS ───────────────────────────────────────── */
        .al-block       { border-left: 3px solid #d04040; background-color: #fdf0f0; border-radius: 0 4px 4px 0; padding: 7px 11px; margin-bottom: 7px; }
        .al-block-amber { border-left: 3px solid #c47a20; background-color: #fef6ec; border-radius: 0 4px 4px 0; padding: 7px 11px; margin-bottom: 7px; }
        .al-sev-c { display: inline-block; font-size: 8px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 20px; background-color: #d04040; color: #fff; margin-right: 7px; vertical-align: middle; }
        .al-sev-h { display: inline-block; font-size: 8px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 20px; background-color: #c47a20; color: #fff; margin-right: 7px; vertical-align: middle; }
        .al-name  { font-size: 11px; font-weight: 600; color: #2e3c52; vertical-align: middle; }
        .al-lbl   { font-size: 9.5px; color: #9aa5b4; }
        .al-val   { font-size: 10.5px; color: #2e3c52; }

        /* ── HISTORY ITEMS ────────────────────────────────────────── */
        .hist-item { padding: 4px 0; border-bottom: 1px solid #dde3ec; }
        .hist-item:last-child { border-bottom: none; padding-bottom: 0; }
        .hdot     { display: inline-block; width: 5px; height: 5px; border-radius: 3px; background-color: #9aa5b4; vertical-align: middle; margin-right: 7px; }
        .htxt     { display: inline; font-size: 11px; color: #2e3c52; }
        .hperson  { font-size: 9.5px; color: #9aa5b4; padding-left: 12px; }

        /* ── VITALS GRID ──────────────────────────────────────────── */
        .vitals-tbl { width: 100%; border-collapse: separate; border-spacing: 1px; background-color: #dde3ec; border-radius: 4px; margin-bottom: 8px; }
        .vc         { background-color: #ffffff; padding: 9px 13px; vertical-align: top; width: 33.33%; }
        .vc-lbl     { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 3px; }
        .vc-val     { font-size: 17px; color: #2e3c52; line-height: 1.1; }
        .vc-unit    { font-size: 9px; color: #9aa5b4; }
        .vc-badge   { display: inline-block; margin-top: 4px; font-size: 7.5px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 20px; }
        .vb-danger  { background-color: #fdf0f0; color: #d04040; }
        .vb-ok      { background-color: #e6f5f3; color: #2a8a7a; }
        .vb-warn    { background-color: #fef6ec; color: #c47a20; }

        /* ── PAIN SCALE ───────────────────────────────────────────── */
        .pain-bar-tbl  { width: 100%; border-collapse: separate; border-spacing: 2px; margin: 4px 0 5px; }
        .pain-bar-tbl td { height: 7px; border-radius: 2px; }
        .pain-num      { font-size: 22px; font-weight: 700; color: #c47a20; line-height: 1; }
        .pain-den      { font-size: 11px; color: #9aa5b4; }
        .pain-cat      { font-size: 10px; color: #c47a20; font-weight: 600; }

        /* ── ANTHROPOMETRY GRID ───────────────────────────────────── */
        .anthro-tbl { width: 100%; border-collapse: separate; border-spacing: 1px; background-color: #dde3ec; border-radius: 4px; margin-bottom: 8px; }
        .ac         { background-color: #ffffff; padding: 8px 13px; vertical-align: top; width: 33.33%; }
        .ac-lbl     { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
        .ac-val     { font-size: 15px; color: #2e3c52; }
        .ac-unit    { font-size: 9px; color: #9aa5b4; }
        .ac-interp  { display: inline-block; margin-top: 3px; font-size: 7.5px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 20px; background-color: #fef6ec; color: #c47a20; }

        /* ── PHYSICAL EXAMINATION ─────────────────────────────────── */
        .phys-sub-hd { font-size: 9px; font-weight: 700; color: #053046; margin-bottom: 5px; padding-bottom: 3px; border-bottom: 0.5pt solid #b8d4e0; }
        .exam-tbl    { width: 100%; border-collapse: collapse; }
        .exam-tbl tr { border-bottom: 1px solid #dde3ec; }
        .exam-tbl tr:last-child { border-bottom: none; }
        .exam-tbl td { padding: 6px 10px; font-size: 10.5px; background-color: #fff; }
        .exam-tbl td:first-child { width: 42%; color: #5a6a80; background-color: #f7f9fc; }
        .edot        { display: inline-block; width: 5px; height: 5px; border-radius: 3px; background-color: #d04040; margin-right: 5px; vertical-align: middle; }
        .phys-img-container { position: relative; display: block; width: 100%; }
        .phys-img    { width: 100%; height: auto; max-height: 185px; border: 1px solid #dde3ec; border-radius: 3px; display: block; object-fit: contain; }
        .phys-svg-overlay { position: absolute; top: 1px; left: 1px; right: 1px; bottom: 1px; pointer-events: none; }
        .anno-cond   { font-size: 10px; color: #5a6a80; font-style: italic; margin-top: 1px; padding-left: 12px; }

        /* ── DIAGNOSES ────────────────────────────────────────────── */
        .diag-item { padding: 7px 11px; background-color: #fff; border: 1px solid #dde3ec; border-radius: 4px; margin-bottom: 5px; }
        .diag-item:last-child { margin-bottom: 0; }
        .dtype     { display: inline-block; font-size: 8px; font-weight: 700; text-transform: uppercase; padding: 2px 9px; border-radius: 20px; min-width: 60px; text-align: center; margin-right: 7px; vertical-align: middle; }
        .dt-p      { background-color: #053046; color: #eae1b7; }
        .dt-a      { background-color: #eaf3f6; color: #053046; border: 1px solid #b8d4e0; }
        .dt-s      { background-color: #f5f7f8; color: #5a6a7a; border: 1px solid #c8d8e0; }
        .dcode     { font-size: 10.5px; color: #053046; font-weight: 700; margin-right: 5px; vertical-align: middle; }
        .dname     { font-size: 11px; vertical-align: middle; }

        /* ── TREATMENTS ───────────────────────────────────────────── */
        .tindakan-row  { padding: 7px 11px; background-color: #fff; border: 1px solid #dde3ec; border-radius: 4px; margin-bottom: 5px; }
        .tindakan-row:last-child { margin-bottom: 0; }
        .tindakan-name { font-size: 11.5px; font-weight: 600; color: #2e3c52; }
        .tindakan-ct   { font-size: 9.5px; color: #9aa5b4; margin-left: 5px; }
        .tx-res        { display: inline-block; font-size: 8px; font-weight: 700; background-color: #e6f5f3; color: #2a8a7a; padding: 2px 8px; border-radius: 20px; float: right; }

        /* ── PRESCRIPTIONS ────────────────────────────────────────── */
        .rx-card   { border: 1px solid #b8d4e0; border-radius: 4px; margin-bottom: 8px; }
        .rx-card:last-child { margin-bottom: 0; }
        .rx-hdr    { background-color: #eaf3f6; padding: 7px 14px; border-bottom: 1px solid #b8d4e0; }
        .rx-sym    { font-size: 14px; font-weight: 700; font-style: italic; color: #053046; margin-right: 7px; vertical-align: middle; }
        .rx-drug   { font-size: 12px; font-weight: 600; color: #053046; vertical-align: middle; }
        .rx-body   { padding: 10px 14px; background-color: #fff; }
        .rxf-lbl   { font-size: 8px; color: #9aa5b4; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
        .rxf-val   { font-size: 11px; font-weight: 600; color: #2e3c52; }

        /* ── FOOTER / SIGNATURE ───────────────────────────────────── */
        .footer      { margin-top: 18px; padding-top: 12px; border-top: 1px solid #dde3ec; }
        .footer-note { font-size: 9px; color: #9aa5b4; line-height: 1.8; }
        .sig-block   { text-align: center; }
        .sig-date    { font-size: 10px; color: #9aa5b4; margin-bottom: 7px; }
        .sig-line    { height: 1px; background-color: #dde3ec; margin-bottom: 5px; }
        .sig-name    { font-size: 12px; font-weight: 700; color: #2e3c52; }
        .sig-title   { font-size: 9px; color: #9aa5b4; }

        /* ── UTILITIES ────────────────────────────────────────────── */
        .tl { text-align: left; }
        .tc { text-align: center; }
        .tr { text-align: right; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<!-- Fixed Page Header -->
<div class="phdr">
    <table>
        <tr>
            <td class="tl" style="color:#053046; font-weight:700;">{{ $data['workspace']['name'] ?? 'KLINIK' }}</td>
            <td class="tc">REKAM MEDIS ELEKTRONIK</td>
            <td class="tr">No: <strong style="color:#2e3c52;">{{ $data['visit_registration']['visit_registration_code'] ?? '-' }}</strong></td>
        </tr>
    </table>
</div>

<!-- Fixed Page Footer -->
<div class="pftr">
    <table>
        <tr>
            <td class="tl">{{ $data['visit_registration']['visit_registration_code'] ?? '' }}</td>
            <td class="tc">Dokumen resmi &mdash; sah tanpa tanda tangan basah</td>
            <td class="tr">Dicetak: {{ now()->format('d M Y H:i') }}</td>
        </tr>
    </table>
</div>

<div>

    <!-- ============================================================
         DOCUMENT HEADER — blue gradient
         ============================================================ -->
    <div class="doc-header">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                @if(isset($data['workspace']['logo']) && $data['workspace']['logo'])
                <td style="width:46px; vertical-align:middle; padding:0; padding-right:10px;">
                    <img src="{{ $data['workspace']['logo'] }}" alt="Logo" style="width:38px; height:38px; border-radius:4px;">
                </td>
                @endif
                <td style="vertical-align:top; padding:0;">
                    <div class="dh-clinic">{{ $data['workspace']['name'] ?? 'KLINIK KESEHATAN' }}</div>
                    <div class="dh-sub">Rekam Medis Elektronik</div>
                </td>
                <td style="text-align:right; vertical-align:top; padding:0;">
                    <div class="dh-rm-lbl">No. Rekam Medis</div>
                    <div class="dh-rm-num">{{ $data['visit_registration']['visit_registration_code'] ?? '-' }}</div>
                    <div class="dh-rm-date">{{ $data['visit_registration']['visit_date'] ?? now()->format('d F Y') }}</div>
                </td>
            </tr>
        </table>
        <div class="dh-divider"></div>
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:40%; vertical-align:top; padding:0;">
                    @php
                        try {
                            $dob    = \Carbon\Carbon::parse($data['patient']['date_of_birth'] ?? null);
                            $diff   = $dob->diff(now());
                            $ageStr = $diff->y . '.' . $diff->m;
                        } catch (\Throwable $e) {
                            $ageStr = null;
                        }
                    @endphp
                    <div class="dh-pf-lbl">Nama Pasien</div>
                    <div class="dh-pf-val-lg">
                        {{ $data['patient']['name'] ?? '-' }}
                        @if($ageStr)
                        <span style="font-size:10px; font-weight:400; color:#9db8c4; margin-left:6px; vertical-align:middle;">{{ $ageStr }} thn</span>
                        @endif
                    </div>
                </td>
                <td style="width:25%; vertical-align:top; padding:0;">
                    <div class="dh-pf-lbl">Tanggal Lahir</div>
                    <div class="dh-pf-val">{{ $data['patient']['date_of_birth'] ?? '-' }}@if(isset($data['patient']['age']) && $data['patient']['age']) ({{ $data['patient']['age'] }} Thn)@endif</div>
                </td>
                <td style="width:20%; vertical-align:top; padding:0;">
                    <div class="dh-pf-lbl">Jenis Kelamin</div>
                    <div class="dh-pf-val">{{ $data['patient']['gender'] ?? '-' }}</div>
                </td>
                <td style="width:15%; vertical-align:top; padding:0;">
                    <div class="dh-pf-lbl">Layanan</div>
                    <div class="dh-pf-val">{{ $data['visit_registration']['medic_service']['name'] ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ALLERGY ALERT BAR (conditional) -->
    @if(!empty($data['soap']['subjectives']['forms']['Allergy']) && count($data['soap']['subjectives']['forms']['Allergy']) > 0)
    <div class="alert-bar">&#9888; Peringatan Alergi &mdash; pasien memiliki riwayat alergi obat, lihat detail di bawah</div>
    @endif

    <!-- DOCTOR STRIP -->
    <div class="doc-strip">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:middle; padding:0;">
                    <div class="ds-lbl">Dokter Penanggung Jawab (DPJP)</div>
                    <div class="ds-name">{{ $data['practitioner']['name'] ?? '-' }}</div>
                </td>
                <td style="text-align:right; vertical-align:middle; padding:0;">
                    <span class="svc-badge">{{ $data['practitioner']['profession'] ?? 'Dokter' }}</span>
                </td>
            </tr>
        </table>
    </div>

    @php
        $soap         = $data['soap'] ?? [];
        $subjectForms = $soap['subjectives']['forms'] ?? [];
        $objectForms  = $soap['objectives']['forms'] ?? [];
        $assessForms  = $soap['assessments']['forms'] ?? [];
        $planForms    = $soap['plans']['forms'] ?? [];
    @endphp

    <div class="body">

    <!-- ============================================================
         S — SUBJECTIVE
         ============================================================ -->
    <div class="section">
        <div class="sec-hdr-s">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle; padding:0;">
                        <span class="sec-badge sb-s">S</span>
                        <span class="sec-title">Subjective</span>
                    </td>
                    <td style="text-align:right; vertical-align:middle; padding:0;">
                        <span class="sec-sub">Anamnesis &amp; Keluhan</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- SubjectNote --}}
        @if(isset($subjectForms['SubjectNote']) && !empty($subjectForms['SubjectNote']['exam']['note']))
        <div class="soap-note">
            <div class="soap-note-title">Catatan Subjektif</div>
            {{ $subjectForms['SubjectNote']['exam']['note'] }}
        </div>
        @endif

        {{-- Symptoms --}}
        @if(isset($subjectForms['Symptom']) && is_array($subjectForms['Symptom']) && count($subjectForms['Symptom']) > 0)
        <div class="scard">
            <div class="scard-title">Keluhan / Gejala</div>
            @foreach($subjectForms['Symptom'] as $symptom)
            <span class="sym-tag">{{ $symptom['exam']['name'] ?? '-' }}</span>
            @endforeach
        </div>
        @endif

        {{-- Allergies --}}
        @if(isset($subjectForms['Allergy']) && is_array($subjectForms['Allergy']) && count($subjectForms['Allergy']) > 0)
        <div class="scard">
            <div class="scard-title-r">&#9888; Riwayat Alergi</div>
            @foreach($subjectForms['Allergy'] as $allergy)
            @php
                $ae = $allergy['exam'] ?? [];
                $sevScale = intval($ae['allergy_scale'] ?? 0);
                if ($sevScale >= 4) {
                    $alBlockClass = 'al-block';
                    $alSevClass   = 'al-sev-c';
                } else {
                    $alBlockClass = 'al-block-amber';
                    $alSevClass   = 'al-sev-h';
                }
            @endphp
            <div class="{{ $alBlockClass }}">
                <div style="margin-bottom:5px;">
                    <span class="{{ $alSevClass }}">{{ $ae['allergy_scale_spell'] ?? ($ae['allergy_scale'] ?? '-') }}</span>
                    <span class="al-name">{{ $ae['name'] ?? '-' }}</span>
                </div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:50%; padding:0; vertical-align:top;">
                            <div class="al-lbl">Tipe</div>
                            <div class="al-val">{{ $ae['allergy_type']['name'] ?? '-' }}</div>
                        </td>
                        @if(!empty($ae['effects']))
                        <td style="width:50%; padding:0; vertical-align:top;">
                            <div class="al-lbl">Efek</div>
                            <div class="al-val">{{ implode(', ', $ae['effects']) }}</div>
                        </td>
                        @elseif(!empty($ae['allergen']))
                        <td style="width:50%; padding:0; vertical-align:top;">
                            <div class="al-lbl">Alergen</div>
                            <div class="al-val">{{ $ae['allergen'] }}</div>
                        </td>
                        @endif
                    </tr>
                </table>
            </div>
            @endforeach
        </div>
        @endif

        {{-- PatientFamilyIllness — personal history vs family --}}
        @if(isset($subjectForms['PatientFamilyIllness']) && is_array($subjectForms['PatientFamilyIllness']) && count($subjectForms['PatientFamilyIllness']) > 0)
        @php
            $histItems   = array_values(array_filter($subjectForms['PatientFamilyIllness'], fn($f) => ($f['exam']['type'] ?? '') === 'HistoryIllness'));
            $familyItems = array_values(array_filter($subjectForms['PatientFamilyIllness'], fn($f) => ($f['exam']['type'] ?? '') === 'FamilyIllness'));
        @endphp
        @if(count($histItems) > 0 || count($familyItems) > 0)
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <tr>
                @if(count($histItems) > 0)
                <td style="vertical-align:top; padding:0; {{ count($familyItems) > 0 ? 'padding-right:4px;' : '' }}">
                    <div class="scard">
                        <div class="scard-title">Riwayat Penyakit Pribadi</div>
                        @foreach($histItems as $h)
                        <div class="hist-item">
                            <span class="hdot"></span>
                            <span class="htxt">{{ $h['exam']['name'] ?? '-' }}</span>
                        </div>
                        @endforeach
                    </div>
                </td>
                @endif
                @if(count($familyItems) > 0)
                <td style="vertical-align:top; padding:0; {{ count($histItems) > 0 ? 'padding-left:4px;' : '' }}">
                    <div class="scard">
                        <div class="scard-title">Riwayat Penyakit Keluarga</div>
                        @foreach($familyItems as $f)
                        <div class="hist-item">
                            <span class="hdot"></span>
                            <div style="display:inline-block; vertical-align:middle;">
                                <div class="htxt">{{ $f['exam']['name'] ?? '-' }}</div>
                                @if(!empty($f['exam']['family_name']))
                                <div class="hperson">{{ $f['exam']['family_name'] }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </td>
                @endif
            </tr>
        </table>
        @endif
        @endif

    </div>{{-- /section S --}}

    <!-- ============================================================
         O — OBJECTIVE
         ============================================================ -->
    <div class="section">
        <div class="sec-hdr-o">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle; padding:0;">
                        <span class="sec-badge sb-o">O</span>
                        <span class="sec-title">Objective</span>
                    </td>
                    <td style="text-align:right; vertical-align:middle; padding:0;">
                        <span class="sec-sub">Pemeriksaan Fisik &amp; Vital</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ObjectNote --}}
        @if(isset($objectForms['ObjectNote']) && !empty($objectForms['ObjectNote']['exam']['note']))
        <div class="soap-note">
            <div class="soap-note-title">Catatan Objektif</div>
            {{ $objectForms['ObjectNote']['exam']['note'] }}
        </div>
        @endif

        {{-- VitalSign --}}
        @if(isset($objectForms['VitalSign']) && !empty($objectForms['VitalSign']['exam']))
        @php $vs = $objectForms['VitalSign']['exam']; @endphp
        <div class="lbl">Tanda Vital</div>
        <table class="vitals-tbl">
            <tr>
                @if(isset($vs['systolic']) || isset($vs['diastolic']))
                <td class="vc">
                    <div class="vc-lbl">Tekanan Darah</div>
                    <div class="vc-val">{{ $vs['systolic'] ?? '-' }}<span class="vc-unit">/</span>{{ $vs['diastolic'] ?? '-' }} <span class="vc-unit">mmHg</span></div>
                    @if(!empty($vs['blood_pressure_status']))
                    @php
                        $bpStatus = $vs['blood_pressure_status'];
                        $bpClass  = $bpStatus === 'NORMAL' ? 'vb-ok' : (in_array($bpStatus, ['ELEVATED', 'PRE_HYPERTENSION']) ? 'vb-warn' : 'vb-danger');
                        $bpLabel  = ucwords(strtolower(str_replace('_', ' ', $bpStatus)));
                    @endphp
                    <div><span class="vc-badge {{ $bpClass }}">{{ $bpLabel }}</span></div>
                    @endif
                </td>
                @endif
                @if(isset($vs['pulse_rate']))
                <td class="vc">
                    <div class="vc-lbl">Nadi</div>
                    <div class="vc-val">{{ $vs['pulse_rate'] }} <span class="vc-unit">bpm</span></div>
                </td>
                @endif
                @if(isset($vs['temperature']))
                <td class="vc">
                    <div class="vc-lbl">Suhu @if(!empty($vs['temperature_type']))({{ $vs['temperature_type'] }})@endif</div>
                    <div class="vc-val">{{ $vs['temperature'] }} <span class="vc-unit">&deg;C</span></div>
                    @if(!empty($vs['temperature_status']))
                    @php $tClass = $vs['temperature_status'] === 'NORMAL' ? 'vb-ok' : 'vb-warn'; @endphp
                    <div><span class="vc-badge {{ $tClass }}">{{ $vs['temperature_status'] }}</span></div>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                @if(isset($vs['respiration_rate']))
                <td class="vc">
                    <div class="vc-lbl">Pernapasan</div>
                    <div class="vc-val">{{ $vs['respiration_rate'] }} <span class="vc-unit">/mnt</span></div>
                </td>
                @endif
                @if(isset($vs['oxygen_saturation']))
                <td class="vc">
                    <div class="vc-lbl">Saturasi O&#x2082;</div>
                    <div class="vc-val">{{ $vs['oxygen_saturation'] }} <span class="vc-unit">%</span></div>
                    @if(!empty($vs['oxygen_status']))
                    @php $oClass = $vs['oxygen_status'] === 'NORMAL' ? 'vb-ok' : 'vb-danger'; @endphp
                    <div><span class="vc-badge {{ $oClass }}">{{ $vs['oxygen_status'] }}</span></div>
                    @endif
                </td>
                @endif
                @if(!empty($vs['loc']['name']))
                <td class="vc">
                    <div class="vc-lbl">Kesadaran</div>
                    <div style="font-size:12px; font-weight:600; color:#2e3c52; margin-top:5px;">{{ $vs['loc']['name'] }}</div>
                </td>
                @endif
            </tr>
        </table>
        @endif

        {{-- PainScale --}}
        @if(isset($objectForms['PainScale']) && !empty($objectForms['PainScale']['exam']))
        @php $ps = $objectForms['PainScale']['exam']; $pVal = intval($ps['rating_scale'] ?? 0); @endphp
        <div class="scard">
            <div class="scard-title">Skala Nyeri</div>
            <table class="pain-bar-tbl">
                <tr>
                    @for($i = 1; $i <= 10; $i++)
                    @php
                        if ($i <= $pVal) {
                            $pColor = $i <= 3 ? '#2a8a7a' : ($i <= 6 ? '#c47a20' : '#d04040');
                        } else {
                            $pColor = '#dde3ec';
                        }
                    @endphp
                    <td style="background-color:{{ $pColor }}; width:10%;"></td>
                    @endfor
                    <td style="width:44px; text-align:right; padding-left:4px; vertical-align:middle;">
                        <span class="pain-num">{{ $pVal }}</span><span class="pain-den"> / 10</span>
                    </td>
                </tr>
            </table>
            <div class="pain-cat">{{ $ps['scale_result'] ?? '-' }}</div>
        </div>
        @endif

        {{-- Anthropometry --}}
        @if(isset($objectForms['Anthropometry']) && !empty($objectForms['Anthropometry']['exam']))
        @php
            $anth = $objectForms['Anthropometry']['exam'];
            $anthItems = [];
            if (isset($anth['weight']))              $anthItems[] = ['label' => 'Berat Badan',  'value' => $anth['weight'],             'unit' => 'kg', 'interp' => null];
            if (isset($anth['height']))              $anthItems[] = ['label' => 'Tinggi Badan', 'value' => $anth['height'],             'unit' => 'cm', 'interp' => null];
            if (isset($anth['bmi']))                 $anthItems[] = ['label' => 'BMI',          'value' => $anth['bmi'],                'unit' => '',   'interp' => $anth['bmi_category'] ?? null];
            if (isset($anth['waist_circumference'])) $anthItems[] = ['label' => 'L. Pinggang',  'value' => $anth['waist_circumference'], 'unit' => 'cm', 'interp' => null];
            if (isset($anth['hip_circumference']))   $anthItems[] = ['label' => 'L. Pinggul',   'value' => $anth['hip_circumference'],   'unit' => 'cm', 'interp' => null];
            if (isset($anth['whr']))                 $anthItems[] = ['label' => 'WHR',           'value' => $anth['whr'],                'unit' => '',   'interp' => $anth['whr_risk'] ?? null];
            if (isset($anth['ideal_weight']))        $anthItems[] = ['label' => 'BB Ideal',      'value' => $anth['ideal_weight'],       'unit' => 'kg', 'interp' => null];
            $aChunks = array_chunk($anthItems, 3);
        @endphp
        <div class="lbl" style="margin-top:2px;">Antropometri</div>
        <table class="anthro-tbl">
            @foreach($aChunks as $aRow)
            <tr>
                @foreach($aRow as $a)
                <td class="ac">
                    <div class="ac-lbl">{{ $a['label'] }}</div>
                    <div class="ac-val">{{ $a['value'] }} <span class="ac-unit">{{ $a['unit'] }}</span></div>
                    @if(!empty($a['interp']))<div><span class="ac-interp">{{ $a['interp'] }}</span></div>@endif
                </td>
                @endforeach
                @php for ($p = 0; $p < (3 - count($aRow)); $p++) echo '<td class="ac"></td>'; @endphp
            </tr>
            @endforeach
        </table>
        @endif

        {{-- PhysicalExamination --}}
        @if(isset($objectForms['PhysicalExamination']) && !empty($objectForms['PhysicalExamination']['exam']))
        @php
            $pe = $objectForms['PhysicalExamination']['exam'];
            $physExams = [];
            foreach (['body_form', 'muscle_form', 'odontogram'] as $formKey) {
                if (!empty($pe[$formKey]) && !empty($pe[$formKey]['data'])) {
                    $physExams[] = [
                        'type'      => $formKey,
                        'label'     => $pe[$formKey]['label'] ?? $formKey,
                        'image_url' => $pe[$formKey]['asset_url'] ?? '',
                        'data'      => $pe[$formKey]['data'],
                    ];
                }
            }
        @endphp
        @if(count($physExams) > 0)
        <div class="lbl" style="margin-top:2px;">Pemeriksaan Fisik</div>
        @foreach(array_chunk($physExams, 2) as $examPair)
        <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:8px;">
            <tr>
                @foreach($examPair as $examIdx => $exam)
                <td style="vertical-align:top; padding:0; {{ $examIdx === 0 && count($examPair) > 1 ? 'padding-right:4px;' : '' }}">
                    <div class="phys-sub-hd">{{ $exam['label'] }}</div>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            @if($exam['image_url'])
                            <td style="width:42%; vertical-align:top; padding:0; padding-right:8px;">
                                <div class="phys-img-container">
                                    <img src="{{ $exam['image_url'] }}" alt="{{ $exam['label'] }}" class="phys-img" id="phys-img-{{ $exam['type'] }}">
                                    @php
                                        // Calculate viewBox dimensions based on all coordinates
                                        $maxX = 100;
                                        $maxY = 100;
                                        foreach($exam['data'] as $anno) {
                                            if(!empty($anno['coodinates']) && is_array($anno['coodinates'])) {
                                                foreach($anno['coodinates'] as $coord) {
                                                    if(isset($coord['x'])) $maxX = max($maxX, $coord['x']);
                                                    if(isset($coord['y'])) $maxY = max($maxY, $coord['y']);
                                                }
                                            }
                                        }
                                        // Add 10% padding
                                        $viewBoxWidth = $maxX * 1.1;
                                        $viewBoxHeight = $maxY * 1.1;
                                    @endphp
                                    <svg class="phys-svg-overlay" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {{ $viewBoxWidth }} {{ $viewBoxHeight }}" preserveAspectRatio="xMidYMid meet">
                                        @foreach($exam['data'] as $annoIndex => $anno)
                                            @if(!empty($anno['coodinates']) && is_array($anno['coodinates']))
                                                @php
                                                    // Generate unique color for each annotation
                                                    $colors = ['#d04040', '#2a8a7a', '#c47a20', '#053046', '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b'];
                                                    $color = $colors[$annoIndex % count($colors)];
                                                    // Calculate radius based on viewBox size
                                                    $radius = max(3, $viewBoxWidth * 0.008);
                                                @endphp
                                                @foreach($anno['coodinates'] as $coord)
                                                    @if(isset($coord['x']) && isset($coord['y']))
                                                        <circle
                                                            cx="{{ $coord['x'] }}"
                                                            cy="{{ $coord['y'] }}"
                                                            r="{{ $radius }}"
                                                            fill="{{ $color }}"
                                                            stroke="#ffffff"
                                                            stroke-width="{{ max(1, $radius * 0.3) }}"
                                                            opacity="0.9"
                                                        />
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </svg>
                                </div>
                            </td>
                            @endif
                            <td style="vertical-align:top; padding:0;">
                                <div style="border:1px solid #dde3ec; border-radius:4px;">
                                    <table class="exam-tbl">
                                        @foreach($exam['data'] as $annoIndex => $anno)
                                        @php
                                            $colors = ['#d04040', '#2a8a7a', '#c47a20', '#053046', '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b'];
                                            $color = $colors[$annoIndex % count($colors)];
                                        @endphp
                                        <tr>
                                            <td>
                                                @if(!empty($anno['coodinates']))
                                                <span class="edot" style="background-color: {{ $color }};"></span>
                                                @endif
                                                {{ $anno['anatomy']['name'] ?? '-' }}
                                                @if(!empty($anno['coodinates']))
                                                <span style="font-size:9px; color:#9aa5b4;">({{ count($anno['coodinates']) }} titik)</span>
                                                @endif
                                            </td>
                                            <td>{{ $anno['condition'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                @endforeach
                @if(count($examPair) === 1)<td style="width:50%; padding:0;"></td>@endif
            </tr>
        </table>
        @endforeach
        @endif
        @endif

        {{-- BasicDiagnose --}}
        @if(isset($objectForms['BasicDiagnose']) && is_array($objectForms['BasicDiagnose']) && count($objectForms['BasicDiagnose']) > 0)
        <div class="lbl" style="margin-top:2px;">Diagnosa</div>
        @foreach($objectForms['BasicDiagnose'] as $dx)
        @php
            $dxe      = $dx['exam'] ?? [];
            $dxType   = $dxe['type'] ?? '';
            $dxClass  = match($dxType) {
                'PrimaryDiagnose'   => 'dt-p',
                'InitialDiagnose'   => 'dt-a',
                'SecondaryDiagnose' => 'dt-s',
                default             => 'dt-s',
            };
            $dxLabel  = match($dxType) {
                'PrimaryDiagnose'   => 'Primer',
                'InitialDiagnose'   => 'Awal',
                'SecondaryDiagnose' => 'Sekunder',
                default             => 'Dx',
            };
        @endphp
        <div class="diag-item">
            <span class="dtype {{ $dxClass }}">{{ $dxLabel }}</span>
            <span class="dcode">{{ $dxe['code'] ?? '-' }}</span>
            <span class="dname">{{ $dxe['name'] ?? '-' }}</span>
        </div>
        @endforeach
        @endif

    </div>{{-- /section O --}}

    <!-- ============================================================
         A — ASSESSMENT
         ============================================================ -->
    <div class="section">
        <div class="sec-hdr-a">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle; padding:0;">
                        <span class="sec-badge sb-a">A</span>
                        <span class="sec-title">Assessment</span>
                    </td>
                    <td style="text-align:right; vertical-align:middle; padding:0;">
                        <span class="sec-sub">Diagnosa &amp; Tindakan</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- AssessmentNote --}}
        @if(isset($assessForms['AssessmentNote']) && !empty($assessForms['AssessmentNote']['exam']['note']))
        <div class="soap-note">
            <div class="soap-note-title">Catatan Assessment</div>
            {{ $assessForms['AssessmentNote']['exam']['note'] }}
        </div>
        @endif

        {{-- ClinicalTreatment --}}
        @if(isset($assessForms['ClinicalTreatment']) && is_array($assessForms['ClinicalTreatment']) && count($assessForms['ClinicalTreatment']) > 0)
        <div class="lbl">Tindakan Klinis</div>
        @foreach($assessForms['ClinicalTreatment'] as $tx)
        @php $txe = $tx['exam'] ?? []; @endphp
        <div class="tindakan-row">
            @if(!empty($txe['result']) && $txe['result'] !== '-')
            <span class="tx-res">{{ $txe['result'] }}</span>
            @endif
            <span class="tindakan-name">{{ $txe['name'] ?? '-' }}</span>
            <span class="tindakan-ct">&times;{{ $txe['qty'] ?? '1' }}</span>
            @if(!empty($txe['note']) && $txe['note'] !== '-')
            <div style="font-size:10px; color:#5a6a80; font-style:italic; margin-top:2px;">{{ $txe['note'] }}</div>
            @endif
        </div>
        @endforeach
        @endif

    </div>{{-- /section A --}}

    <!-- ============================================================
         P — PLAN
         ============================================================ -->
    <div class="section">
        <div class="sec-hdr-p">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle; padding:0;">
                        <span class="sec-badge sb-p">P</span>
                        <span class="sec-title">Plan</span>
                    </td>
                    <td style="text-align:right; vertical-align:middle; padding:0;">
                        <span class="sec-sub">Rencana Terapi</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- PlanNote --}}
        @if(isset($planForms['PlanNote']) && !empty($planForms['PlanNote']['exam']['note']))
        <div class="soap-note">
            <div class="soap-note-title">Catatan Plan</div>
            {{ $planForms['PlanNote']['exam']['note'] }}
        </div>
        @endif

        {{-- BasicPrescription --}}
        @if(isset($planForms['BasicPrescription']) && is_array($planForms['BasicPrescription']) && count($planForms['BasicPrescription']) > 0)
        <div class="lbl">Resep</div>
        @foreach($planForms['BasicPrescription'] as $rx)
        @php $rxe = $rx['exam'] ?? []; @endphp
        <div class="rx-card">
            <div class="rx-hdr">
                <span class="rx-sym">R/</span>
                <span class="rx-drug">{{ $rxe['name'] ?? '-' }}</span>
            </div>
            <div class="rx-body">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:33.33%; vertical-align:top; padding:0; padding-right:8px;">
                            <div class="rxf-lbl">Jumlah</div>
                            <div class="rxf-val">{{ $rxe['qty'] ?? '-' }}</div>
                        </td>
                        <td style="width:33.33%; vertical-align:top; padding:0; padding-right:8px;">
                            <div class="rxf-lbl">Waktu Minum</div>
                            <div class="rxf-val">
                                @if(!empty($rxe['dosage_instruction']['divided_times']))
                                {{ implode(' · ', $rxe['dosage_instruction']['divided_times']) }}
                                @else
                                &mdash;
                                @endif
                            </div>
                        </td>
                        <td style="width:33.33%; vertical-align:top; padding:0;">
                            <div class="rxf-lbl">Konsumsi</div>
                            <div class="rxf-val">{{ $rxe['dosage_instruction']['consume_at']['value'] ?? '—' }}</div>
                        </td>
                    </tr>
                </table>
                @if(!empty($rxe['indication']) && $rxe['indication'] !== '-')
                <div style="margin-top:7px; font-size:9.5px; color:#5a6a80; background-color:#f7f9fc; padding:4px 8px; border-radius:3px;">
                    <span style="font-size:8px; color:#9aa5b4; text-transform:uppercase; letter-spacing:0.08em;">Indikasi:</span>
                    {{ \Illuminate\Support\Str::limit($rxe['indication'], 200) }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
        @endif

    </div>{{-- /section P --}}

    <!-- ============================================================
         FOOTER / SIGNATURE
         ============================================================ -->
    <div class="footer">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:55%; vertical-align:bottom; padding:0;">
                    <div class="footer-note">
                        Laporan ini dicetak secara elektronik dari sistem informasi medis.<br>
                        Dokumen resmi &mdash; berlaku tanpa tanda tangan basah.<br>
                        <span style="font-size:8px;">{{ $data['visit_registration']['visit_registration_code'] ?? '' }}</span>
                    </div>
                </td>
                <td style="width:45%; text-align:center; vertical-align:bottom; padding:0;">
                    <div class="sig-block">
                        <div class="sig-date">{{ $data['visit_registration']['visit_date'] ?? now()->format('d F Y') }}</div>
                        <div style="height:100px;"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $data['practitioner']['name'] ?? '-' }}</div>
                        <div class="sig-title">{{ $data['practitioner']['profession'] ?? 'Dokter' }}@if(isset($data['practitioner']['sip_number']) && $data['practitioner']['sip_number']) &nbsp;|&nbsp; SIP: {{ $data['practitioner']['sip_number'] }}@endif</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    </div>{{-- /.body --}}

</div>
</body>
</html>
