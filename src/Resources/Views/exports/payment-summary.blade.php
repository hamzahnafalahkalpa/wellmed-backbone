<tr>
    <th class="text-center" style="border: 1px solid #fff;">{{ chr(65 + $index) }}</th>
    <th style="border: 1px solid #fff;">
        {{ $payment_summary->name }}
    </th>
    <th class="text-right" style="border: 1px solid #fff;"></th>
    <th class="text-right" style="border: 1px solid #fff;">{{number_format($payment_summary->amount, 2)}}</th>
    <th class="text-right" style="border: 1px solid #fff;">{{$payment_summary->discount == 0 ? '-' : number_format($payment_summary->discount, 2)}}</th>
    <th class="text-right" style="border: 1px solid #fff;">{{number_format($payment_summary->debt, 2)}}</th>
</tr>
@if(isset($payment_summary->payment_details) && count($payment_summary->payment_details) > 0)
    @foreach($payment_summary->payment_details as $index_detail => $detail)
        <tr>
            <td class="text-center" style="border: 1px solid #fff;">{{ $index_detail + 1 }}</td>
            <td style="border: 1px solid #fff;">
                {{ $detail->transaction_item->name }}
                @if(isset($detail->transaction_item->note))
                    <span class="text-sm">- {{ $detail->transaction_item->note }}</span>
                @endif
            </td>
            <td class="text-right" style="border: 1px solid #fff;">{{$detail->qty}}</td>
            <td class="text-right" style="border: 1px solid #fff;">{{number_format($detail->amount, 2)}}</td>
            <td class="text-right" style="border: 1px solid #fff;">{{$detail->discount == 0 ? '-' : number_format($detail->discount, 2)}}</td>
            <td class="text-right" style="border: 1px solid #fff;">{{number_format($detail->debt, 2)}}</td>
        </tr>
    @endforeach
@endif