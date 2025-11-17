<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class TherapeuticClassSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
        ['name' => 'Agen Radiografi & Diagnostik'],
        [
            'name' => 'Alergi dan Sistem Imun', 'childs' => [
                ['name' => 'Antihistamin dan Antialergi'],
                ['name' => 'Imunosupresan'],
                ['name' => 'Vaksin, Antiserum, dan Imunologikal']
            ]
        ],
        [
            'name' => 'Anti Infeksi (Sistemik)', 'childs' => [
                ['name' => 'Aminoglikosida'],
                ['name' => 'Antelmintik'],
                ['name' => 'Antiamuba'],
                ['name' => 'Antibiotik Golongan Lain'],
                ['name' => 'Antijamur'],
                ['name' => 'Antilepra'],
                ['name' => 'Antimalaria'],
                ['name' => 'Antivirus'],
                ['name' => 'Betalaktam Golongan Lain'],
                ['name' => 'Kloramfenikol'],
                ['name' => 'Kombinasi Antibakterial'],
                ['name' => 'Kuinolon'],
                ['name' => 'Makrolid'],
                ['name' => 'Obat Anti Protozoa dan Golongan Lain'],
                ['name' => 'Obat Antituberkulosis'],
                ['name' => 'Penisilin'],
                ['name' => 'Sefalosporin'],
                ['name' => 'Sulfonamid'],
                ['name' => 'Tetrasiklin'],
            ]
        ],
        [
            'name' => 'Antidotum & Zat Detoksifikasi Untuk Terapi Ketergantungan Zat', 'childs' => [
                ['name' => 'Antidotum dan Obat Detoksifiaksi'],
                ['name' => 'Obat Untuk Terapi Ketergantungan Zat'],
            ]
        ],
        [
            'name' => 'Hormon', 'childs' => [
                ['name' => 'Androgen dan Preparat Sintetiknya'],
                ['name' => 'Estrogen dan Progesteron Serta Preparat Sintetiknya'],
                ['name' => 'Hormon - Hormon Tropik dan Preparat Sintetiknya'],
                ['name' => 'Hormon Kortikosteroid'],
                ['name' => 'Kombinasi Hormon - Hormon Kelamin'],
                ['name' => 'Obat Lain yang Mempengaruhi Regulasi Hormonal'],
                ['name' => 'Preparat Anabolik']
            ]
        ],
        [
            'name' => 'Kontrasepsi', 'childs' => [
                ['name' => 'Kontrasepsi Lain'],
                ['name' => 'Kontrasepsi Oral'],
                ['name' => 'Kontrasepsi Suntik'],
            ]
        ],
        ['name' => 'Lartuan Intravena & Larutan Steril Lain'],
        [
            'name' => 'Mata', 'childs' => [
                ['name' => 'Antiinfeksi dan Antiseptik Mata'],
                ['name' => 'Antiseptik Mata Dengan Kortikosteroid'],
                ['name' => 'Kortikosteroid Mata'],
                ['name' => 'Obat Dekongestan, Anestesi, Antiinflamasi Mata'],
                ['name' => 'Obat Midriatikum'],
                ['name' => 'Obat Miotikom'],
                ['name' => 'Pelumas Mata'],
                ['name' => 'Preparat Antiglaukoma'],
                ['name' => 'Preparat Mata Golongan Lain'],
            ]
        ],
        [
            'name' => 'Nutrisi', 'childs' => [
                ['name' => 'Elektrolit'],
                ['name' => 'Produk Nutrisi Bayi'],
                ['name' => 'Produk Nutrisi Parenteral'],
                ['name' => 'Produk Nutrisi/Enteral'],
            ]
        ],
        [
                'name' => 'Obat Anestesi, Sediaan Bedah, dan Perawatan Luka', 'childs' => [
                ['name' => 'Anestesi Lokal dan Umum'],
                ['name' => 'Kasa Penutup Luka dan Perawatan Luka'],
                ['name' => 'Obat Penghambat Neuromuskular'],
            ]
        ],
        [
            'name' => 'Onkologi', 'childs' => [
                ['name' => 'Imunoterapi Kanker'],
                ['name' => 'Kemoterapi Sitotoksik'],
                ['name' => 'Perawatan Suportif'],
                ['name' => 'Terapi Hormon Untuk Kanker'],
                ['name' => 'Terapi Target Untuk Kanker'],
            ]
        ],
        [
            'name' => 'Perawatan Kulit &amp; Diri', 'childs' => [
                ['name' => 'Emolien, Pembersih Kulit, dan Pelindung Kulit'],
                ['name' => 'Perawatan Diri'],
                ['name' => 'Perawatan Rongga Mulut'],
            ]
        ],
        ['name' => 'Produk Terapeutik Lain'],
        [
            'name' => 'Sistem Endokrin &amp; Metabolik', 'childs' => [
                ['name' => 'Hormon Tiroid'],
                ['name' => 'Obat Antidiabetes'],
                ['name' => 'Obat Antiobesitas'],
                ['name' => 'Obat Antitiroid'],
                ['name' => 'Obat Metabolisme Golongan Lain'],
                ['name' => 'Obat Metabolisme Tulang'],
                ['name' => 'Preparat Insulin'],
            ]
        ],
        [
            'name' => 'Sistem Gastrointestinal &amp; Hepatobilier', 'childs' => [
                ['name' => 'Antasid, Obat Antirefluks, dan Antiulserasi'],
                ['name' => 'Antidiare'],
                ['name' => 'Antiemetik'],
                ['name' => 'Antispasmodik'],
                ['name' => 'Digestan'],
                ['name' => 'Kolagogum, Kolelitolitik, dan Hepatoprotektor'],
                ['name' => 'Laksatif, Pencahar'],
                ['name' => 'Obat Gastrointestinal Lain'],
                ['name' => 'Preparat Anorektal'],
                ['name' => 'Regulator GIT Antiflatulen & Antiinflamasi']
            ]
        ],
        [
            'name' => 'Sistem Kardiovaskular &amp; Hematopoietik', 'childs' => [
                ['name' => 'Antagonis Angiotensin II'],
                ['name' => 'Antagonis Kalsium'],
                ['name' => 'Antidiuretik'],
                ['name' => 'Antihipertensi Golongan Lain'],
                ['name' => 'Antikoagulan, Antiplatelet, dan Fibrinolitik (Trombolitik)'],
                ['name' => 'Diuretik'],
                ['name' => 'Hemostatik'],
                ['name' => 'Obat AntianginaAC Inhibitor/Direct Renin Inhibitor'],
                ['name' => 'Obat Dislipidemia'],
                ['name' => 'Obat Hematopoietik'],
                ['name' => 'Obat Hemorheologi'],
                ['name' => 'Obat Jantung'],
                ['name' => 'Obat Kardiovaskular Golongan Lain'],
                ['name' => 'Penyekat Beta'],
                ['name' => 'Preparat Flebitis, dan Varises'],
                ['name' => 'Vasodilator Perifer dan Aktivator Serebral'],
                ['name' => 'Vasokonstriktor']
            ]
        ],
        [
            'name' => 'Sistem Kemih Kelamin', 'childs' => [
                ['name' => 'Antiseptik Saluran Kemih'],
                ['name' => 'Obat Saluran Kemih Kelamin Golongan Lain'],
                ['name' => 'Obat Untuk Disfungsi Ereksi dan Gangguan Ejakulasi'],
                ['name' => 'Obat Untuk Penyakit Saluran Kemih dan Prostat'],
                ['name' => 'Obat yang Bekerja Pada Uterus'],
                ['name' => 'Preparat Untuk Masalah Vagina']
            ]
        ],
        [
            'name' => 'Sistem Muskuloskeletal', 'childs' => [
                ['name' => 'Disease - Modifying Anti - Reumatic Drugs (DMARD)'],
                ['name' => 'Enzim Antiinflamasi'],
                ['name' => 'Obat Gangguan Neuromuskular'],
                ['name' => 'Obat Hiperurisemia dan Gout'],
                ['name' => 'Obat Lain yang Bekerja Pada Sistem Muskuloskeletal'],
                ['name' => 'Relaksan Otot']
            ]
        ],
        [
            'name' => 'Sistem Pernapasan', 'childs' => [
                ['name' => 'Dekongestan Nasal dan Preparat Nasal Lain'],
                ['name' => 'Obat Batuk dan Pilek'],
                ['name' => 'Obat Sistem Pernapasan Golongan Lain'],
                ['name' => 'Preparat Antiasma dan PPOK'],
                ['name' => 'Stimulan Saluran Pernapasan']
            ]
        ],
        [
            'name' => "Sistem Saraf Pusat", 'childs' => [
                ['name' => 'Analgesik (Non Opiat) dan Antipiretik'],
                ['name' => 'Analgesik (Opiat'],
                ['name' => 'Ansiolitik'],
                ['name' => 'Antidepresan'],
                ['name' => 'Antikonvulsan'],
                ['name' => 'Antipsikotik'],
                ['name' => 'Hipnotik dan Sedativa'],
                ['name' => 'Nootropik dan Neurotonik/Neurotropik'],
                ['name' => 'Obat Anti Parkinson'],
                ['name' => 'Obat Anti Vertigo'],
                ['name' => 'Obat Antiinflamasi Non Steroid (OAINS'],
                ['name' => 'Obat Penyakit Neurodegeneratif'],
                ['name' => 'Obat SSP Golongan Lain dan Obat ADHD'],
                ['name' => 'Obat Untuk Nyeri Neuropatik'],
                ['name' => 'Preparat Antimigren'],
            ]
        ],
        [
            'name' => 'Suplemen Kesehatan &amp; Makanan', 'childs' => [
                ['name' => 'Perangsa Nafsu Makan'],
                ['name' => 'Produk Kesehatan Komplementer Lain'],
                ['name' => 'Suplemen dan Terapi Penunjang'],
            ]
        ],
        [
            'name' => 'Telinga & Mulut/Tenggorokan', 'childs' => [
                ['name' => 'Antiinfeksi dan Antiseptik Telinga'],
                ['name' => 'Antiseptik Telinga dan Kortikosteroid'],
                ['name' => 'Kortikosteroid Telinga'],
                ['name' => 'Obat Antiinfeksi Orofaring'],
                ['name' => 'Obat Untuk Tukak dan Inflamasi Rongga Mulut'],
                ['name' => 'Preparat Telinga Golongan Lain'],
            ]
        ],
        [
            'name' => 'Terapi Untuk Kulit', 'childs' => [
                ['name' => 'Antibiotik Topikal'],
                ['name' => 'Antihistamin/Antipruritus Topikal'],
                ['name' => 'Antiinfeksi Topikal Dengan Kortikosteroid'],
                ['name' => 'Antijamur dan Antiparasit Topikal'],
                ['name' => 'Antiseptik dan Desinfektan Kulit'],
                ['name' => 'Antivirus Topikal'],
                ['name' => 'Kortikosteroid Topikal'],
                ['name' => 'Obat Kulit Lain'],
                ['name' => 'Obat Kutil dan Kapalan (Kalus)'],
                ['name' => 'Preparat Akne'],
                ['name' => 'Preparat Psoriasis, Seboroik, dan Iktiosis'],
            ]
        ],
        [
            'name' => 'Vitamin &amp; Mineral', 'childs' => [
                ['name' => 'Kalsium/Dengan Vitamin'],
                ['name' => 'Vitamin A, D, dan E'],
                ['name' => 'Vitamin B Kompleks/Dengan Vitamin C'],
                ['name' => 'Vitamin C'],
                ['name' => 'Vitamin dan Mineral (Geriatrik)'],
                ['name' => 'Vitamin dan Mineral (Pediatrik)'],
                ['name' => 'Vitamin dan Mineral (Untuk Masa Hamil dan Nifas)/Antianemia'],
                ['name' => 'Vitamin dan/atau Mineral'],
            ]
        ]
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->datas as $data) {
            app(config('app.contracts.TherapeuticClass'))->prepareStoreTherapeuticClass(
                $this->requestDTO(config('app.contracts.TherapeuticClassData'), $data)
            );
        }
    }
}
