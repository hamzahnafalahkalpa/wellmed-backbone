<?php

namespace Projects\WellmedBackbone\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Visit Registration Queue Service
 *
 * Manages daily queue numbering for patient registration using Elasticsearch.
 * Queue counter resets daily at midnight.
 */
class VisitRegistrationQueueService
{
    protected $client;
    protected string $indexName = 'visit-registration-queue';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    /**
     * Get the full index name with tenant prefix.
     */
    protected function getIndexName(): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');
        return $prefix . $separator . $this->indexName;
    }

    /**
     * Generate document ID for the queue counter.
     * One document per tenant per day.
     */
    protected function generateDocumentId(int $tenantId): string
    {
        return "queue_{$tenantId}";
    }

    /**
     * Ensure the index exists in Elasticsearch.
     */
    protected function ensureIndexExists(): bool
    {
        try {
            $indexName = $this->getIndexName();

            $exists = $this->client->indices()->exists(['index' => $indexName]);
            $indexExists = is_bool($exists) ? $exists : (method_exists($exists, 'asBool') ? $exists->asBool() : (bool) $exists);

            if (!$indexExists) {
                $this->client->indices()->create([
                    'index' => $indexName,
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 1,
                            'number_of_replicas' => 0
                        ],
                        'mappings' => [
                            'properties' => [
                                'tenant_id' => ['type' => 'integer'],
                                'count' => ['type' => 'integer'],
                                'expired_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd'],
                                'updated_at' => ['type' => 'date']
                            ]
                        ]
                    ]
                ]);

                Log::channel('elasticsearch')->info('Created visit registration queue index', [
                    'index' => $indexName
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure queue index exists', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get or create the queue document for the current tenant.
     * Resets count to 0 if expired.
     */
    public function getOrCreateQueueDocument(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $this->ensureIndexExists();

            $indexName = $this->getIndexName();
            $documentId = $this->generateDocumentId($tenantId);
            $today = Carbon::today()->format('Y-m-d');
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');

            // Try to get existing document
            try {
                $response = $this->client->get([
                    'index' => $indexName,
                    'id' => $documentId
                ]);

                $document = $response['_source'] ?? null;

                if ($document) {
                    $expiredAt = $document['expired_at'] ?? null;

                    // Check if expired (current date >= expired_at)
                    if ($expiredAt && Carbon::parse($expiredAt)->lte(Carbon::today())) {
                        // Reset counter
                        $document = [
                            'tenant_id' => $tenantId,
                            'count' => 0,
                            'expired_at' => $tomorrow,
                            'updated_at' => now()->toIso8601String()
                        ];

                        $this->storeDocument($document, $tenantId);

                        Log::channel('elasticsearch')->info('Queue counter reset (expired)', [
                            'tenant_id' => $tenantId,
                            'new_expired_at' => $tomorrow
                        ]);
                    }

                    return $document;
                }
            } catch (\Throwable $e) {
                // Document doesn't exist, will create below
            }

            // Create new document
            $document = [
                'tenant_id' => $tenantId,
                'count' => 0,
                'expired_at' => $tomorrow,
                'updated_at' => now()->toIso8601String()
            ];

            $this->storeDocument($document, $tenantId);

            Log::channel('elasticsearch')->info('Created new queue document', [
                'tenant_id' => $tenantId,
                'expired_at' => $tomorrow
            ]);

            return $document;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get/create queue document', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);

            // Return default document on error
            return [
                'tenant_id' => $tenantId ?? 0,
                'count' => 0,
                'expired_at' => Carbon::tomorrow()->format('Y-m-d'),
                'updated_at' => now()->toIso8601String()
            ];
        }
    }

    /**
     * Get next queue number and increment the counter.
     * Returns the NEW queue number (after increment).
     */
    public function getNextQueueNumber(?int $tenantId = null): int
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();

            // Get current document (will reset if expired)
            $document = $this->getOrCreateQueueDocument($tenantId);

            // Increment count
            $newCount = ($document['count'] ?? 0) + 1;

            // Update document
            $document['count'] = $newCount;
            $document['updated_at'] = now()->toIso8601String();

            $this->storeDocument($document, $tenantId);

            Log::channel('elasticsearch')->debug('Queue number incremented', [
                'tenant_id' => $tenantId,
                'queue_number' => $newCount
            ]);

            return $newCount;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get next queue number', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);

            // Fallback: return timestamp-based number to avoid duplicates
            return (int) now()->format('His');
        }
    }

    /**
     * Get current queue count without incrementing.
     */
    public function getCurrentCount(?int $tenantId = null): int
    {
        $document = $this->getOrCreateQueueDocument($tenantId);
        return $document['count'] ?? 0;
    }

    /**
     * Reserve next queue number without incrementing the counter.
     * Returns the number that WILL be assigned.
     */
    public function reserveNextQueueNumber(?int $tenantId = null): int
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $document = $this->getOrCreateQueueDocument($tenantId);
            $nextNumber = ($document['count'] ?? 0) + 1;

            Log::channel('elasticsearch')->debug('Queue number reserved', [
                'tenant_id' => $tenantId,
                'reserved_number' => $nextNumber
            ]);

            return $nextNumber;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to reserve queue number', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);

            // Fallback: return timestamp-based number to avoid duplicates
            return (int) now()->format('His');
        }
    }

    /**
     * Confirm and commit the queue number by incrementing the counter.
     * Should be called after successful registration.
     */
    public function confirmQueueNumber(?int $tenantId = null): bool
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();

            // Get current document
            $document = $this->getOrCreateQueueDocument($tenantId);

            // Increment count
            $newCount = ($document['count'] ?? 0) + 1;

            // Update document
            $document['count'] = $newCount;
            $document['updated_at'] = now()->toIso8601String();

            $result = $this->storeDocument($document, $tenantId);

            if ($result['success']) {
                Log::channel('elasticsearch')->debug('Queue number confirmed', [
                    'tenant_id' => $tenantId,
                    'queue_number' => $newCount
                ]);
            }

            return $result['success'];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to confirm queue number', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);

            return false;
        }
    }

    /**
     * Store document in Elasticsearch.
     */
    protected function storeDocument(array $document, int $tenantId): array
    {
        try {
            $indexName = $this->getIndexName();
            $documentId = $this->generateDocumentId($tenantId);

            $response = $this->client->index([
                'index' => $indexName,
                'id' => $documentId,
                'body' => $document
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store queue document', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Initialize queue for tenant (called on tenant boot).
     * This ensures the document exists and resets if expired.
     */
    public function initializeForTenant(?int $tenantId = null): void
    {
        if (!config('elasticsearch.enabled', false)) {
            return;
        }

        try {
            $this->getOrCreateQueueDocument($tenantId);
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->warning('Failed to initialize queue for tenant', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);
        }
    }
}
