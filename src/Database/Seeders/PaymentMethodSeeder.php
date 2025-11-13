<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentMethodData;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    use HasRequestData;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            [
                'name' => 'CASH',
                'flag' => 'PaymentMethod',
                'label' => 'TUNAI',
            ],
            [
                'name' => 'Deposit',
                'flag' => 'PaymentMethod',
                'label' => 'DEPOSIT',
            ],
            [
                'name' => 'BANK TRANSFER',
                'flag' => 'PaymentMethod',
                'label' => 'NON TUNAI',
                'form' => [
                    'label' => 'BANK TRANSFER',
                    'name'  => 'Pembayaran Bank',
                    'dynamic_forms'  => [
                        [
                            'label'          => 'Nama Bank',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Nomor Rekening',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Atas Nama',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Kode Transaksi',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'CREDIT CARD',
                'flag' => 'PaymentMethod',
                'label' => 'NON TUNAI',
                'form' => [
                    'label' => 'CREDIT CARD',
                    'name'  => 'Pembayaran Kredit',
                    'dynamic_forms'  => [
                        [
                            'label'          => 'Nomor Kartu',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Tipe Kartu',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Tanggal Kadaluarsa',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ],
                        [
                            'label'          => 'Kode Transaksi',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'DEBIT CARD',
                'flag' => 'PaymentMethod',
                'label' => 'NON TUNAI',
            ],
            [
                'name' => 'E-MONEY',
                'flag' => 'PaymentMethod',
                'label' => 'NON TUNAI',
                'form' => [
                    'label' => 'E-MONEY',
                    'name'  => 'Pembayaran E-Money',
                    'dynamic_forms'  => [
                        [
                            'label'          => 'No Telpon',
                            'key'            => 'value',
                            'type'           => 'INPUT',
                            'component_name' => null,
                            'default_value'  => null,
                            'attribute'      => null,
                            'rule'           => null,
                            'options'        => [
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $paymentMethod = app(config('app.contracts.PaymentMethod'));
        foreach ($arr as $data) $paymentMethod->prepareStorePaymentMethod($this->requestDTO(PaymentMethodData::class,$data));
    }
}
