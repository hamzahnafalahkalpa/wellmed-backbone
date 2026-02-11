@extends('wellmed::exports.informed-consent.informed-consent')

@section('document-title', 'FORMULIR PENOLAKAN TINDAKAN MEDIS')

@section('document-content')
@php
    $treatments = $assessment?->exam?->forms?->treatment ?? collect([]);
    $diagnosis = $assessment?->exam?->forms?->diagnosis ?? collect([]);
    $examDate = $visit_examination?->created_at ? \Carbon\Carbon::parse($visit_examination->created_at)->isoFormat('D MMMM Y') : now()->isoFormat('D MMMM Y');
@endphp

{{-- SECTION D: DIAGNOSIS / INDIKASI --}}
<div class="section-title">D. DIAGNOSIS / INDIKASI</div>
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

{{-- SECTION E: TINDAKAN YANG DITOLAK --}}
<div class="section-title">E. TINDAKAN MEDIS YANG DITOLAK</div>
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
</table>

{{-- SECTION F: INFORMASI YANG TELAH DIBERIKAN --}}
<div class="section-title">F. INFORMASI YANG TELAH DIBERIKAN OLEH DOKTER</div>
<div class="content-text">
    Dokter yang merawat telah menjelaskan kepada saya mengenai:
</div>

<div style="margin: 10px 0; font-size: 11px; line-height: 1.8;">
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Kondisi kesehatan/diagnosis saya saat ini
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Tindakan medis yang dianjurkan untuk mengatasi kondisi tersebut
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Manfaat dan tujuan dari tindakan medis yang dianjurkan
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Risiko dan komplikasi jika tindakan tidak dilakukan
    </div>
    <div style="margin: 5px 0;">
        <span class="checkbox-box">✓</span>
        Alternatif pengobatan lain yang tersedia (jika ada)
    </div>
</div>

{{-- SECTION G: RISIKO PENOLAKAN --}}
<div class="section-title">G. RISIKO AKIBAT PENOLAKAN TINDAKAN</div>
<table class="bordered-table">
    <tr>
        <th style="width: 150px;">Risiko yang Mungkin Terjadi</th>
        <td>
            {{ $refusal_risks ?? 'Dengan menolak tindakan medis yang dianjurkan, saya memahami bahwa kondisi kesehatan saya dapat memburuk, termasuk namun tidak terbatas pada: perburukan kondisi penyakit, komplikasi yang lebih serius, kecacatan permanen, atau kematian.' }}
        </td>
    </tr>
</table>

{{-- SECTION H: PERNYATAAN PENOLAKAN --}}
<div class="section-title">H. PERNYATAAN PENOLAKAN</div>
<div class="content-text" style="border: 2px solid #c00; padding: 15px; background-color: #fff5f5;">
    <strong>DENGAN INI SAYA MENYATAKAN:</strong><br><br>

    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        Saya telah menerima penjelasan yang lengkap dan jelas mengenai kondisi kesehatan saya, tindakan medis yang dianjurkan, serta risiko jika tindakan tidak dilakukan.
    </div>
    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        Saya telah diberi kesempatan untuk bertanya dan semua pertanyaan saya telah dijawab dengan memuaskan.
    </div>
    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        Saya memahami sepenuhnya risiko dan konsekuensi dari penolakan tindakan medis ini.
    </div>
    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        <strong>Dengan pertimbangan dan keputusan sendiri, saya MENOLAK untuk dilakukan tindakan medis tersebut di atas.</strong>
    </div>
    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        Saya bertanggung jawab penuh atas segala akibat yang mungkin timbul dari penolakan ini.
    </div>
    <div style="margin: 8px 0;">
        <span class="checkbox-box">✓</span>
        Saya membebaskan dokter dan rumah sakit/klinik dari segala tuntutan hukum yang mungkin timbul akibat penolakan ini.
    </div>
</div>

{{-- ALASAN PENOLAKAN --}}
<div class="section-title">I. ALASAN PENOLAKAN (Opsional)</div>
<table class="bordered-table">
    <tr>
        <td style="min-height: 60px; padding: 10px;">
            {{ $refusal_reason ?? '...........................................................................................................................................................................................
            ...........................................................................................................................................................................................
            ...........................................................................................................................................................................................' }}
        </td>
    </tr>
</table>

<div class="note-box" style="background-color: #fff0f0; border-color: #c00;">
    <strong>PERINGATAN:</strong><br>
    Penolakan tindakan medis dapat berakibat fatal terhadap kondisi kesehatan Anda.
    Dokter dan pihak rumah sakit/klinik tetap akan memberikan perawatan terbaik sesuai dengan pilihan Anda.
    Anda dapat mengubah keputusan ini kapan saja dengan menyampaikan secara tertulis kepada dokter yang merawat.
</div>

{{-- SECTION J: TANDA TANGAN --}}
<div class="section-title">J. TANDA TANGAN</div>

<table style="width: 100%; margin-top: 10px;">
    <tr>
        <td style="width: 50%;"></td>
        <td style="width: 50%; text-align: right; font-size: 11px;">
            {{ $workspace?->setting?->address?->district?->name ?? $workspace?->setting?->address?->city?->name ?? '...................' }}, {{ $examDate }}
            <br>
            Pukul: {{ now()->format('H:i') }} WIB
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
            <strong>Yang Menolak Tindakan</strong><br>
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
