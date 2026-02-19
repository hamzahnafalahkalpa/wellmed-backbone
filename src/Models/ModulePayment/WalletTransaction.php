<?php

namespace Projects\WellmedBackbone\Models\ModulePayment;

use Hanafalah\ModulePayment\Models\Transaction\WalletTransaction as PaymentWalletTransaction;

class WalletTransaction extends PaymentWalletTransaction
{
    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'wallet_transaction',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'uuid',
            'name',

            // === Transaction Details ===
            'transaction_type',
            'status',

            // === Wallet Reference ===
            'wallet_id',
            'user_wallet_id',

            // === Amounts ===
            'previous_balance',
            'current_balance',
            'nominal',

            // === Reference ===
            'reference_type',
            'reference_id',

            // === Consument ===
            'consument_type',
            'consument_id',

            // === Author ===
            'author_type',
            'author_id',

            // === Timestamps ===
            'reported_at',
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
