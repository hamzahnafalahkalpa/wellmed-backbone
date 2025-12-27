@extends('wellmed::exports.informed-consent.informed-consent')

@section('document-title','SURAT PENOLAKAN TINDAKAN MEDIS')

@section('document-content')
@php 
    $max_column = 6;
@endphp
    <p>
        Dengan ini saya menyatakan bahwa saya menolak untuk dilakukan tindakan medis yang telah dijelaskan sebelumnya oleh pihak medis, yaitu:
        <ol>
            @foreach ($assessment->exam->forms->treatment as $treatment)
                <li>{{ $treatment->name ?? 'Nama tindakan tidak tersedia' }}</li>
            @endforeach
        </ol>
    </p>
    <p>Saya memahami dan perlunya dilakukan tindakan medis terkait, namun dengan pertimbangan dan keputusan pribadi saya menolak tindakan tersebut. Dalam hal ini saya bertanggung jawab atas konsekuensi yang mungkin timbul.</p>
    <p>Demikian surat penolakan ini saya buat dengan sebenarnya tanpa paksaan dari pihak manapun, untuk dipergunakan sebagaimana mestinya.</p>
    <br>
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: right;">
                {{ $workspace?->setting?->address?->district?->name ?? '.............' }}, ............ 20....
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; text-align: center;">
                <strong>Yang Menyatakan</strong>
            </td>
            <td style="width: 50%; text-align: center;">
                <strong>Tenaga Medis Penanggung Jawab</strong>
            </td>
        </tr>
        <tr>
            <td style="height: 80px;"></td>
            <td style="height: 80px;"></td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <strong>(...................................................)</strong>
            </td>
            <td style="text-align: center;">
                <strong>(...................................................)</strong>
            </td>
        </tr>
    </table>
@endsection