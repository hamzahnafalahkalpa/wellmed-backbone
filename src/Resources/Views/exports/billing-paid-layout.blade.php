@extends('wellmed::app')

@section('title', 'Visit Billing')

@section('css')
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

@page {
    size: A4;
    margin: 120px 40px 130px 40px;
    /* margin: 1cm; */
}

header {
    position: fixed;
    top: -90px;
    left: 0;
    right: 0;
    height: 60px;
    text-align: center;
}

footer {
    position: fixed;
    bottom: -70px;
    left: 0;
    right: 0;
    height: 50px;
    text-align: center;
    font-size: 12px;
}

/* main {
    margin-top: 70px;
    margin-bottom: 60px;
} */

/* DomPDF tidak support grid → fallback aman */
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
    width: 70%;
}

.header-grid > div:last-child {
    text-align: right;
    width: 30%;
}

.header-logo {
    text-align: right;
}

.text-right {
    text-align: right;
}

/* repeat header table */
thead { display: table-header-group; }
tfoot { display: table-footer-group; }


.footer-note {
    font-size: 10px;
    margin-bottom: 8px;
    text-align: left;
}

.footer-sign {
    width: 100%;
    display: table;
    table-layout: fixed;
    text-align: center;
    margin-bottom: 6px;
}

.footer-sign div {
    display: table-cell;
    vertical-align: top;
}

.footer-meta {
    font-size: 10px;
    display: table;
    width: 100%;
    table-layout: fixed;
}

.footer-meta div {
    display: table-cell;
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
</style>
@endsection

@section('content')
@php 
    $max_column = 6;
    $billing = $transaction->billing;
    $consument = $transaction->consument;
@endphp

<header>
    <div class="header-grid" style="align-items: center;">
        <div class="col-span-3 text-left font-bold">
            <h2 style="margin:0; font-family: 'Aptos'">{{ strtoupper($workspace->name ?? 'PT KALPA INOVASI DIGITAL') }}</h2>
        </div>
        <div class="col-span-3 header-logo">
            <div class="block text-right rounded-[5px] overflow-hidden ml-auto" style="height:auto;">
                @if($workspace?->setting?->logo)
                    <img src="{{ $workspace->setting->logo }}" height="40" alt="Logo">
                @else
                    <img src="{!! backbone_asset('/assets/kalpa-logo.png') !!}" height="40" alt="Logo">
                @endif
            </div>
        </div>
    </div>
</header>

<footer>
    <div class="footer-note">
        Semua denominasi dalam rupiah kecuali ada dinyatakan berbeda.
    </div>
    <br>
    <br>
    <div class="footer-sign">
        <div>
            Dibuat Oleh, <strong>Hamzah</strong>
        </div>
        <div>
            Disetujui Oleh, <strong>{{ $consument->name }}</strong>
        </div>
        <div>
            Dicetak Oleh, <strong>Hamzah</strong>
        </div>
    </div>
</footer>

<main>
    <table style="width: 100%; margin-bottom: 16px; border-collapse: collapse;">
        <tr style="color: #000">
            <!-- Kiri -->
            <td style="width: 50%; vertical-align: top;">
                <div style="font-weight:bold">{{ strtoupper($workspace->name) }}</div>
                <div>Alamat: {{$workspace->setting?->address?->name}}</div>
                <div>Telp: {{$workspace->phone}}</div>
                <div>Email: {{$workspace->setting?->email}}</div>
            </td>
            <!-- Kanan -->
            <td style="width: 50%; vertical-align: top; text-align: right;padding:0px">
                <table class="bg-soft-blue" style="width: 100%;">
                    <tr>
                        <td colspan="2" style="border:1px solid #fff" class="border font-bold p-2">Kwitansi Pasien</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #fff" class="w-[180px] border p-2">Tanggal Transaksi</td>
                        <td style="border:1px solid #fff" class="border text-right p-2">{{$transaction->created_at}}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #fff" class="w-[180px] border p-2">Total Kwitansi</td>
                        <td style="border:1px solid #fff" class="border text-right p-2">{{$billing->debt == 0 ? '-' : $billing->debt}}</td>
                    </tr>
                </table>
                <table class="bg-soft-blue mt-4" style="width: 100%;margin-top:16px">
                    @if(isset($transaction->consument->medical_record))
                        <tr>
                            <td style="border:1px solid #fff" class="w-[180px] border p-2">Nama Pasien</td>
                            <td style="border:1px solid #fff" class="border text-right p-2">{{$transaction->consument->name}}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #fff" class="w-[180px] border p-2">Nomor RM</td>
                            <td style="border:1px solid #fff" class="border text-right p-2">{{$transaction->consument->medical_record}}</td>
                        </tr>
                    @else 
                        <tr>
                            <td style="border:1px solid #fff" class="w-[180px] border p-2">Nama Pelanggan</td>
                            <td style="border:1px solid #fff" class="border text-right p-2">{{$transaction->consument->name}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="border:1px solid #fff" class="w-[180px] border p-2">Nomor Kunjungan</td>
                        <td style="border:1px solid #fff" class="border text-right p-2">{{$transaction->reference->visit_code}}</td>
                    </tr>                        
                    <tr>
                        <td style="border:1px solid #fff" class="w-[180px] border p-2">Nomor Kwitansi</td>
                        <td style="border:1px solid #fff" class="border text-right p-2">{{$billing?->billing_code}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @yield('tbody')
</main>
@endsection

<style>
@yield('billing-css')
</style>
