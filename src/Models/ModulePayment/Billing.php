<?php

namespace Projects\WellmedBackbone\Models\ModulePayment;

use Hanafalah\ModulePayment\Models\Transaction\Billing as PaymentBilling;

class Billing extends PaymentBilling
{
    protected $casts = [
        'billing_code'       => 'string',
        'has_transaction_id' => 'string',
        'author_type'        => 'string',
        'author_id'          => 'string',
        'cashier_type'       => 'string',
        'cashier_id'         => 'string',
        // Props-based fields
        'author_name'        => 'string',
        'cashier_name'       => 'string',
        'patient_name'       => 'string',
        'patient_nik'        => 'string',
        'consument_name'     => 'string',
        'total_amount'       => 'float',
        'total_paid'         => 'float',
        'total_debt'         => 'float',
    ];

    /**
     * Props query mapping for virtual attributes
     */
    public function getPropsQuery(): array
    {
        return [
            'author_name'   => 'props->prop_author->name',
            'cashier_name'  => 'props->prop_cashier->name',
            'patient_name'  => 'props->prop_transaction->prop_consument->prop_reference->name',
            'patient_nik'   => 'props->prop_transaction->prop_consument->prop_reference->prop_people->card_identity->nik',
            'consument_name' => 'props->prop_transaction->prop_consument->name',
            'total_amount'  => 'props->prop_transaction->total_amount',
            'total_paid'    => 'props->prop_transaction->total_paid',
            'total_debt'    => 'props->prop_transaction->total_debt',
        ];
    }

    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'billing',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'uuid',
            'billing_code',

            // === Transaction Reference ===
            'has_transaction_id',

            // === Author (from props) ===
            'author_type',
            'author_id',
            'author_name',

            // === Cashier (from props) ===
            'cashier_type',
            'cashier_id',
            'cashier_name',

            // === Patient/Consument Info (from props) ===
            'patient_name',
            'patient_nik',
            'consument_name',

            // === Payment Amounts (from props) ===
            'total_amount',
            'total_paid',
            'total_debt',

            // === Status ===
            'status',

            // === Timestamps ===
            'reported_at',
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
