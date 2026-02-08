@extends('wellmed::app')

@section('title', 'Informed Consent')

@section('css')
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

@page {
    size: A4;
    margin: 100px 40px 100px 40px;
}

header {
    position: fixed;
    top: -80px;
    left: 0;
    right: 0;
    height: 70px;
    text-align: center;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

footer {
    position: fixed;
    bottom: -80px;
    left: 0;
    right: 0;
    height: 60px;
    text-align: center;
    font-size: 10px;
    border-top: 1px solid #ccc;
    padding-top: 10px;
}

.header-grid {
    width: 100%;
    display: table;
}

.header-grid > div {
    display: table-cell;
    vertical-align: middle;
}

.header-grid > div:first-child {
    text-align: left;
    width: 15%;
}

.header-grid > div:nth-child(2) {
    text-align: center;
    width: 70%;
}

.header-grid > div:last-child {
    text-align: right;
    width: 15%;
}

.clinic-name {
    font-size: 16px;
    font-weight: bold;
    margin: 0;
    color: #333;
}

.clinic-address {
    font-size: 10px;
    margin: 2px 0;
    color: #666;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.text-justify {
    text-align: justify;
}

thead { display: table-header-group; }
tfoot { display: table-footer-group; }

.document-title {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    text-decoration: underline;
    margin: 10px 0;
}

.document-number {
    text-align: center;
    font-size: 11px;
    margin-bottom: 15px;
}

.section-title {
    font-weight: bold;
    font-size: 11px;
    margin: 10px 0 5px 0;
    background-color: #f5f5f5;
    padding: 5px;
}

.info-table {
    width: 100%;
    margin-bottom: 10px;
}

.info-table td {
    padding: 3px 5px;
    font-size: 11px;
    vertical-align: top;
}

.info-table .label {
    width: 150px;
    font-weight: normal;
}

.info-table .separator {
    width: 10px;
}

.info-table .value {
    border-bottom: 1px dotted #999;
}

.bordered-table {
    border: 1px solid #333;
    margin: 10px 0;
}

.bordered-table th,
.bordered-table td {
    border: 1px solid #333;
    padding: 5px 8px;
    font-size: 11px;
}

.bordered-table th {
    background-color: #f0f0f0;
    font-weight: bold;
    text-align: left;
    width: 180px;
}

.content-text {
    font-size: 11px;
    line-height: 1.6;
    text-align: justify;
    margin: 8px 0;
}

.checkbox-item {
    font-size: 11px;
    margin: 5px 0;
    padding-left: 20px;
}

.checkbox-box {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 1px solid #333;
    margin-right: 8px;
    vertical-align: middle;
}

.signature-table {
    width: 100%;
    margin-top: 20px;
}

.signature-table td {
    width: 50%;
    text-align: center;
    vertical-align: top;
    padding: 10px;
    font-size: 11px;
}

.signature-box {
    height: 60px;
    border-bottom: 1px solid #333;
    margin: 10px 30px;
}

.signature-name {
    font-weight: bold;
}

.stamp-area {
    width: 80px;
    height: 80px;
    border: 1px dashed #999;
    margin: 0 auto;
    font-size: 9px;
    color: #999;
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}

.footer-info {
    font-size: 9px;
    color: #666;
}

.note-box {
    border: 1px solid #333;
    padding: 8px;
    margin: 10px 0;
    font-size: 10px;
    background-color: #fffef0;
}

.watermark {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 80px;
    color: rgba(0,0,0,0.03);
    z-index: -1;
}
</style>
@endsection

@section('content')
@php
    $documentNumber = $document_number ?? 'IC/' . date('Ymd') . '/' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
    $patientAge = $patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->age : null;
    $patientGender = $patient->people?->gender ?? null;
    $genderText = $patientGender === 'male' ? 'Laki-laki' : ($patientGender === 'female' ? 'Perempuan' : '-');
@endphp

<div class="watermark">INFORMED CONSENT</div>

<header>
    <div class="header-grid">
        <div>
            @if($workspace?->logo)
                <img src="{{ $workspace->logo }}" height="50" alt="Logo">
            @else
                <img src="{!! backbone_asset('/assets/kalpa-logo.png') !!}" height="50" alt="Logo">
            @endif
        </div>
        <div>
            <p class="clinic-name">{{ $workspace?->name ?? 'NAMA KLINIK/RUMAH SAKIT' }}</p>
            <p class="clinic-address">{{ $workspace?->setting?->address?->full_address ?? $workspace?->setting?->address?->ktp?->name ?? 'Alamat Klinik' }}</p>
            <p class="clinic-address">
                Telp: {{ $workspace?->setting?->phone ?? '-' }} |
                Email: {{ $workspace?->setting?->email ?? '-' }}
            </p>
        </div>
        <div>
            <p style="font-size: 9px; color: #666;">
                No. Izin: {{ $workspace?->setting?->license_number ?? '-' }}
            </p>
        </div>
    </div>
</header>

<footer>
    <div class="footer-info">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; width: 33%;">
                    Dokumen: {{ $documentNumber }}
                </td>
                <td style="text-align: center; width: 34%;">
                    Dicetak: {{ now()->isoFormat('D MMM Y HH:mm') }}
                </td>
                <td style="text-align: right; width: 33%;">
                    Halaman <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>
</footer>

<main>
    <div class="document-title">@yield('document-title', 'FORMULIR PERSETUJUAN TINDAKAN MEDIS')</div>
    <div class="document-number">No: {{ $documentNumber }}</div>

    {{-- SECTION A: IDENTITAS PASIEN --}}
    <div class="section-title">A. IDENTITAS PASIEN</div>
    <table class="bordered-table">
        <tr>
            <th>Nama Lengkap</th>
            <td>{{ $patient->name ?? '-' }}</td>
            <th>No. Rekam Medis</th>
            <td>{{ $patient->medical_record ?? '-' }}</td>
        </tr>
        <tr>
            <th>NIK</th>
            <td>{{ $patient->people?->nik ?? '-' }}</td>
            <th>No. Registrasi</th>
            <td>{{ $visit_registration?->registration_number ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tempat/Tgl Lahir</th>
            <td>{{ ($patient->people?->birth_place ?? '-') . ', ' . ($patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->isoFormat('D MMMM Y') : '-') }}</td>
            <th>Umur/Jenis Kelamin</th>
            <td>{{ $patientAge ?? '-' }} tahun / {{ $genderText }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td colspan="3">{{ $patient->people?->address?->ktp?->name ?? $patient->people?->address?->domicile?->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>No. Telepon</th>
            <td>{{ $patient->people?->phone ?? '-' }}</td>
            <th>Pekerjaan</th>
            <td>{{ $patient->people?->occupation ?? '-' }}</td>
        </tr>
    </table>

    {{-- SECTION B: IDENTITAS PENANGGUNG JAWAB/WALI --}}
    <div class="section-title">B. IDENTITAS PENANGGUNG JAWAB / WALI (Jika Pasien Tidak Dapat Memberikan Persetujuan)</div>
    <table class="bordered-table">
        <tr>
            <th>Nama Lengkap</th>
            <td>{{ $guardian?->name ?? '......................................................................' }}</td>
            <th>Hubungan dengan Pasien</th>
            <td>{{ $guardian?->relationship ?? '..........................................' }}</td>
        </tr>
        <tr>
            <th>NIK</th>
            <td>{{ $guardian?->nik ?? '......................................................................' }}</td>
            <th>No. Telepon</th>
            <td>{{ $guardian?->phone ?? '..........................................' }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td colspan="3">{{ $guardian?->address ?? '........................................................................................................................' }}</td>
        </tr>
    </table>

    {{-- SECTION C: IDENTITAS DOKTER/TENAGA MEDIS --}}
    <div class="section-title">C. IDENTITAS DOKTER / TENAGA MEDIS PELAKSANA</div>
    <table class="bordered-table">
        <tr>
            <th>Nama Dokter</th>
            <td>{{ $doctor?->name ?? $visit_examination?->doctor?->name ?? '......................................................................' }}</td>
            <th>Spesialisasi</th>
            <td>{{ $doctor?->specialization ?? $visit_examination?->doctor?->specialization ?? '..........................................' }}</td>
        </tr>
        <tr>
            <th>No. SIP</th>
            <td>{{ $doctor?->sip_number ?? $visit_examination?->doctor?->sip_number ?? '......................................................................' }}</td>
            <th>No. STR</th>
            <td>{{ $doctor?->str_number ?? $visit_examination?->doctor?->str_number ?? '..........................................' }}</td>
        </tr>
    </table>

    @yield('document-content')
</main>
@endsection

@section('billing-css')
@endsection
