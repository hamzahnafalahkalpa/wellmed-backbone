@extends('wellmed::app')

@section('title', 'Visit Billing')

@section('css')
    table{
        width:100%;    
    }

    table, th, td {
        border: 1px solid white;
    }

    @page {
        size: A4; /* atau A5 */
        margin: 20mm; /* margin kertas */
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 50px;
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
        margin-top: 70px;  /* jarak header */
        margin-bottom: 70px; /* jarak footer */
    }
    @yield('billing-css')
@endsection

@section('content')
    @php 
        $max_column = 6;
    @endphp
    <table class="w-full bg-white table-auto border-collapse py-1 px-2 box-border">
        <thead>
            <tr>
                {{-- <th colspan="4" class="px-4 py-2">{{strtoupper($workspace?->name) ?? "PT KALPA INOVASI DIGITAL" }}</th> --}}
                <th colspan="{{ $max_column-3 }}" class="px-4 py-2">PT KALPA INOVASI DIGITAL</th>
                {{-- <img src="{{ backbone_asset('https://wellmed-dev.s3.ap-southeast-1.amazonaws.com/assets/kalpa-logo.png') }}" class="rounded no-border" alt=""> --}}
                <th colspan="3" class="px-4 py-2 text-right">
                    <img src="{{ backbone_asset('assets/kalpa-logo.png') }}" class="rounded no-border ml-auto" alt="">
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="{{ $max_column-3 }}" class=""></td>
                <td colspan="3" class="bg-soft-blue">
                    Kwitansi Pasien
                </td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}"></td>
                <td class="bg-soft-blue">Tanggal Transaksi</td>
                <td colspan="2" class="bg-soft-blue">31/12/2025</td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}"></td>
                <td class="bg-soft-blue font-bold">Total Kwitansi</td>
                <td colspan="2" class="bg-soft-blue font-bold">5.850.000</td>
            </tr>
            <tr>
                <td colspan="{{ $max_column }}"></td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}" class=""></td>
                <td class="bg-soft-blue">
                    Nama Pasien
                </td>
                <td colspan="2" class="bg-soft-blue">
                    Hamzah
                </td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}" class=""></td>
                <td class="bg-soft-blue">
                    Nomor RM
                </td>
                <td colspan="2" class="bg-soft-blue">
                    MR0001
                </td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}" class=""></td>
                <td class="bg-soft-blue">
                    Nomor Kunjungan
                </td>
                <td colspan="2" class="bg-soft-blue">
                    VISIT0001
                </td>
            </tr>
            <tr>
                <td colspan="{{ $max_column-3 }}" class=""></td>
                <td class="bg-soft-blue">
                    Nomor Kwitansi
                </td>
                <td colspan="2" class="bg-soft-blue">
                    KW0001
                </td>
            </tr>
            <tr>
                <td colspan="{{ $max_column }}"></td>
            </tr>
            @yield('tbody')
        </tbody>
    </table>
@endsection
