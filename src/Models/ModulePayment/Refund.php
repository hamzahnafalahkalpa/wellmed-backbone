<?php

namespace Projects\WellmedBackbone\Models\ModulePayment;

use Hanafalah\ModulePayment\Models\Transaction\Refund as PaymentRefund;

class Refund extends PaymentRefund
{
    protected $casts = [
        'code'          => 'string',
        'name'          => 'string',
        'invoice_id'    => 'string',
        // Props-based fields
        'invoice_code'  => 'string',
        'patient_name'  => 'string',
        'patient_nik'   => 'string',
        'refund_amount' => 'float',
        'reason'        => 'string',
    ];

    /**
     * Props query mapping for virtual attributes
     */
    public function getPropsQuery(): array
    {
        return [
            'invoice_code'  => 'props->prop_invoice->invoice_code',
            'patient_name'  => 'props->prop_invoice->prop_billing->prop_transaction->prop_consument->prop_reference->name',
            'patient_nik'   => 'props->prop_invoice->prop_billing->prop_transaction->prop_consument->prop_reference->prop_people->card_identity->nik',
            'refund_amount' => 'props->prop_wallet_transaction->amount',
            'reason'        => 'props->reason',
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
        'index_name' => 'refund',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'code',
            'name',

            // === Invoice Reference (from props) ===
            'invoice_id',
            'invoice_code',

            // === Patient Info (from props) ===
            'patient_name',
            'patient_nik',

            // === Refund Details (from props) ===
            'refund_amount',
            'reason',

            // === Timestamps ===
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
