<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Zahzah\ModulePatient\Enums\VisitPatient\VisitStatus;
use Zahzah\ModulePatient\Enums\VisitRegistration\RegistrationStatus;

class MasterReportCollectionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $collections = [
            [
                "name"           => "MCU REPORT",
                "flag"           => "MCU_REPORT",
                "data_type"      => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => [
                        [
                            "label"   => "Payer",
                            "key"     => "search_payer_id",
                            "type"    => "DropDownPayer",
                            "options" => []
                        ],
                        [
                            "label"   => "Agent",
                            "key"     => "search_agent_id",
                            "type"    => "DropDownAgent",
                            "options" => []
                        ],
                    ]
                ],
                "columns"        => [
                    [
                        "key"     => "date",
                        "label"   => "Transaction Date"
                    ],
                    [
                        "key"     => "visit_number",
                        "label"   => "Visit Number"
                    ],
                    [
                        "key"     => "no_rm",
                        "label"   => "No. RM"
                    ],
                    [
                        "key"     => "patient_name",
                        "label"   => "Patient Name"
                    ],
                    [
                        "key"     => "gender",
                        "label"   => "Gender"
                    ],
                    [
                        "key"     => "dob",
                        "label"   => "DOB"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"     => "doctor_name",
                        "label"   => "Doctor Name"
                    ],
                    [
                        "key"     => "mcu_package_name",
                        "label"   => "MCU Package Name"
                    ],
                    [
                        "key"     => "patient_type_name",
                        "label"   => "Patient Type"
                    ],
                    [
                        "key"     => "payer_name",
                        "label"   => "Payer Name"
                    ],
                    [
                        "key"     => "agent_name",
                        "label"   => "Agent Name"
                    ],
                    [
                        "key"     => "total_amount",
                        "label"   => "Total"
                    ],
                ]
            ],
            [
                "name"              => "SALES TRANSACTION DETAIL",
                "flag"              => "SALES_TRANSACTION_DETAIL_REPORT",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Transaction Number"
                    ],
                    [
                        "key"   => "transaction_status",
                        "label" => "Transaction Status"
                    ],
                    [
                        "key"   => "transaction_type",
                        "label" => "Transaction Type"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "patient_type",
                        "label" => "Patient Type"
                    ],
                    [
                        "key"   => "poly",
                        "label" => "Poli"
                    ],
                    [
                        "key"   => "visit_number",
                        "label" => "Visit Number"
                    ],
                    [
                        "key"   => "service_name",
                        "label" => "Service Name"
                    ],
                    [
                        "key"   => "service_total",
                        "label" => "Service Total"
                    ]
                ]
            ],
            [
                "name"              => "ALL FINANCIAL TRANSACTION REPORT",
                "flag"              => "All_FINANCIAL_TRANSACTION_REPORT",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => [
                        [
                            "label"   => "Payment Method",
                            "key"     => "payment_mode",
                            "type"    => "DropDownPaymentMethod",
                            "options" => []
                        ]
                        // [
                        //     "label"   => "Type of Service",
                        //     "key"     => "type_of_service",
                        //     "type"    => "DropDownTypeOfService",
                        //     "options" => []
                        // ],
                    ]
                ],
                "columns"  => [
                    [
                        "key"   => "transaction_date",
                        "label" => "Date"
                    ],
                    [
                        "key"   => "visit_code",
                        "label" => "Visit Code"
                    ],
                    [
                        "key"   => "rm_number",
                        "label" => "RM"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patien Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "type_of_service",
                        "label" => "Type of Service"
                    ],
                    [
                        "key"   => "patient_type",
                        "label" => "Patient Type"
                    ],
                    [
                        "key"   => "payment_mode",
                        "label" => "Payment Mode"
                    ],
                    [
                        "key"   => "cashier",
                        "label" => "Cashier"
                    ],
                    [
                        "key"   => "document_number",
                        "label" => "Document Number"
                    ],
                    [
                        "key"   => "receipt_amount",
                        "label" => "Receipt Amount"
                    ]
                ]
            ],
            [
                "name"              => "CANCELATION DETAILS REPORT",
                "flag"              => "CANCELATION_DETAILS_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Tanggal"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No. RM"
                    ],
                    [
                        "key"   => "no_visit",
                        "label" => "Visit Number"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Nama Pasien"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"     => "polyname",
                        "label"   => "Poly"
                    ],
                    [
                        "key"   => "nominal",
                        "label" => "Nominal Cancel"
                    ],
                    [
                        "key"   => "comment",
                        "label" => "Komentar"
                    ],
                    [
                        "key"   => "cashier",
                        "label" => "Kasir"
                    ]
                ]
            ],
            [
                "name"              => "MARKETING REPORT",
                "flag"              => "MARKETING_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"           => [
                    [
                        "key"     => "no_rm",
                        "label"   => "No. RM"
                    ],
                    [
                        "key"     => "patient_name",
                        "label"   => "Nama Pasien"
                    ],
                    [
                        "key"     => "birthday",
                        "label"   => "Tanggal Lahir"
                    ],
                    [
                        "key"     => "email",
                        "label"   => "Email"
                    ],
                    [
                        "key"     => "phone",
                        "label"   => "No. Telepon"
                    ],
                    [
                        "key"     => "address",
                        "label"   => "Alamat"
                    ]
                ]
            ],
            [
                "name"              => "DIAGNOSIS REPORT",
                "flag"              => "DIAGNOSIS_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"           => [
                    [
                        "key"     => "patient_name",
                        "label"   => "Nama Pasien"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"     => "no_rm",
                        "label"   => "No. RM"
                    ],
                    [
                        "key"     => "nik",
                        "label"   => "NIK"
                    ],
                    [
                        "key"     => "gender",
                        "label"   => "Jenis Kelamin"
                    ],
                    [
                        "key"     => "date_of_birth",
                        "label"   => "Tgl Lahir"
                    ],
                    [
                        "key"     => "address",
                        "label"   => "Alamat"
                    ],
                    [
                        "key"     => "visit_reason",
                        "label"   => "Alasan Kunjungan"
                    ],
                    [
                        "key"     => "consulting_doctor",
                        "label"   => "Konsultasi Dokter"
                    ],
                    [
                        "key"     => "diagnosis_code",
                        "label"   => "Kode Diagnosa"
                    ],
                    [
                        "key"     => "diagnosis_name",
                        "label"   => "Nama Diagnosa"
                    ],
                    [
                        "key"     => "diagnosis_type",
                        "label"   => "Tipe Diagnosa"
                    ]
                ]
            ],
            [
                "name"              => "COUNTING VISIT PATIENT",
                "flag"              => "COUNTING_VISIT_PATIENT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Tree_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"           => [
                    [
                        "key"     => "poly_name",
                        "label"   => "Poly Name"
                    ],
                    [
                        "key"   => "visit_count",
                        "label" => "Hitungan Kunjungan"
                    ],
                    [
                        "key"   => "new_visit_count",
                        "label" => "Jumlah Kunjungan Baru"
                    ],
                    [
                        "key"   => "visit_male_count",
                        "label" => "Kunjungan Pria"
                    ],
                    [
                        "key"   => "visit_female_count",
                        "label" => "Kunjungan Wanita"
                    ],
                    [
                        "key"   => "repeater_visit_count",
                        "label" => "Jumlah Kunjungan Berulang"
                    ]
                ]
            ],
            [
                "name"              => "RM CONTROLLER REPORT",
                "flag"              => "VISIT_PATIENT_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"           => [
                    [
                        "key"     => "Date",
                        "label"   => "Date"
                    ],
                    [
                        "key"     => "visit_number",
                        "label"   => "Visit Number"
                    ],
                    [
                        "key"     => "no_rm",
                        "label"   => "No. RM"
                    ],
                    [
                        "key"     => "first_name",
                        "label"   => "First Name"
                    ],
                    [
                        "key"     => "last_name",
                        "label"   => "Last Name"
                    ],
                    [
                        "key"     => "patient_name",
                        "label"   => "Full Name"
                    ],
                    [
                        "key"     => "poly_name",
                        "label"   => "Poly Name"
                    ],
                    [
                        "key"     => "dob",
                        "label"   => "DOB"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"     => "patient_type_name",
                        "label"   => "Patient Type name"
                    ],
                    [
                        "key"     => "father_name",
                        "label"   => "Father Name"
                    ],
                    [
                        "key"     => "emergency_phone",
                        "label"     => "Emergency Phone"
                    ]
                ]
            ],
            [
                "name"              => "COGS DETAIL REPORT SUMMARY",
                "flag"              => "COGS_DETAIL_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Transaction Number"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "patient_type",
                        "label" => "Patient Type"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "item_number",
                        "label" => "Item Number"
                    ],
                    [
                        "key"   => "item_name",
                        "label" => "Item Name"
                    ],
                    [
                        "key"   => "item_group",
                        "label" => "Item Group"
                    ],
                    [
                        "key"   => "batch_no",
                        "label" => "Batch Number"
                    ],
                    [
                        "key"   => "quantity",
                        "label" => "Quantity"
                    ],
                    [
                        "key"   => "sales_price",
                        "label" => "Sales Price"
                    ],
                    [
                        "key"   => "cost",
                        "label" => "Cost"
                    ],
                    [
                        "key"   => "extended_price",
                        "label" => "Extended Price"
                    ]
                ]
            ],
            [
                "name"              => "DEFERRED DETAIL REPORT",
                "flag"              => "DEFERRED_DETAIL_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "payer_name",
                        "label" => "Payer Name"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Transaction Number"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "visit_number",
                        "label" => "Visit Number"
                    ],
                    [
                        "key"   => "service_name",
                        "label" => "Service Name"
                    ],
                    [
                        "key"   => "service_total",
                        "label" => "Service Total"
                    ],
                    [
                        "key"   => "bill_total",
                        "label" => "Bill Total"
                    ]
                ]
            ],
            [
                "name"              => "DISCOUNT DETAIL REPORT",
                "flag"              => "DISCOUNT_DETAIL_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters" => [
                    [
                        "label"   => "Jenis Filter",
                        "key"     => "date_type",
                        "type"    => "Select",
                        "options" => [
                            "TRANSACTION",
                            "DAILY",
                            "MONTHLY",
                            "YEARLY"
                        ]
                    ]
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Transaction Number"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "payment_method",
                        "label" => "Payment Method"
                    ],
                    [
                        "key"   => "transaction_total",
                        "label" => "Transaction Total"
                    ],
                    [
                        "key"   => "discount_amount",
                        "label" => "Discount Amount"
                    ],
                    [
                        "key"   => "net_invoice",
                        "label" => "Net Invoice"
                    ],
                    [
                        "key"   => "discount_notes",
                        "label" => "Discount Notes"
                    ],
                    [
                        "key"   => "discount_by",
                        "label" => "Discount By"
                    ]
                ]
            ],
            [
                "name"              => "REFUND TRANSACTION REPORT",
                "flag"              => "REFUND_TRANSACTION_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => [
                        [
                            "label"   => "Payment Method",
                            "key"     => "payment_mode",
                            "type"    => "DropDownPaymentMethod",
                            "options" => []
                        ]
                    ]
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Billing Number"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"   => "phone_1",
                        "label" => "Phone 1"
                    ],
                    [
                        "key"   => "phone_2",
                        "label" => "Phone 2"
                    ],
                    [
                        "key"   => "payment_method",
                        "label" => "Payment Method"
                    ],
                    [
                        "key"   => "transaction_total",
                        "label" => "Transaction Total"
                    ],
                    [
                        "key"   => "refund_amount",
                        "label" => "Refund Amount"
                    ],
                    [
                        "key"   => "refund_method",
                        "label" => "Refund Method"
                    ],
                    [
                        "key"   => "net_invoice",
                        "label" => "Net Invoice"
                    ],
                    [
                        "key"   => "refund_notes",
                        "label" => "Refund Notes"
                    ],
                    [
                        "key"   => "refund_by",
                        "label" => "Refund By"
                    ]
                ]
            ],
            [
                "name"              => "PURCHASE ITEM DETAIL REPORT",
                "flag"              => "PURCHASE_ITEM_DETAIL_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Creation Date"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Pengadaan Number"
                    ],
                    [
                        "key"   => "supplier_name",
                        "label" => "Supplier Name"
                    ],
                    [
                        "key"   => "status",
                        "label" => "Status"
                    ],
                    [
                        "key"   => "received_date",
                        "label" => "Received Date"
                    ],
                    [
                        "key"   => "item_number",
                        "label" => "Item Number"
                    ],
                    [
                        "key"   => "item_name",
                        "label" => "Item Name"
                    ],
                    [
                        "key"   => "item_group",
                        "label" => "Item Group"
                    ],
                    [
                        "key"   => "batch_no",
                        "label" => "Batch No"
                    ],
                    [
                        "key"   => "quantity",
                        "label" => "Quantity"
                    ],
                    [
                        "key"   => "purchase_price",
                        "label" => "Purchase Price"
                    ],
                    [
                        "key"   => "tax",
                        "label" => "Tax"
                    ],
                    [
                        "key"   => "extended_cost",
                        "label" => "Extended Cost"
                    ]
                ]
            ],
            [
                "name"              => "STOCK LEDGER REPORT",
                "flag"              => "STOCK_LEDGER_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "item_number",
                        "label" => "Item Number"
                    ],
                    [
                        "key"   => "item_name",
                        "label" => "Item Name"
                    ],
                    [
                        "key"   => "transaction_number",
                        "label" => "Transaction Number"
                    ],
                    [
                        "key"   => "transaction_date",
                        "label" => "Transaction Date"
                    ],
                    [
                        "key"   => "transaction_type",
                        "label" => "Transaction Type"
                    ],
                    [
                        "key"   => "opening_bal",
                        "label" => "Opening Bal"
                    ],
                    [
                        "key"   => "qty_in",
                        "label" => "Qty In"
                    ],
                    [
                        "key"   => "qty_out",
                        "label" => "Qty Out"
                    ],
                    [
                        "key"   => "closing_bal",
                        "label" => "Closing Bal"
                    ],
                    [
                        "key"   => "batch_no",
                        "label" => "Batch No"
                    ],
                    [
                        "key"   => "purchase_price",
                        "label" => "Purchase Price"
                    ],
                    [
                        "key"   => "sale_price",
                        "label" => "Sale Price"
                    ]
                ]
            ],
            [
                "name"              => "STOCK STATEMENT REPORT",
                "flag"              => "STOCK_STATEMENT_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "item_number",
                        "label" => "Item Number"
                    ],
                    [
                        "key"   => "item_name",
                        "label" => "Item Name"
                    ],
                    [
                        "key"   => "batch",
                        "label" => "Batch"
                    ],
                    [
                        "key"   => "unit",
                        "label" => "Unit"
                    ],
                    [
                        "key"   => "quantity",
                        "label" => "Quantity"
                    ],
                    [
                        "key"   => "purhase_price",
                        "label" => "Purchase Price"
                    ],
                    [
                        "key"   => "sale_price",
                        "label" => "Sale Price"
                    ],
                    [
                        "key"   => "supplier",
                        "label" => "Supplier"
                    ],
                    [
                        "key"   => "expiry_date",
                        "label" => "Expiry Date"
                    ],
                    [
                        "key"   => "received_date",
                        "label" => "Received Date"
                    ]
                ]
            ],
            [
                "name"              => "UNCLOSED VISIT REPORT",
                "flag"              => "UNCLOSED_VISIT_REPORT",
                "default_filter"    => "TRANSACTION",
                "data_type"         => "Data_Table",
                "filters"        => [
                    "default_filters" => [
                        [
                            "label"         => "Jenis Filter",
                            "key"           => "date_type",
                            "type"          => "Select",
                            "default_value" => "TRANSACTION",
                            "options" => [
                                "TRANSACTION",
                                "DAILY",
                                "MONTHLY",
                                "YEARLY"
                            ]
                        ],
                    ],
                    "additional_filters" => []
                ],
                "columns"  => [
                    [
                        "key"   => "date",
                        "label" => "Visit Date"
                    ],
                    [
                        "key"   => "visit_number",
                        "label" => "Visit Number"
                    ],
                    [
                        "key"   => "patient_name",
                        "label" => "Patient Name"
                    ],
                    [
                        "key"     => "phone_1",
                        "label"   => "Phone 1"
                    ],
                    [
                        "key"     => "phone_2",
                        "label"   => "Phone 2"
                    ],
                    [
                        "key"   => "no_rm",
                        "label" => "No RM"
                    ],
                    [
                        "key"   => "poli",
                        "label" => "Poli"
                    ],
                    [
                        "key"   => "service_name",
                        "label" => "Service Name"
                    ],
                    [
                        "key"   => "service_status",
                        "label" => "Service Status"
                    ]
                ]
            ],
        ];

        $report_model = app(config('database.models.MasterReport'));
        foreach ($collections as $collect) {
            $report_data_model = $report_model->updateOrCreate([
                "flag" => $collect['flag']
            ], [
                "name" => $collect['name']
            ]);

            $report_data_model->filters = $collect['filters'];
            $report_data_model->columns = $collect['columns'];
            $report_data_model->save();
        }
    }
}
