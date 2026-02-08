@extends('wellmed::exports.medical-certificate.medical-certificate')

@section('document-title', 'SURAT KETERANGAN SEHAT')

@section('document-content')
@php
    $patientAge = $patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->age : null;
    $patientGender = $patient->people?->gender ?? null;
    $genderText = $patientGender === 'male' ? 'Laki-laki' : ($patientGender === 'female' ? 'Perempuan' : '-');
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at) : now();

    // Get vital signs from assessment if available
    $vitalSigns = $assessment?->exam?->forms?->vital_signs ?? null;
    $bloodPressure = $vitalSigns?->blood_pressure ?? '-';
    $heartRate = $vitalSigns?->heart_rate ?? '-';
    $temperature = $vitalSigns?->temperature ?? '-';
    $respiratoryRate = $vitalSigns?->respiratory_rate ?? '-';
    $weight = $vitalSigns?->weight ?? '-';
    $height = $vitalSigns?->height ?? '-';
@endphp

<div class="content-text">
    Yang bertanda tangan di bawah ini, Dokter pada <strong>{{ $workspace?->name ?? '......................................' }}</strong>,
    dengan ini menerangkan bahwa:
</div>

{{-- IDENTITAS PASIEN --}}
<table class="patient-table">
    <tr>
        <th>Nama Lengkap</th>
        <td>{{ $patient->name ?? '-' }}</td>
    </tr>
    <tr>
        <th>NIK</th>
        <td>{{ $patient->people?->nik ?? '-' }}</td>
    </tr>
    <tr>
        <th>Tempat/Tanggal Lahir</th>
        <td>{{ ($patient->people?->birth_place ?? '-') . ', ' . ($patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->isoFormat('D MMMM Y') : '-') }}</td>
    </tr>
    <tr>
        <th>Umur</th>
        <td>{{ $patientAge ?? '-' }} tahun</td>
    </tr>
    <tr>
        <th>Jenis Kelamin</th>
        <td>{{ $genderText }}</td>
    </tr>
    <tr>
        <th>Pekerjaan</th>
        <td>{{ $patient->people?->occupation ?? $occupation ?? '-' }}</td>
    </tr>
    <tr>
        <th>Alamat</th>
        <td>{{ $patient->people?->address?->ktp?->name ?? $patient->people?->address?->domicile?->name ?? '-' }}</td>
    </tr>
    <tr>
        <th>No. Telepon</th>
        <td>{{ $patient->people?->phone ?? '-' }}</td>
    </tr>
</table>

<div class="content-text">
    Telah dilakukan pemeriksaan kesehatan pada:
</div>

<table class="patient-table" style="width: 60%;">
    <tr>
        <th>Tanggal Pemeriksaan</th>
        <td>{{ $examDate->isoFormat('D MMMM Y') }}</td>
    </tr>
    <tr>
        <th>Tempat Pemeriksaan</th>
        <td>{{ $workspace?->name ?? '-' }}</td>
    </tr>
    <tr>
        <th>No. Rekam Medis</th>
        <td>{{ $patient->medical_record ?? '-' }}</td>
    </tr>
</table>

{{-- HASIL PEMERIKSAAN FISIK --}}
<div class="content-text" style="margin-top: 15px;">
    <strong>Hasil Pemeriksaan:</strong>
</div>

<table class="patient-table" style="width: 100%;">
    <tr>
        <th colspan="4" style="text-align: center; background-color: #e8f4f8;">TANDA-TANDA VITAL</th>
    </tr>
    <tr>
        <th style="width: 25%;">Tekanan Darah</th>
        <td style="width: 25%;">{{ $bloodPressure }} mmHg</td>
        <th style="width: 25%;">Denyut Nadi</th>
        <td style="width: 25%;">{{ $heartRate }} x/menit</td>
    </tr>
    <tr>
        <th>Suhu Tubuh</th>
        <td>{{ $temperature }} &deg;C</td>
        <th>Pernapasan</th>
        <td>{{ $respiratoryRate }} x/menit</td>
    </tr>
    <tr>
        <th>Berat Badan</th>
        <td>{{ $weight }} kg</td>
        <th>Tinggi Badan</th>
        <td>{{ $height }} cm</td>
    </tr>
</table>

<table class="patient-table" style="width: 100%; margin-top: 10px;">
    <tr>
        <th colspan="2" style="text-align: center; background-color: #e8f4f8;">PEMERIKSAAN FISIK</th>
    </tr>
    <tr>
        <th style="width: 30%;">Keadaan Umum</th>
        <td>{{ $general_condition ?? 'Baik' }}</td>
    </tr>
    <tr>
        <th>Kesadaran</th>
        <td>{{ $consciousness ?? 'Compos Mentis' }}</td>
    </tr>
    <tr>
        <th>Kepala/Leher</th>
        <td>{{ $head_neck ?? 'Dalam batas normal' }}</td>
    </tr>
    <tr>
        <th>Mata</th>
        <td>{{ $eyes ?? 'Konjungtiva tidak anemis, sklera tidak ikterik' }}</td>
    </tr>
    <tr>
        <th>THT</th>
        <td>{{ $ent ?? 'Dalam batas normal' }}</td>
    </tr>
    <tr>
        <th>Thorax (Jantung & Paru)</th>
        <td>{{ $thorax ?? 'Dalam batas normal' }}</td>
    </tr>
    <tr>
        <th>Abdomen</th>
        <td>{{ $abdomen ?? 'Dalam batas normal' }}</td>
    </tr>
    <tr>
        <th>Ekstremitas</th>
        <td>{{ $extremities ?? 'Dalam batas normal' }}</td>
    </tr>
</table>

@if($lab_results ?? false)
<table class="patient-table" style="width: 100%; margin-top: 10px;">
    <tr>
        <th colspan="2" style="text-align: center; background-color: #e8f4f8;">PEMERIKSAAN PENUNJANG</th>
    </tr>
    @foreach($lab_results as $lab)
    <tr>
        <th style="width: 30%;">{{ $lab['name'] ?? '-' }}</th>
        <td>{{ $lab['result'] ?? '-' }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- KESIMPULAN --}}
<div class="content-text" style="margin-top: 20px;">
    <strong>Berdasarkan hasil pemeriksaan tersebut di atas, dengan ini dinyatakan bahwa yang bersangkutan dalam keadaan:</strong>
</div>

<div class="result-box-wrapper">
    <div class="result-box">
        <div class="result-text">SEHAT</div>
        <div style="font-size: 11px; margin-top: 5px;">
            @if($purpose ?? false)
                dan layak untuk: {{ $purpose }}
            @else
                dan layak untuk bekerja/melaksanakan tugas
            @endif
        </div>
    </div>
</div>

<div class="content-text">
    Demikian surat keterangan sehat ini dibuat dengan sebenarnya berdasarkan hasil pemeriksaan medis yang dilakukan,
    untuk dapat dipergunakan sebagaimana mestinya.
</div>

@if($notes ?? false)
<div class="content-text" style="font-style: italic;">
    <strong>Catatan:</strong> {{ $notes }}
</div>
@endif

{{-- TANDA TANGAN --}}
<div class="signature-section">
    <table class="signature-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                {{ $workspace?->setting?->address?->city?->name ?? $workspace?->setting?->address?->district?->name ?? '...................' }}, {{ $examDate->isoFormat('D MMMM Y') }}
                <br><br>
                <strong>Dokter Pemeriksa,</strong>
                <div class="signature-box"></div>
                <div class="signature-name">
                    {{ $doctor?->name ?? $visit_examination?->doctor?->name ?? '(...................................)' }}
                </div>
                <div style="font-size: 10px;">
                    @if($doctor?->sip_number ?? $visit_examination?->doctor?->sip_number)
                        SIP: {{ $doctor?->sip_number ?? $visit_examination?->doctor?->sip_number }}
                    @endif
                </div>
                <div class="stamp-area">
                    <span>Stempel<br>Klinik</span>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="validity-notice">
    <strong>Surat keterangan ini berlaku selama {{ $validity_days ?? 14 }} ({{ $validity_days_text ?? 'empat belas' }}) hari sejak tanggal diterbitkan.</strong><br>
    Surat ini bukan merupakan surat rujukan dan tidak dapat digunakan untuk keperluan klaim asuransi.
</div>

@endsection
