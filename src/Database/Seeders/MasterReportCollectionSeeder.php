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
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $collections = [
            // ===== PATIENT DATA RECAP REPORT =====
            // Model: Patient -> ViewPatient
            // Response structure: { id, name, medical_record, patient_type, people: {...}, card_identity: {...} }
            [
                "name"           => "Laporan Data Pasien",
                "label"          => "PATIENT_DATA_RECAP_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns" => [
                    ["key" => "medical_record", "label" => "No RM"],
                    ["key" => "people.card_identity.nik", "label" => "NIK"],
                    ["key" => "name", "label" => "Nama Pasien"],
                    ["key" => "patient_type.name", "label" => "Jenis Pasien"],
                    ["key" => "people.phone_1", "label" => "Kontak 1"],
                    ["key" => "people.phone_2", "label" => "Kontak 2"],
                    ["key" => "people.age", "label" => "Usia"],
                    ["key" => "people.sex", "label" => "Jenis Kelamin"],
                    ["key" => "people.pob", "label" => "Tempat Lahir"],
                    ["key" => "people.dob", "label" => "Tanggal Lahir"]
                    // ["key" => "payer.name", "label" => "Payer/Asuransi"],
                ]
            ],

            // ===== VISIT PATIENT REPORT =====
            // Model: VisitPatient -> ViewVisitPatient
            // Response structure: { id, visit_code, visited_at, patient: {...}, practitioner_evaluation: {...}, visit_registration: {...} }
            [
                "name"           => "Laporan Kunjungan Pasien",
                "label"          => "VISIT_PATIENT_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    ["key" => "visited_at", "label" => "Tanggal Berkunjung"],
                    ["key" => "visit_code", "label" => "Kode Kunjungan"],
                    ["key" => "practitioner_evaluation.name", "label" => "Petugas Pendaftaran"],
                    ["key" => "patient.medical_record", "label" => "No RM"],
                    ["key" => "patient.people.card_identity.nik", "label" => "NIK"],
                    ["key" => "patient.name", "label" => "Nama Pasien"],
                    ["key" => "patient.patient_type.name", "label" => "Jenis Pasien"],
                    ["key" => "patient.people.phone_1", "label" => "Kontak 1"],
                    ["key" => "patient.people.age", "label" => "Usia"],
                    ["key" => "patient.people.sex", "label" => "Jenis Kelamin"],
                    ["key" => "patient.people.pob", "label" => "Tempat Lahir"],
                    ["key" => "patient.people.dob", "label" => "Tanggal Lahir"],
                    ["key" => "patient_type_service.name", "label" => "Tipe Layanan"],
                    // ["key" => "payer.name", "label" => "Payer/Asuransi"],
                    ["key" => "status", "label" => "Status"],
                ]
            ],

            // ===== TRANSACTION BILLING REPORT =====
            // Model: Billing -> ViewBilling
            // Response structure: { id, billing_code, has_transaction: {...}, author: {...}, cashier: {...}, debt, amount }
            [
                "name"           => "Laporan Tagihan Pasien",
                "label"          => "TRANSACTION_BILLING_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns" => [
                    ["key" => "billing_code", "label" => "Kode Billing"],
                    ["key" => "has_transaction.consument.name", "label" => "Nama Pasien"],
                    ["key" => "amount", "label" => "Total Tagihan"],
                    ["key" => "discount", "label" => "Diskon"],
                    ["key" => "money_paid", "label" => "Dibayar"],
                    ["key" => "debt", "label" => "Sisa Tagihan"],
                    ["key" => "author.name", "label" => "Pembuat"],
                    ["key" => "cashier.name", "label" => "Kasir"],
                    ["key" => "reported_at", "label" => "Tanggal Laporan"],
                    ["key" => "created_at", "label" => "Tanggal Dibuat"],
                ]
            ],

            // ===== PAYMENT RECAP REPORT =====
            // Model: Invoice -> ViewInvoice
            // Response structure: { id, invoice_code, billing: {...}, payment_summary: {...}, paid_at }
            [
                "name"           => "Laporan Pembayaran Pasien",
                "label"          => "PAYMENT_RECAP_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns" => [
                    ["key" => "invoice_code", "label" => "Kode Invoice"],
                    ["key" => "billing.billing_code", "label" => "Kode Billing"],
                    ["key" => "billing.has_transaction.consument.name", "label" => "Nama Pasien"],
                    ["key" => "debt", "label" => "Total Pembayaran"],
                    ["key" => "paid", "label" => "Dibayar"],
                    ["key" => "payer.name", "label" => "Payer"],
                    ["key" => "author.name", "label" => "Pembuat"],
                    ["key" => "paid_at", "label" => "Tanggal Bayar"],
                    ["key" => "created_at", "label" => "Tanggal Dibuat"],
                ]
            ],

            // ===== MEDIC OBSERVATION RECAP REPORT =====
            // Model: VisitExamination -> ViewVisitExamination
            // Response structure: { id, visit_examination_code, patient: {...}, visit_registration: {...}, sign_off_at }
            [
                "name"           => "Laporan Observasi Pasien",
                "label"          => "MEDIC_OBSERVATION_RECAP_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns" => [
                    ["key" => "visit_examination_code", "label" => "Kode Pemeriksaan"],
                    ["key" => "patient.name", "label" => "Nama Pasien"],
                    ["key" => "patient.medical_record", "label" => "No RM"],
                    ["key" => "patient.people.sex", "label" => "Jenis Kelamin"],
                    ["key" => "patient.people.age", "label" => "Usia"],
                    ["key" => "visit_registration.medic_service.name", "label" => "Layanan"],
                    ["key" => "visit_registration.warehouse.name", "label" => "Lokasi"],
                    ["key" => "visit_registration.visit_patient.visited_at", "label" => "Tanggal Kunjungan"],
                    ["key" => "is_prescription_completed", "label" => "Resep Selesai"],
                    ["key" => "sign_off_at", "label" => "Tanggal Sign Off"],
                    ["key" => "created_at", "label" => "Tanggal Dibuat"],
                ]
            ],

            // ===== REFUND DISCOUNT RECAP REPORT =====
            // Model: Refund -> ViewRefund (extends ViewBaseWalletTransaction)
            // Response structure: { id, code, name, invoice: {...}, wallet_transaction: {...}, refund_items: [...] }
            // [
            //     "name"           => "Laporan Refund dan Diskon",
            //     "label"          => "REFUND_DISCOUNT_RECAP_REPORT",
            //     "data_type"      => "DataTable",
            //     "filters"        => [
            //         "default_filters" => [
            //             [
            //                 "label"          => "Jenis Filter",
            //                 "key"            => "value",
            //                 "type"           => "Select",
            //                 "component_name" => null,
            //                 "default_value"  => "TRANSACTION",
            //                 "attribute"      => null,
            //                 "options"        => ["TRANSACTION"]
            //             ],
            //         ],
            //         "additional_filters" => []
            //     ],
            //     "columns" => [
            //         ["key" => "code", "label" => "Kode Refund"],
            //         ["key" => "name", "label" => "Nama"],
            //         ["key" => "invoice.invoice_code", "label" => "Kode Invoice"],
            //         ["key" => "invoice.billing.billing_code", "label" => "Kode Billing"],
            //         ["key" => "invoice.billing.has_transaction.reference.name", "label" => "Nama Pasien"],
            //         ["key" => "invoice.billing.has_transaction.reference.medical_record", "label" => "No RM"],
            //         ["key" => "wallet_transaction.nominal", "label" => "Nominal Refund"],
            //         ["key" => "wallet_transaction.previous_balance", "label" => "Saldo Sebelum"],
            //         ["key" => "wallet_transaction.current_balance", "label" => "Saldo Sesudah"],
            //         ["key" => "channel.name", "label" => "Channel"],
            //         ["key" => "created_at", "label" => "Tanggal Refund"],
            //     ]
            // ],

            // ===== DIAGNOSIS RECAP REPORT =====
            // Model: PatientIllness -> ViewPatientIllness
            // Response structure: { id, name, patient: {...}, disease: {...}, classification_disease: {...} }
            [
                "name"           => "Laporan Diagnosis Pasien",
                "label"          => "DIAGNOSIS_RECAP_REPORT",
                "data_type"      => "DataTable",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"          => "Jenis Filter",
                            "key"            => "value",
                            "type"           => "Select",
                            "component_name" => null,
                            "default_value"  => "TRANSACTION",
                            "attribute"      => null,
                            "options"        => ["TRANSACTION"]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns" => [
                    ["key" => "name", "label" => "Nama Diagnosis"],
                    ["key" => "disease.code", "label" => "Kode ICD"],
                    ["key" => "disease.name", "label" => "Nama Penyakit"],
                    ["key" => "classification_disease.code", "label" => "Kode Klasifikasi"],
                    ["key" => "classification_disease.name", "label" => "Klasifikasi Penyakit"],
                    ["key" => "patient.name", "label" => "Nama Pasien"],
                    ["key" => "patient.medical_record", "label" => "No RM"],
                    ["key" => "patient.people.sex", "label" => "Jenis Kelamin"],
                    ["key" => "patient.people.age", "label" => "Usia"],
                    ["key" => "patient.people.dob", "label" => "Tanggal Lahir"],
                    ["key" => "created_at", "label" => "Tanggal Dibuat"],
                ]
            ]
        ];

        $schema = app(config('app.contracts.MasterReport'));
        foreach ($collections as $collect) {
            $schema->prepareStoreMasterReport($this->requestDTO(config('app.contracts.MasterReportData'),$collect));
        }
    }
}
