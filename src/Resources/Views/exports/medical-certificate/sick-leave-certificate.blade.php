@extends('wellmed::exports.medical-certificate.medical-certificate')

@section('document-title', 'SURAT KETERANGAN SAKIT')

@section('document-content')
@php
    $patientAge = $patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->age : null;
    $patientGender = $patient->people?->gender ?? null;
    $genderText = $patientGender === 'male' ? 'Laki-laki' : ($patientGender === 'female' ? 'Perempuan' : '-');
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at) : now();

    // Rest period calculation
    $restDays = $rest_days ?? 3;
    $restStartDate = isset($rest_start_date) ? \Carbon\Carbon::parse($rest_start_date) : $examDate;
    $restEndDate = isset($rest_end_date) ? \Carbon\Carbon::parse($rest_end_date) : $restStartDate->copy()->addDays($restDays - 1);

    // Get diagnosis (use general description for privacy if needed)
    $diagnosisDisplay = $diagnosis_display ?? $diagnosis_text ?? 'Keluhan medis yang memerlukan istirahat';
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

<div class="content-text" style="margin-top: 15px;">
    Berdasarkan hasil pemeriksaan yang telah dilakukan, yang bersangkutan dinyatakan:
</div>

{{-- KETERANGAN SAKIT --}}
<div class="result-box-wrapper">
    <div class="result-box" style="border-color: #c53030; background-color: #fff5f5;">
        <div class="result-text" style="color: #c53030;">SAKIT</div>
        <div style="font-size: 11px; margin-top: 5px; color: #c53030;">
            dan memerlukan istirahat
        </div>
    </div>
</div>

{{-- DETAIL ISTIRAHAT --}}
<table class="patient-table" style="margin-top: 20px;">
    <tr>
        <th colspan="2" style="text-align: center; background-color: #fff5f5; color: #c53030;">KETERANGAN ISTIRAHAT</th>
    </tr>
    <tr>
        <th style="width: 30%;">Keluhan/Diagnosis</th>
        <td>{{ $diagnosisDisplay }}</td>
    </tr>
    <tr>
        <th>Lama Istirahat</th>
        <td><strong>{{ $restDays }} ({{ $rest_days_text ?? 'tiga' }}) hari</strong></td>
    </tr>
    <tr>
        <th>Mulai Tanggal</th>
        <td>{{ $restStartDate->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
        <th>Sampai Dengan Tanggal</th>
        <td>{{ $restEndDate->isoFormat('dddd, D MMMM Y') }}</td>
    </tr>
    <tr>
        <th>Keterangan Tambahan</th>
        <td>
            @if($additional_notes ?? false)
                {{ $additional_notes }}
            @else
                Pasien dianjurkan untuk beristirahat di rumah dan minum obat sesuai resep dokter.
                Apabila keluhan berlanjut atau memberat, segera periksa kembali ke dokter.
            @endif
        </td>
    </tr>
</table>

@if($recommendations ?? false)
<div class="content-text" style="margin-top: 15px;">
    <strong>Anjuran Dokter:</strong>
    <ul style="margin: 5px 0 0 20px; font-size: 11px;">
        @foreach($recommendations as $recommendation)
            <li>{{ $recommendation }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="content-text" style="margin-top: 20px;">
    Demikian surat keterangan sakit ini dibuat dengan sebenarnya berdasarkan hasil pemeriksaan medis yang dilakukan,
    untuk dapat dipergunakan sebagaimana mestinya.
</div>

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

<div class="validity-notice" style="background-color: #fff5f5; border-color: #c53030;">
    <strong>PERHATIAN:</strong><br>
    <ul style="text-align: left; margin: 5px 0 0 20px; font-size: 9px;">
        <li>Surat keterangan sakit ini hanya berlaku untuk periode yang tercantum di atas.</li>
        <li>Surat ini tidak dapat diperpanjang tanpa pemeriksaan ulang oleh dokter.</li>
        <li>Penyalahgunaan surat keterangan ini dapat dikenakan sanksi sesuai hukum yang berlaku.</li>
        <li>Untuk keperluan klaim asuransi, harap menghubungi bagian administrasi klinik.</li>
    </ul>
</div>

@endsection
