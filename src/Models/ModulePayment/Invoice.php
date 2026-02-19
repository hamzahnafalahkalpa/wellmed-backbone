<?php

namespace Projects\WellmedBackbone\Models\ModulePayment;

use Hanafalah\ModulePayment\Models\Transaction\Invoice as PaymentInvoice;

class Invoice extends PaymentInvoice
{
    protected $casts = [
        'invoice_code'   => 'string',
        'billing_id'     => 'string',
        'author_id'      => 'string',
        'author_type'    => 'string',
        'payer_id'       => 'string',
        'payer_type'     => 'string',
        // Props-based fields
        'author_name'    => 'string',
        'payer_name'     => 'string',
        'billing_code'   => 'string',
        'patient_name'   => 'string',
        'patient_nik'    => 'string',
        'total_amount'   => 'float',
        'total_paid'     => 'float',
        'total_debt'     => 'float',
    ];

    /**
     * Props query mapping for virtual attributes
     */
    public function getPropsQuery(): array
    {
        return [
            'author_name'  => 'props->prop_author->name',
            'payer_name'   => 'props->prop_payer->name',
            'billing_code' => 'props->prop_billing->billing_code',
            'patient_name' => 'props->prop_billing->prop_transaction->prop_consument->prop_reference->name',
            'patient_nik'  => 'props->prop_billing->prop_transaction->prop_consument->prop_reference->prop_people->card_identity->nik',
            'total_amount' => 'props->prop_payment_summary->total_amount',
            'total_paid'   => 'props->prop_payment_summary->total_paid',
            'total_debt'   => 'props->prop_payment_summary->total_debt',
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
        'index_name' => 'invoice',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'flag',
            'invoice_code',

            // === Billing Reference (from props) ===
            'billing_id',
            'billing_code',

            // === Author (from props) ===
            'author_id',
            'author_type',
            'author_name',

            // === Payer (from props) ===
            'payer_id',
            'payer_type',
            'payer_name',

            // === Patient Info (from props) ===
            'patient_name',
            'patient_nik',

            // === Payment Amounts (from props) ===
            'total_amount',
            'total_paid',
            'total_debt',

            // === Timestamps ===
            'reported_at',
            'paid_at',
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
