<?php

use Hanafalah\LaravelSupport\Jobs\ElasticJob;

return [
    /*
    |--------------------------------------------------------------------------
    | Index Definitions
    |--------------------------------------------------------------------------
    |
    | Define all indices used in the application for reporting and searching.
    |
    */
    'indices' => [
        // Location/Master Data
        'country' => [
            'name' => 'country',
        ],
        'province' => [
            'name' => 'province',
        ],
        'district' => [
            'name' => 'district',
        ],
        'subdistrict' => [
            'name' => 'subdistrict',
        ],
        'village' => [
            'name' => 'village',
        ],
        'disease' => [
            'name' => 'disease',
        ],

        // Patient & Visit Data
        'patient' => [
            'name' => 'patient',
        ],
        'patient_illness' => [
            'name' => 'patient_illness',
        ],
        'visit_patient' => [
            'name' => 'visit_patient',
        ],
        'visit_registration' => [
            'name' => 'visit_registration',
        ],
        'visit_examination' => [
            'name' => 'visit_examination',
        ],

        // Billing & Financial Data
        'billing' => [
            'name' => 'billing',
        ],
        'invoice' => [
            'name' => 'invoice',
        ],
        'refund' => [
            'name' => 'refund',
        ],
        'wallet_transaction' => [
            'name' => 'wallet_transaction',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Retention Policy
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of old Elasticsearch indices.
    | This helps prevent index bloat, especially for per-patient daily indices.
    |
    | retention_days: Number of days to keep data (e.g., 2 = today + yesterday)
    | flushable: Whether this index type can be automatically flushed
    | flush_schedule: Cron expression for automatic flush (null = manual only)
    |
    */
    'retention' => [
        'enabled' => env('ELASTICSEARCH_RETENTION_ENABLED', true),

        // Default retention for all indices (in days)
        'default_days' => env('ELASTICSEARCH_RETENTION_DAYS', 7),

        // Per-index type retention configuration
        'indices' => [
            // DEPRECATED: Old per-patient daily indices (will be migrated)
            // Run: php artisan wellmed-backbone:migrate-patient-integration-indices
            // After migration, these indices will be deleted
            'patient-dashboard-metrics' => [
                'retention_days' => 0,      // Delete all old indices
                'flushable' => true,
                'flush_documents' => true,
                'flush_indices' => true,    // Delete the indices entirely
                'description' => 'DEPRECATED - Old per-patient daily metrics (migrate to patient-satu-sehat-integration)',
                'deprecated' => true,
            ],

            // NEW: Patient Satu Sehat Integration (single index per tenant)
            // This is NOT time-based - stores current state of each patient
            // No retention needed - data persists as long as patient exists
            'patient-satu-sehat-integration' => [
                'retention_days' => null,   // No retention - data persists forever
                'flushable' => false,       // Never auto-flush
                'flush_documents' => false,
                'flush_indices' => false,
                'description' => 'Patient Satu Sehat integration status (1 doc per patient, current state)',
            ],

            // Main dashboard metrics (shared index per tenant)
            'dashboard-metrics-daily' => [
                'retention_days' => env('ELASTICSEARCH_DASHBOARD_DAILY_RETENTION_DAYS', 30),
                'flushable' => true,
                'flush_documents' => true,
                'flush_indices' => false,   // Keep index structure
                'description' => 'Tenant-level daily dashboard aggregations',
            ],

            'dashboard-metrics-weekly' => [
                'retention_days' => env('ELASTICSEARCH_DASHBOARD_WEEKLY_RETENTION_DAYS', 90),
                'flushable' => true,
                'flush_documents' => true,
                'flush_indices' => false,
                'description' => 'Tenant-level weekly dashboard aggregations',
            ],

            'dashboard-metrics-monthly' => [
                'retention_days' => env('ELASTICSEARCH_DASHBOARD_MONTHLY_RETENTION_DAYS', 365),
                'flushable' => true,
                'flush_documents' => true,
                'flush_indices' => false,
                'description' => 'Tenant-level monthly dashboard aggregations',
            ],

            'dashboard-metrics-yearly' => [
                'retention_days' => env('ELASTICSEARCH_DASHBOARD_YEARLY_RETENTION_DAYS', 1825), // 5 years
                'flushable' => false,
                'flush_documents' => false,
                'flush_indices' => false,
                'description' => 'Tenant-level yearly dashboard aggregations (keep forever)',
            ],

            // Visit registration queue (real-time data)
            'visit-registration-queue' => [
                'retention_days' => env('ELASTICSEARCH_QUEUE_RETENTION_DAYS', 1),
                'flushable' => true,
                'flush_documents' => true,
                'flush_indices' => false,
                'description' => 'Real-time visit queue data',
            ],
        ],

        // Schedule for automatic flush (runs daily at 2 AM)
        'schedule' => [
            'enabled' => env('ELASTICSEARCH_RETENTION_SCHEDULE_ENABLED', true),
            'cron' => '0 2 * * *',
            'queue' => 'elasticsearch',
        ],
    ]
];
