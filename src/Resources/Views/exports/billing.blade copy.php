@extends('wellmed::exports.billing-layout')

@section('billing-css')
    td[1] {
        width: 5%;
    }
    td[2] {
        width: 45%;
    }
    td[3] {
        width: 10%;
    }
    td[4] {
        width: 10%;
    }
    td[5] {
        width: 10%;
    }
    td[6] {
        width: 20%;
    }
@endsection

@section('tbody')
    <tr>
        <th class="bg-navy">No</th>
        <th class="bg-navy">Nama Pelayanan</th>
        <th class="bg-navy">Qty</th>
        <th class="bg-navy">Harga</th>
        <th class="bg-navy">Diskon</th>
        <th class="bg-navy">Jumlah</th>
    </tr>
    @for ($i=0;$i<20;$i++)
        <tr>
            <td class="text-center">1</td>
            <td>MCU APOLLO GROUP - SSGL</td>
            <td class="text-center">1</td>
            <td class="text-right">4.800.000</td>
            <td class="text-right">-</td>
            <td class="text-right">4.800.000</td>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td>
                Vaccine - Yellow Fever
                <span class="text-sm">test</span>
            </td>
            <td class="text-center">1</td>
            <td class="text-right">800.000</td>
            <td class="text-right">-</td>
            <td class="text-right">800.000</td>
        </tr>
        <tr>
            <td class="text-center">3</td>
            <td>
                USG - Ultrasound Abdomen
            </td>
            <td class="text-center">1</td>
            <td class="text-right">250.000</td>
            <td class="text-right">-</td>
            <td class="text-right">250.000</td>
        </tr>
    @endfor
    <tr>
        <td colspan="3"></td>
        <td colspan="2">Total Transaksi</td>
        <td class="text-right">5.850.000</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="2">Diskon</td>
        <td class="text-right">-</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="2">Pajak</td>
        <td class="text-right">-</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="2">Total</td>
        <td class="text-right">5.850.000</td>
    </tr>
@endsection
