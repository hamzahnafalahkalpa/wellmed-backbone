@extends('wellmed::app')

@section('title', 'Surat Keterangan Medis')

@section('css')
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

@page {
    size: A4;
    margin: 100px 50px 100px 50px;
}

header {
    position: fixed;
    top: -80px;
    left: 0;
    right: 0;
    height: 70px;
    text-align: center;
    border-bottom: 3px double #333;
    padding-bottom: 10px;
}

footer {
    position: fixed;
    bottom: -80px;
    left: 0;
    right: 0;
    height: 60px;
    text-align: center;
    font-size: 9px;
    border-top: 1px solid #ccc;
    padding-top: 8px;
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
    font-size: 18px;
    font-weight: bold;
    margin: 0;
    color: #1a365d;
    text-transform: uppercase;
}

.clinic-tagline {
    font-size: 10px;
    font-style: italic;
    margin: 2px 0;
    color: #666;
}

.clinic-address {
    font-size: 10px;
    margin: 2px 0;
    color: #444;
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
    font-size: 16px;
    font-weight: bold;
    text-decoration: underline;
    margin: 15px 0 5px 0;
    letter-spacing: 2px;
}

.document-number {
    text-align: center;
    font-size: 11px;
    margin-bottom: 20px;
    color: #666;
}

.patient-table {
    width: 80%;
    margin: 15px auto;
    border: 1px solid #333;
}

.patient-table th,
.patient-table td {
    border: 1px solid #333;
    padding: 6px;
    font-size: 11px;
}

.patient-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    text-align: left;
    width: 160px;
}

.content-text {
    font-size: 11px;
    line-height: 1.8;
    text-align: justify;
    margin: 12px 0;
}

.result-box {
    text-align: center;
    margin: 10px auto;
    padding: 15px 30px;
    border: 2px solid #1a365d;
    background-color: #f0f7ff;
    display: inline-block;
    width: auto;
}

.result-box-wrapper {
    text-align: center;
}

.result-text {
    font-size: 16px;
    font-weight: bold;
    color: #1a365d;
    letter-spacing: 1px;
}

.signature-section {
    margin-top: 10px;
}

.signature-table {
    width: 100%;
}

.signature-table td {
    vertical-align: top;
    padding: 10px;
    font-size: 11px;
}

.signature-box {
    height: 40px;
    margin: 10px 0;
}

.signature-name {
    font-weight: bold;
    text-decoration: underline;
}

.stamp-area {
    width: 100px;
    height: 100px;
    border: 1px dashed #999;
    margin: 10px auto;
    border-radius: 50%;
    font-size: 8px;
    color: #999;
    display: table;
}

.stamp-area span {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}

.footer-info {
    font-size: 8px;
    color: #888;
}

.validity-notice {
    font-size: 10px;
    color: #666;
    font-style: italic;
    text-align: center;
    margin-top: 0px;
    padding: 8px;
    border: 1px dashed #999;
    background-color: #fffef0;
}

.qr-code {
    width: 60px;
    height: 60px;
    border: 1px solid #ccc;
    margin: 5px auto;
    font-size: 8px;
    color: #999;
    display: table;
}

.qr-code span {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}

.watermark {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 100px;
    color: rgba(0,0,0,0.02);
    z-index: -1;
    white-space: nowrap;
}
</style>
@endsection

@section('content')
@php
    $documentNumber = $document_number ?? 'SKM/' . date('Ymd') . '/' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
    $patientAge = $patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->age : null;
    $patientGender = $patient->people?->gender ?? null;
    $genderText = $patientGender === 'male' ? 'Laki-laki' : ($patientGender === 'female' ? 'Perempuan' : '-');
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at) : now();
@endphp

<div class="watermark">SURAT KETERANGAN</div>

<header>
    <div class="header-grid">
        <div>
            @if($workspace?->setting?->logo)
                <img src="{{ $workspace->setting->logo }}" height="55" alt="Logo">
            @else
                <img src="{!! backbone_asset('/assets/kalpa-logo.png') !!}" height="55" alt="Logo">
            @endif
        </div>
        <div>
            <p class="clinic-name">{{ $workspace?->name ?? 'NAMA KLINIK/RUMAH SAKIT' }}</p>
            @if(isset($workspace?->setting?->tagline))
                <p class="clinic-tagline">"{{ $workspace->setting->tagline }}"</p>
            @endif
            <p class="clinic-address">{{ $workspace?->setting?->address?->full_address ?? $workspace?->setting?->address?->ktp?->name ?? 'Alamat Klinik' }}</p>
            <p class="clinic-address">
                Telp: {{ $workspace?->setting?->phone ?? '-' }} |
                Email: {{ $workspace?->setting?->email ?? '-' }}
            </p>
        </div>
        <div>
            <div class="qr-code">
                <span>QR Code<br>Verifikasi</span>
            </div>
        </div>
    </div>
</header>

<footer>
    <div class="footer-info">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; width: 33%;">
                    No. Izin Klinik: {{ $workspace?->setting?->license_number ?? '-' }}
                </td>
                <td style="text-align: center; width: 34%;">
                    Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah
                </td>
                <td style="text-align: right; width: 33%;">
                    {{ $documentNumber }}
                </td>
            </tr>
        </table>
    </div>
</footer>

<main>
    <div class="document-title">@yield('document-title', 'SURAT KETERANGAN MEDIS')</div>
    <div class="document-number">Nomor: {{ $documentNumber }}</div>

    @yield('document-content')
</main>
@endsection

@section('billing-css')
@endsection
