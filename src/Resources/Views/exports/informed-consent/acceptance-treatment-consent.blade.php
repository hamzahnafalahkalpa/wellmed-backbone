@extends('wellmed::exports.informed-consent.informed-consent')

@section('document-title','SURAT PERSETUJUAN TINDAKAN MEDIS')

@section('document-content')
@php 
    $max_column = 6;
@endphp
    <p>
        Menyatakan dengan sesungguhnya bahwa saya telah mendapat penjelasan dari dokter yang merawat saya mengenai tindakan medis yang akan dilakukan, yaitu:
        <ol>
            @foreach ($assessment->exam->forms->treatment as $treatment)
                <li>{{ $treatment->name ?? 'Nama tindakan tidak tersedia' }}</li>
            @endforeach
        </ol>
    </p>
    <p>Maka kami menyatakan tidak keberatan untuk dilakukan tindakan medis, setelah diberikan penjelasan terkait risiko dan tujuan tindakan medis tersebut.</p>
    <p>Demikian surat persetujuan ini dibuat dengan sebenarnya tanpa paksaan dari pihak manapun, untuk dipergunakan sebagaimana mestinya.</p>
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
                <strong>Pelaksana Tindakan Medis</strong>
            </td>
            <td style="width: 50%; text-align: center;">
                <strong>Yang Membuat Pernyataan</strong>
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