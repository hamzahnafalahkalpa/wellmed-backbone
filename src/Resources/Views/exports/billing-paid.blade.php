@extends('wellmed::exports.billing-paid-layout')

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
    @php 
        $max_column = 6;
        $billing = $transaction->billing;
        $invoices = $billing->invoices;
    @endphp
    @foreach ($invoices as $invoice)
        @php 
            $payment_history = $invoice->payment_history;
            $payment_histories = $payment_history->childs;
        @endphp
        <table class="w-full border-collapse">
            <tr style="color:#fff">
                <th style="border: 1px solid #fff;" class="bg-navy">No</th>
                <th style="border: 1px solid #fff;" class="bg-navy">Nama Pelayanan</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Qty</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Harga</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Diskon</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Jumlah</th>
            </tr>
            @foreach ($payment_histories as $key => $payment_summary)
                @include(
                    'wellmed::exports.payment-history',
                    ['index' => $key, 'payment_history' => $payment_summary]
                )
            @endforeach
            <tr>
                <th style="border: 1px solid #fff;" colspan="3"></th>
                <th style="border: 1px solid #fff;" colspan="2">Total Transaksi</th>
                <th style="border: 1px solid #fff;" class="text-right">{{number_format($billing->amount, 2)}}</th>
            </tr>
            <tr>
                <th style="border: 1px solid #fff;" colspan="3"></th>
                <th style="border: 1px solid #fff;" colspan="2">Diskon</th>
                <th style="border: 1px solid #fff;" class="text-right">{{$billing->discount == 0 ? '-' : number_format($billing->discount, 2)}}</th>
            </tr>
            <tr>
                <th style="border: 1px solid #fff;" colspan="3"></th>
                <th style="border: 1px solid #fff;" colspan="2">Pajak</th>
                <th style="border: 1px solid #fff;" class="text-right">-</th>
            </tr>
            <tr>
                <th style="border: 1px solid #fff;" colspan="3"></th>
                <th style="border: 1px solid #fff;" colspan="2">Total</th>
                <th style="border: 1px solid #fff;" class="text-right">{{number_format($billing->amount, 2)}}</th>
            </tr>
        </table>
        <br>
        <table class="w-full border-collapse">
            <caption class="text-left">Rekapan Pembayaran</caption>
            <tr style="color:#fff">
                <th style="border: 1px solid #fff;" class="bg-navy">No</th>
                <th style="border: 1px solid #fff;" class="bg-navy">Metode Pembayaran</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Kode Bayar</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Timestamp</th>
                <th style="border: 1px solid #fff;" class="bg-navy text-right">Jumlah</th>
            </tr>
            @foreach ($invoice->split_payments as $split_key => $split_payment)
                <tr>
                    <td style="border: 1px solid #fff;" class="text-center">{{ $split_key + 1 }}</td>
                    <td style="border: 1px solid #fff;">{{ $split_payment->payment_method?->name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #fff;" class="text-right">{{ $split_payment->split_payment_code ?? '-' }}</td>
                    <td style="border: 1px solid #fff;" class="text-right">{{ $split_payment->created_at }}</td>
                    <td style="border: 1px solid #fff;" class="text-right">{{ number_format($split_payment->money_paid, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th style="border: 1px solid #fff;" colspan="3"></th>
                <th style="border: 1px solid #fff;">Total Pembayaran</th>
                <th style="border: 1px solid #fff;" class="text-right">{{number_format($billing->amount, 2)}}</th>
            </tr>
        </table>
    @endforeach
@endsection
