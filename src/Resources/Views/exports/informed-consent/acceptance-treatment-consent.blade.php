@extends('wellmed::exports.informed-consent.informed-consent')

@section('document-title', 'FORMULIR PERSETUJUAN TINDAKAN MEDIS')

@section('document-content')
@php
    $treatments = $assessment?->exam?->forms?->treatment ?? collect([]);
    $diagnosis = $assessment?->exam?->forms?->diagnosis ?? collect([]);
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at)->isoFormat('D MMMM Y') : now()->isoFormat('D MMMM Y');
@endphp

{{-- SECTION D: DIAGNOSIS / INDIKASI --}}
<div class="section-title">D. DIAGNOSIS / INDIKASI TINDAKAN</div>
<table class="bordered-table">
    <tr>
        <th style="width: 150px;">Diagnosis</th>
        <td>
            @if(count($diagnosis) > 0)
                @foreach ($diagnosis as $index => $diag)
                    {{ ($index + 1) }}. {{ $diag->name ?? $diag->icd10?->name ?? '-' }}
                    @if($diag->icd10?->code)
                        ({{ $diag->icd10->code }})
                    @endif
                    <br>
                @endforeach
            @else
                {{ $diagnosis_text ?? '...............................................................................................................................' }}
            @endif
        </td>
    </tr>
</table>

{{-- SECTION E: INFORMASI TINDAKAN MEDIS --}}
<div class="section-title">E. INFORMASI TINDAKAN MEDIS YANG AKAN DILAKUKAN</div>
<table class="bordered-table">
    <tr>
        <th style="width: 150px;">Nama Tindakan</th>
        <td>
            @if(count($treatments) > 0)
                @foreach ($treatments as $index => $treatment)
                    {{ ($index + 1) }}. {{ $treatment->name ?? 'Nama tindakan tidak tersedia' }}<br>
                @endforeach
            @else
                {{ $treatment_name ?? '...............................................................................................................................' }}
            @endif
        </td>
    </tr>
    <tr>
        <th>Tujuan Tindakan</th>
        <td>{{ $treatment_purpose ?? '...............................................................................................................................' }}</td>
    </tr>
    <tr>
        <th>Tata Cara Tindakan</th>
        <td>{{ $treatment_procedure ?? '...............................................................................................................................' }}</td>
    </tr>
    <tr>
        <th>Risiko & Komplikasi</th>
        <td>
            {{ $treatment_risks ?? 'Risiko yang mungkin terjadi meliputi namun tidak terbatas pada: perdarahan, infeksi, reaksi alergi, nyeri, dan komplikasi lain yang tidak dapat diprediksi sebelumnya.' }}
        </td>
    </tr>
    <tr>
        <th>Alternatif Tindakan</th>
        <td>{{ $treatment_alternatives ?? '...............................................................................................................................' }}</td>
    </tr>
    <tr>
        <th>Prognosis</th>
        <td>{{ $prognosis ?? 'Prognosis tergantung pada kondisi pasien dan respons terhadap tindakan.' }}</td>
    </tr>
    <tr>
        <th>Jenis Anestesi</th>
        <td>
            <span class="checkbox-box">{{ ($anesthesia_type ?? '') === 'local' ? '✓' : '' }}</span> Lokal
            &nbsp;&nbsp;
            <span class="checkbox-box">{{ ($anesthesia_type ?? '') === 'regional' ? '✓' : '' }}</span> Regional
            &nbsp;&nbsp;
            <span class="checkbox-box">{{ ($anesthesia_type ?? '') === 'general' ? '✓' : '' }}</span> Umum
            &nbsp;&nbsp;
            <span class="checkbox-box">{{ ($anesthesia_type ?? '') === 'none' ? '✓' : '' }}</span> Tanpa Anestesi
        </td>
    </tr>
    {{-- <tr>
        <th>Perkiraan Biaya</th>
        <td>{{ $estimated_cost ? 'Rp ' . number_format($estimated_cost, 0, ',', '.') : '...............................................................................................................................' }}</td>
    </tr> --}}
</table>

{{-- SECTION F: PERNYATAAN PASIEN/WALI --}}
<div class="section-title">F. PERNYATAAN PASIEN / WALI</div>
<div class="content-text">
    Saya yang bertanda tangan di bawah ini menyatakan bahwa:
</div>

<div style="margin: 10px 0; font-size: 11px; line-height: 1.8;">
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Saya telah menerima penjelasan secara lengkap dan jelas mengenai diagnosis, tindakan medis yang akan dilakukan, tujuan, tata cara, risiko, komplikasi, alternatif tindakan, dan prognosis.
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Saya telah diberi kesempatan untuk bertanya dan semua pertanyaan saya telah dijawab dengan memuaskan.
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Saya memahami bahwa hasil tindakan tidak dapat dijamin 100% berhasil dan komplikasi dapat terjadi meskipun tindakan telah dilakukan dengan baik.
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Saya memberikan persetujuan untuk dilakukan tindakan medis tersebut di atas dengan penuh kesadaran dan tanpa paksaan dari pihak manapun.
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Saya bersedia menanggung biaya yang timbul dari tindakan medis ini sesuai dengan ketentuan yang berlaku.
    </div>
</div>

<div class="note-box">
    <strong>CATATAN PENTING:</strong><br>
    Persetujuan ini dapat dibatalkan sebelum tindakan dilakukan dengan menyampaikan secara tertulis kepada dokter yang bersangkutan.
    Dalam keadaan darurat yang mengancam jiwa, dokter berhak mengambil tindakan medis yang diperlukan untuk menyelamatkan pasien.
</div>

{{-- SECTION G: TANDA TANGAN --}}
<div class="section-title">G. TANDA TANGAN</div>

<table style="width: 100%; margin-top: 10px;">
    <tr>
        <td style="width: 50%;"></td>
        <td style="width: 50%; text-align: right; font-size: 11px;">
            {{ $workspace?->setting?->address?->district?->name ?? $workspace?->setting?->address?->city?->name ?? '...................' }}, {{ $examDate }}
        </td>
    </tr>
</table>

<table class="signature-table" style="border: 1px solid #333;">
    <tr>
        <td style="border-right: 1px solid #333; width: 50%;">
            <strong>Yang Memberikan Penjelasan</strong><br>
            <small>(Dokter/Tenaga Medis)</small>
            <div class="signature-box"></div>
            <div class="signature-name">
                ({{ $doctor?->name ?? $visit_examination?->doctor?->name ?? '.........................................' }})
            </div>
            <div style="font-size: 10px;">
                SIP: {{ $doctor?->sip_number ?? $visit_examination?->doctor?->sip_number ?? '........................' }}
            </div>
        </td>
        <td style="width: 50%;">
            <strong>Yang Membuat Pernyataan</strong><br>
            <small>(Pasien/Wali)</small>
            <div class="signature-box"></div>
            <div class="signature-name">
                ({{ $patient->name ?? '.........................................' }})
            </div>
            <div style="font-size: 10px;">
                Hubungan: {{ $signer_relationship ?? 'Pasien Sendiri' }}
            </div>
        </td>
    </tr>
</table>

{{-- SAKSI --}}
<table class="signature-table" style="border: 1px solid #333; border-top: none; margin-top: 0;">
    <tr>
        <td colspan="2" style="text-align: center; background-color: #f0f0f0; font-weight: bold; padding: 5px;">
            SAKSI-SAKSI
        </td>
    </tr>
    <tr>
        <td style="border-right: 1px solid #333; width: 50%;">
            <strong>Saksi I (Pihak Keluarga)</strong>
            <div class="signature-box"></div>
            <div class="signature-name">
                (..........................................................)
            </div>
        </td>
        <td style="width: 50%;">
            <strong>Saksi II (Pihak Rumah Sakit/Klinik)</strong>
            <div class="signature-box"></div>
            <div class="signature-name">
                (..........................................................)
            </div>
        </td>
    </tr>
</table>

@endsection
