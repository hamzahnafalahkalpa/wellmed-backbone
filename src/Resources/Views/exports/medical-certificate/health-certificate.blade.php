@extends('wellmed::exports.medical-certificate.medical-certificate')

@section('document-title', 'SURAT KETERANGAN KESEHATAN')

@section('document-content')
@php
    $patientAge = $patient->people?->dob ? \Carbon\Carbon::parse($patient->people->dob)->age : null;
    $patientGender = $patient->people?->gender ?? null;
    $genderText = $patientGender === 'male' ? 'Laki-laki' : ($patientGender === 'female' ? 'Perempuan' : '-');
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at) : now();

    // Get vital signs from assessment if available
    $vitalSigns = $assessment?->exam?->forms?->vital_signs ?? null;
    $bloodPressure = $vitalSigns?->blood_pressure ?? ($blood_pressure ?? '-');
    $heartRate = $vitalSigns?->heart_rate ?? ($heart_rate ?? '-');
    $temperature = $vitalSigns?->temperature ?? ($temperature ?? '-');
    $respiratoryRate = $vitalSigns?->respiratory_rate ?? ($respiratory_rate ?? '-');
    $weight = $vitalSigns?->weight ?? ($weight ?? '-');
    $height = $vitalSigns?->height ?? ($height ?? '-');

    // Calculate BMI if possible
    $bmi = null;
    $bmiCategory = null;
    if (is_numeric($weight) && is_numeric($height) && $height > 0) {
        $heightM = $height / 100;
        $bmi = round($weight / ($heightM * $heightM), 1);
        if ($bmi < 18.5) $bmiCategory = 'Berat Badan Kurang';
        elseif ($bmi < 25) $bmiCategory = 'Normal';
        elseif ($bmi < 30) $bmiCategory = 'Berat Badan Lebih';
        else $bmiCategory = 'Obesitas';
    }

    // Certificate purpose
    $certificatePurpose = $purpose ?? $certificate_purpose ?? 'Keperluan Umum';
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
        <th>Golongan Darah</th>
        <td>{{ $patient->people?->blood_type ?? $blood_type ?? '-' }}</td>
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
    <tr>
        <th>Keperluan</th>
        <td><strong>{{ $certificatePurpose }}</strong></td>
    </tr>
</table>

{{-- HASIL PEMERIKSAAN --}}
<div class="content-text" style="margin-top: 15px;">
    <strong>Hasil Pemeriksaan:</strong>
</div>

<table class="patient-table" style="width: 100%;">
    <tr>
        <th colspan="4" style="text-align: center; background-color: #e8f4f8;">TANDA-TANDA VITAL & ANTROPOMETRI</th>
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
    @if($bmi)
    <tr>
        <th>Indeks Massa Tubuh (IMT)</th>
        <td>{{ $bmi }} kg/m&sup2;</td>
        <th>Kategori</th>
        <td>{{ $bmiCategory }}</td>
    </tr>
    @endif
</table>

<table class="patient-table" style="width: 100%; margin-top: 10px;">
    <tr>
        <th colspan="3" style="text-align: center; background-color: #e8f4f8;">PEMERIKSAAN FISIK</th>
    </tr>
    <tr>
        <th style="width: 25%;">Keadaan Umum</th>
        <td style="width: 50%;">{{ $general_condition ?? 'Baik' }}</td>
        <td style="width: 25%; text-align: center;">
            @if(($general_condition_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Kesadaran</th>
        <td>{{ $consciousness ?? 'Compos Mentis (Sadar Penuh)' }}</td>
        <td style="text-align: center;">
            <span style="color: green;">&#10003; Normal</span>
        </td>
    </tr>
    <tr>
        <th>Mata</th>
        <td>
            Visus: OD {{ $visus_od ?? '6/6' }} | OS {{ $visus_os ?? '6/6' }}<br>
            {{ $eyes ?? 'Konjungtiva tidak anemis, sklera tidak ikterik' }}
        </td>
        <td style="text-align: center;">
            @if(($eyes_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Telinga</th>
        <td>{{ $ears ?? 'Dalam batas normal, tidak ada gangguan pendengaran' }}</td>
        <td style="text-align: center;">
            @if(($ears_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Hidung & Tenggorokan</th>
        <td>{{ $nose_throat ?? 'Dalam batas normal' }}</td>
        <td style="text-align: center;">
            <span style="color: green;">&#10003; Normal</span>
        </td>
    </tr>
    <tr>
        <th>Gigi & Mulut</th>
        <td>{{ $teeth_mouth ?? 'Dalam batas normal' }}</td>
        <td style="text-align: center;">
            @if(($teeth_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Jantung</th>
        <td>{{ $heart ?? 'Bunyi jantung I-II reguler, tidak ada murmur' }}</td>
        <td style="text-align: center;">
            @if(($heart_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Paru-paru</th>
        <td>{{ $lungs ?? 'Vesikuler, tidak ada ronki/wheezing' }}</td>
        <td style="text-align: center;">
            @if(($lungs_status ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; Tidak Normal</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Abdomen</th>
        <td>{{ $abdomen ?? 'Supel, tidak ada nyeri tekan, bising usus normal' }}</td>
        <td style="text-align: center;">
            <span style="color: green;">&#10003; Normal</span>
        </td>
    </tr>
    <tr>
        <th>Ekstremitas</th>
        <td>{{ $extremities ?? 'Tidak ada kelainan, tidak ada edema' }}</td>
        <td style="text-align: center;">
            <span style="color: green;">&#10003; Normal</span>
        </td>
    </tr>
    <tr>
        <th>Kulit</th>
        <td>{{ $skin ?? 'Tidak ada kelainan' }}</td>
        <td style="text-align: center;">
            <span style="color: green;">&#10003; Normal</span>
        </td>
    </tr>
</table>

@if($lab_results ?? false)
<table class="patient-table" style="width: 100%; margin-top: 10px;">
    <tr>
        <th colspan="3" style="text-align: center; background-color: #e8f4f8;">PEMERIKSAAN LABORATORIUM</th>
    </tr>
    @foreach($lab_results as $lab)
    <tr>
        <th style="width: 25%;">{{ $lab['name'] ?? '-' }}</th>
        <td style="width: 50%;">{{ $lab['result'] ?? '-' }} {{ $lab['unit'] ?? '' }}</td>
        <td style="width: 25%; text-align: center;">
            @if(($lab['status'] ?? 'normal') === 'normal')
                <span style="color: green;">&#10003; Normal</span>
            @else
                <span style="color: red;">&#10007; {{ $lab['status'] ?? 'Tidak Normal' }}</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endif

@if($xray_result ?? false)
<table class="patient-table" style="width: 100%; margin-top: 10px;">
    <tr>
        <th colspan="2" style="text-align: center; background-color: #e8f4f8;">PEMERIKSAAN RADIOLOGI</th>
    </tr>
    <tr>
        <th style="width: 25%;">Rontgen Thorax</th>
        <td>{{ $xray_result }}</td>
    </tr>
</table>
@endif

{{-- KESIMPULAN --}}
<div class="content-text" style="margin-top: 20px;">
    <strong>KESIMPULAN:</strong>
</div>

<div class="result-box-wrapper">
    <div class="result-box">
        <div class="result-text">{{ $conclusion ?? 'SEHAT' }}</div>
        @if($conclusion_detail ?? false)
        <div style="font-size: 11px; margin-top: 5px;">
            {{ $conclusion_detail }}
        </div>
        @endif
    </div>
</div>

@if($recommendations ?? false)
<div class="content-text" style="margin-top: 10px;">
    <strong>Rekomendasi/Saran:</strong>
    <ul style="margin: 5px 0 0 20px; font-size: 11px;">
        @foreach($recommendations as $recommendation)
            <li>{{ $recommendation }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="content-text">
    Demikian surat keterangan kesehatan ini dibuat dengan sebenarnya berdasarkan hasil pemeriksaan medis yang dilakukan,
    untuk keperluan <strong>{{ $certificatePurpose }}</strong>.
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

<div class="validity-notice">
    <strong>Surat keterangan ini berlaku selama {{ $validity_days ?? 30 }} ({{ $validity_days_text ?? 'tiga puluh' }}) hari sejak tanggal diterbitkan.</strong><br>
    <small>Surat keterangan ini hanya berlaku untuk keperluan yang disebutkan di atas dan tidak dapat digunakan untuk keperluan lain.</small>
</div>

@endsection
