@extends('wellmed::app')

@section('title', 'MEDICAL CERTIFICATE')

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
@endphp

<header>
    <div class="header-grid" style="align-items: center;">
        <div class="col-span-3 text-left font-bold">
            <h2 style="margin:0; font-family: 'Aptos'">PT KALPA INOVASI DIGITAL</h2>
        </div>
        <div class="col-span-3 header-logo">
            <div class="block text-right rounded-[5px] overflow-hidden ml-auto" style="height:auto;">
                <img src="{!! backbone_asset('/assets/kalpa-logo.png') !!}" height="40" alt="Logo">
            </div>
        </div>
    </div>
</header>

<footer>
    <br>
    <br>
    <div class="footer-sign">
        <div>
            Dibuat Oleh, <strong>Hamzah</strong>
        </div>
        <div>
            {{-- Disetujui Oleh, <strong>{{ $patient->name }}</strong> --}}
            Disetujui Oleh, ______________________
        </div>
        <div>
            Dicetak Oleh, <strong>Hamzah</strong>
        </div>
    </div>
</footer>

<main>
    <h2 class="text-center font-bold">@yield('document-title')</h2>
    <br>
    @yield('document-content')
</main>
@endsection

<style>
@yield('billing-css')
</style>
