<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;


class MasterReportCollectionSeeder extends Seeder
{
    use HasRequestData;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    // 'name'        => 'Kunjungan Pasien', 
    // 'name'        => 'Transaksi Pasien', 
    // 'name'        => 'Rekap Pembayaran', 
    // 'name'        => 'Aktivitas Medis', 
    // 'name'        => 'Refund dan Diskon', 
    // 'name'        => 'Laporan Diagnosa', 
    // 'name'        => 'Kunjungan Belum Ditandatangani',
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $collections = [
            [
                "name"           => "Rekap Data Pasien",
                "label"           => "PATIENT_DATA_RECAP_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION"
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => [
                    ]
                ],
                "columns"        => [
                    [
                        "key"     => "medical_record",
                        "label"   => "No RM"
                    ],
                    [
                        "key"     => "people.card_identity.nik",
                        "label"   => "NIK"
                    ],
                    [
                        "key"     => "name",
                        "label"   => "Nama Pasien"
                    ],
                    [
                        "key"     => "patient_type.name",
                        "label"   => "Jenis Pasien"
                    ],
                    [
                        "key"     => "people.phone_1",
                        "label"   => "Kontak 1"
                    ],
                    [
                        "key"     => "people.phone_2",
                        "label"   => "Kontak 2"
                    ],
                    [
                        "key"     => "people.age",
                        "label"   => "Usia"
                    ],
                    [
                        "key"     => "people.gender",
                        "label"   => "Jenis Kelamin"
                    ],
                    [
                        "key"     => "no_rm",
                        "label"   => "No RM"
                    ],
                    [
                        "key"     => "people.pob",
                        "label"   => "Tempat Lahir"
                    ],
                    [
                        "key"     => "people.dob",
                        "label"   => "Tanggal Lahir"
                    ]
                ]
            ],
            [
                "name"              => "Kunjungan Pasien",
                "label"              => "VISIT_PATIENT_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "visited_at",
                        "label" => "Tanggal Berkunjung"
                    ],
                    [
                        "key"   => "visit_code",
                        "label" => "Kode Kunjungan"
                    ],
                    [
                        "key"   => "practitioner_evaluation.name",
                        "label" => "Petugas Pendaftaran"
                    ],
                    [
                        "key"     => "medical_record",
                        "label"   => "No RM"
                    ],
                    [
                        "key"     => "people.card_identity.nik",
                        "label"   => "NIK"
                    ],
                    [
                        "key"     => "name",
                        "label"   => "Nama Pasien"
                    ],
                    [
                        "key"     => "patient_type.name",
                        "label"   => "Jenis Pasien"
                    ],
                    [
                        "key"     => "people.phone_1",
                        "label"   => "Kontak 1"
                    ],
                    [
                        "key"     => "people.phone_2",
                        "label"   => "Kontak 2"
                    ],
                    [
                        "key"     => "people.age",
                        "label"   => "Usia"
                    ],
                    [
                        "key"     => "people.gender",
                        "label"   => "Jenis Kelamin"
                    ],
                    [
                        "key"     => "no_rm",
                        "label"   => "No RM"
                    ],
                    [
                        "key"     => "people.pob",
                        "label"   => "Tempat Lahir"
                    ],
                    [
                        "key"     => "people.dob",
                        "label"   => "Tanggal Lahir"
                    ]
                ]
            ],
            [
                "name"              => "Transaksi Billing",
                "label"              => "TRANSACTION_BILLING_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ]
            ],
            [
                "name"              => "Rekap Pembayaran",
                "label"              => "PAYMENT_RECAP_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ]
            ],
            [
                "name"              => "Rekap Observasi",
                "label"              => "MEDIC_OBSERVATION_RECAP_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ]
            ],
            [
                "name"              => "Refund dan Diskon",
                "label"              => "REFUND_DISCOUNT_RECAP_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ]
            ],
            [
                "name"              => "Diagnosa Pasien",
                "label"              => "DIAGNOSIS_RECAP_REPORT",
                "data_type"         => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "value",
                            "type"          => "Select",
                            "component_name" => null,
                            "default_value" => "TRANSACTION",
                            "attribute" => null,
                            "options" => [
                                "TRANSACTION",
                                // "DAILY",
                                // "MONTHLY",
                                // "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ]
            ]
        ];

        $schema = app(config('app.contracts.MasterReport'));
        foreach ($collections as $collect) {
            $schema->prepareStoreMasterReport($this->requestDTO(config('app.contracts.MasterReportData'),$collect));
        }
    }
}
