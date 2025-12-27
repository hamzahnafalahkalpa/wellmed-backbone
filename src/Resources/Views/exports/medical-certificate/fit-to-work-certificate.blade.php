@extends('wellmed::exports.medical-certificate.medical-certificate')

@section('document-title','SURAT KETERANGAN SEHAT')

@section('document-content')
@php 
    $max_column = 6;
@endphp
    <p style="text-align: justify;">
        Yang bertanda tangan di bawah ini, dokter pada <strong>{{ $workspace?->name ?? '......................................' }}</strong>, menerangkan bahwa:
    </p>
    
    <table class="w-1/2 border-collapse mb-4" style="width:70%">
        <tr>
            <th style="border: 1px solid #000; width:150px;">Nama Pasien</th>
            <td style="border: 1px solid #000;">{{ $patient->name }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; width:150px;">No. Rekam Medis</th>
            <td style="border: 1px solid #000;">{{ $patient->medical_record }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; width:150px;">Tanggal Lahir</th>
            <td style="border: 1px solid #000;">{{ $patient->people->dob }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; width:150px;">Alamat</th>
            <td style="border: 1px solid #000;">{{ $patient->people?->address?->ktp?->name }}</td>
        </tr>
    </table>
    
    <p style="text-align: justify; margin-top: 20px;">
        Telah dilakukan pemeriksaan kesehatan terhadap yang bersangkutan pada:
    </p>
    
    <table class="w-1/2 border-collapse mb-4" style="width:70%">
        <tr>
            <th style="border: 1px solid #000; width:150px;">Tanggal</th>
            <td style="border: 1px solid #000;">{{ $visit_examination->created_at }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; width:150px;">Tempat Pemeriksaan</th>
            <td style="border: 1px solid #000;">{{ $workspace?->name ?? '............................................' }}</td>
        </tr>
    </table>
    <p style="text-align: justify;">
        Berdasarkan hasil pemeriksaan fisik dan penunjang yang telah dilakukan, dengan ini menerangkan bahwa yang bersangkutan dalam keadaan:
    </p>
    
    <div class="bg-soft-blue" style="text-align: center; margin: 20px 0; padding: 10px;box-sizing:border-box">
        <strong style="font-size: 14px;">SEHAT DAN LAYAK UNTUK BEKERJA</strong>
    </div>
    
    <p style="text-align: justify;">
        Demikian surat keterangan ini dibuat dengan sebenarnya berdasarkan hasil pemeriksaan yang dilakukan, untuk dapat dipergunakan sebagaimana mestinya.
    </p>
    
    <br>
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                {{ $workspace?->setting?->address?->district?->name ?? '.............' }}, {{ now()->isoFormat('D MMMM Y') }}
            </td>
        </tr>
    </table>
    
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                <strong>Dokter Pemeriksa,</strong>
            </td>
        </tr>
        <tr>
            <td style="height: 80px;"></td>
            <td style="height: 80px;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                {{-- <strong>{{ $doctor?->name ?? '(...................................)' }}</strong><br>
                {{ $doctor?->registration_number ? 'SIP. ' . $doctor->registration_number : '' }} --}}
            </td>
        </tr>
    </table>
@endsection