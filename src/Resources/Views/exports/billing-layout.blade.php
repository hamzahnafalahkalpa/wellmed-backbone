@extends('wellmed::app')

@section('title', 'Visit Billing')

@section('css')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    @page {
        size: A4; /* bisa A5 juga */
        /* margin: 20mm; */
        margin: 100px 40px;
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        text-align: center;
    }

    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        text-align: center;
        font-size: 12px;
    }

    main {
        margin-top: 70px;   /* jarak header */
        margin-bottom: 60px; /* jarak footer */
    }

    /* Header table/grid styling */
    .header-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        align-items: center;
    }

    .header-logo {
        text-align: right;
    }

    .text-right {
        text-align: right;
    }

    /* Agar thead diulang otomatis di setiap halaman */
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }

</style>
@endsection

@section('content')
@php 
    $max_column = 6;
    $billing = $transaction->billing;
    $payment_summary = $transaction->payment_summary;
@endphp

<header>
    <div class="header-grid" style="align-items: center;">
        <div class="col-span-3 text-left font-bold">
            <h2>PT KALPA INOVASI DIGITAL</h2>
        </div>
        <div class="col-span-3 header-logo">
            <div class="block text-right rounded-[20px] overflow-hidden ml-auto" style="height: auto;">
                <img src="{!! backbone_asset('/assets/kalpa-logo.png') !!}" alt="Logo">
            </div>
        </div>
    </div>
</header>

<footer>
    <div>
        Page <span class="pagenum"></span> of <span class="pagecount"></span> |
        Kwitansi ini dicetak otomatis
    </div>
</footer>

<main>
    <table style="width: 100%; margin-top:100px; margin-bottom: 16px; border-collapse: collapse;">
        <tr style="color: #000">
            <!-- Kiri -->
            <td style="width: 50%; vertical-align: top;">
                {{-- <div>{{ strtoupper($workspace->name) }}</div>
                <div>Alamat: {{$workspace->setting?->address?->name}}</div>
                <div>Telp: {{$workspace->phone}}</div>
                <div>Email: {{$workspace->setting?->email}}</div> --}}
            </td>
            <!-- Kanan -->
            <td style="width: 50%; vertical-align: top; text-align: right;padding:0px">
                <table class="bg-soft-blue" style="width: 100%;">
                    <tr>
                        <td colspan="2" style="border-color:#fff" class="border font-bold p-2">Kwitansi Pasien</td>
                    </tr>
                    <tr>
                        <td style="border-color:#fff" class="w-[180px] border p-2">Tanggal Transaksi</td>
                        <td style="border-color:#fff" class="border text-right p-2">{{$transaction->created_at}}</td>
                    </tr>
                    <tr>
                        <td style="border-color:#fff" class="w-[180px] border p-2">Total Kwitansi</td>
                        <td style="border-color:#fff" class="border text-right p-2">{{$payment_summary->debt == 0 ? '-' : $transaction->payment_summary->debt}}</td>
                    </tr>
                </table>
                <table class="bg-soft-blue mt-4" style="width: 100%;margin-top:16px">
                    @if(isset($transaction->consument->medical_record))
                        <tr>
                            <td style="border-color:#fff" class="w-[180px] border p-2">Nama Pasien</td>
                            <td style="border-color:#fff" class="border text-right p-2">{{$transaction->consument->name}}</td>
                        </tr>
                        <tr>
                            <td style="border-color:#fff" class="w-[180px] border p-2">Nomor RM</td>
                            <td style="border-color:#fff" class="border text-right p-2">{{$transaction->consument->medical_record}}</td>
                        </tr>
                    @else 
                        <tr>
                            <td style="border-color:#fff" class="w-[180px] border p-2">Nama Pelanggan</td>
                            <td style="border-color:#fff" class="border text-right p-2">{{$transaction->consument->name}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="border-color:#fff" class="w-[180px] border p-2">Nomor Kunjungan</td>
                        <td style="border-color:#fff" class="border text-right p-2">{{$transaction->reference->visit_code}}</td>
                    </tr>                        
                    <tr>
                        <td style="border-color:#fff" class="w-[180px] border p-2">Nomor Kwitansi</td>
                        <td style="border-color:#fff" class="border text-right p-2">{{$billing?->billing_code}}</td>
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
<script type="text/php">
if (isset($pdf)) {
    $pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
        $font = $fontMetrics->get_font("Arial", "normal");
        $canvas->text(
            270,
            820,
            "Page $pageNumber of $pageCount | Kwitansi ini dicetak otomatis",
            $font,
            9,
            [0,0,0]
        );
    });
}
</script>
