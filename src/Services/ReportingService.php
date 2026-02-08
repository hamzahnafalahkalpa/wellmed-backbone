<?php

namespace Projects\WellmedBackbone\Services;

use Hanafalah\LaravelSupport\Concerns\Support\HasElasticsearchLog;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Hanafalah\LaravelSupport\Jobs\ElasticJob;

/**
 * Reporting Service
 *
 * Handles indexing and retrieval of reporting data in Elasticsearch.
 * Supports multiple report types: patient, patient_illness, visit_patient, billing, etc.
 */
class ReportingService
{
    use HasElasticsearchLog;

    protected $client;

    // Report index types
    public const INDEX_PATIENT = 'patient';
    public const INDEX_PATIENT_ILLNESS = 'patient_illness';
    public const INDEX_VISIT_PATIENT = 'visit_patient';
    public const INDEX_VISIT_REGISTRATION = 'visit_registration';
    public const INDEX_VISIT_EXAMINATION = 'visit_examination';
    public const INDEX_BILLING = 'billing';
    public const INDEX_INVOICE = 'invoice';
    public const INDEX_REFUND = 'refund';
    public const INDEX_WALLET_TRANSACTION = 'wallet_transaction';

    public function __construct()
    {
        if (config('elasticsearch.enabled', false)) {
            $this->client = app('elasticsearch');
        }
    }

    /**
     * Get all available index types.
     */
    public function getIndexTypes(): array
    {
        return [
            self::INDEX_PATIENT,
            self::INDEX_PATIENT_ILLNESS,
            self::INDEX_VISIT_PATIENT,
            self::INDEX_VISIT_REGISTRATION,
            self::INDEX_VISIT_EXAMINATION,
            self::INDEX_BILLING,
            self::INDEX_INVOICE,
            self::INDEX_REFUND,
            self::INDEX_WALLET_TRANSACTION,
        ];
    }

    /**
     * Get index name with prefix.
     */
    public function getIndexName(string $indexType): string
    {
        $prefix = config('app.elasticsearch.index_prefix', config('app.env', 'development'));
        $separator = config('app.elasticsearch.index_separator', '.');
        $indexConfig = config("app.elasticsearch.indexes.{$indexType}", ['name' => $indexType]);

        return $prefix . $separator . ($indexConfig['name'] ?? $indexType);
    }

    /**
     * Index a single document.
     */
    public function index(string $indexType, array $data, ?string $documentId = null): array
    {
        if (!config('elasticsearch.enabled', false)) {
            return ['success' => false, 'error' => 'Elasticsearch is disabled'];
        }

        try {
            $indexName = $this->getIndexName($indexType);
            $params = [
                'index' => $indexName,
                'body' => $data,
            ];

            if ($documentId) {
                $params['id'] = $documentId;
            }

            $response = $this->client->index($params);
            $responseArray = $response->asArray();

            $finalDocumentId = $responseArray['_id'] ?? $documentId;

            // Log to elasticsearch_logs table (reporting creates new records)
            if (config('elasticsearch.logging.enabled', true) && $finalDocumentId) {
                $this->logSingleOperation($indexName, $finalDocumentId, 'index', [
                    'result' => $responseArray['result'] ?? null,
                    '_version' => $responseArray['_version'] ?? null,
                ]);
            }

            return [
                'success' => true,
                'id' => $finalDocumentId,
                'index' => $responseArray['_index'] ?? null,
                'result' => $responseArray['result'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error("Failed to index {$indexType}", [
                'error' => $e->getMessage(),
                'index_type' => $indexType,
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Index multiple documents via queue (async).
     */
    public function indexAsync(string $indexType, array $documents): void
    {
        if (!config('elasticsearch.enabled', false)) {
            return;
        }

        dispatch(new ElasticJob([
            'type' => 'BULK',
            'datas' => [
                [
                    'index' => $this->getIndexName($indexType),
                    'data' => $documents,
                ]
            ]
        ]))
        ->onQueue('elasticsearch')
        ->onConnection('rabbitmq');
    }

    /**
     * Index single document via queue (async).
     */
    public function indexOneAsync(string $indexType, array $document): void
    {
        $this->indexAsync($indexType, [$document]);
    }

    /**
     * Bulk index multiple documents synchronously.
     */
    public function bulkIndex(string $indexType, array $documents): array
    {
        if (!config('elasticsearch.enabled', false)) {
            return ['success' => false, 'error' => 'Elasticsearch is disabled'];
        }

        try {
            $indexName = $this->getIndexName($indexType);
            $body = [];

            foreach ($documents as $doc) {
                $action = ['index' => ['_index' => $indexName]];
                if (isset($doc['id'])) {
                    $action['index']['_id'] = $doc['id'];
                }
                $body[] = $action;
                $body[] = $doc;
            }

            $response = $this->client->bulk(['body' => $body]);
            $responseArray = $response->asArray();

            // Log bulk operations
            if (config('elasticsearch.logging.enabled', true)) {
                $this->logElasticsearchOperations($body, $responseArray);
            }

            return [
                'success' => !($responseArray['errors'] ?? false),
                'took' => $responseArray['took'] ?? 0,
                'items_count' => count($responseArray['items'] ?? []),
                'errors' => $responseArray['errors'] ?? false,
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error("Failed to bulk index {$indexType}", [
                'error' => $e->getMessage(),
                'index_type' => $indexType,
                'document_count' => count($documents),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete a document by ID.
     */
    public function delete(string $indexType, string $documentId): array
    {
        if (!config('elasticsearch.enabled', false)) {
            return ['success' => false, 'error' => 'Elasticsearch is disabled'];
        }

        try {
            $indexName = $this->getIndexName($indexType);
            $response = $this->client->delete([
                'index' => $indexName,
                'id' => $documentId,
            ]);
            $responseArray = $response->asArray();

            // Log delete operation
            if (config('elasticsearch.logging.enabled', true)) {
                $this->logSingleOperation($indexName, $documentId, 'delete', [
                    'result' => $responseArray['result'] ?? null,
                ]);
            }

            return [
                'success' => true,
                'result' => $responseArray['result'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error("Failed to delete from {$indexType}", [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete documents by query.
     */
    public function deleteByQuery(string $indexType, array $query): array
    {
        if (!config('elasticsearch.enabled', false)) {
            return ['success' => false, 'error' => 'Elasticsearch is disabled'];
        }

        try {
            $response = $this->client->deleteByQuery([
                'index' => $this->getIndexName($indexType),
                'body' => ['query' => $query],
            ]);

            return [
                'success' => true,
                'deleted' => $response['deleted'] ?? 0,
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error("Failed to delete by query from {$indexType}", [
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update a document by ID.
     */
    public function update(string $indexType, string $documentId, array $data): array
    {
        if (!config('elasticsearch.enabled', false)) {
            return ['success' => false, 'error' => 'Elasticsearch is disabled'];
        }

        try {
            $indexName = $this->getIndexName($indexType);
            $response = $this->client->update([
                'index' => $indexName,
                'id' => $documentId,
                'body' => ['doc' => $data],
            ]);
            $responseArray = $response->asArray();

            // Log update operation
            if (config('elasticsearch.logging.enabled', true)) {
                $this->logSingleOperation($indexName, $documentId, 'update', [
                    'result' => $responseArray['result'] ?? null,
                    '_version' => $responseArray['_version'] ?? null,
                ]);
            }

            return [
                'success' => true,
                'result' => $responseArray['result'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error("Failed to update {$indexType}", [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ===== Convenience Methods for Specific Index Types =====

    /**
     * Index patient data.
     */
    public function indexPatient(array $patientData): void
    {
        $this->indexOneAsync(self::INDEX_PATIENT, $patientData);
    }

    /**
     * Index patient illness data.
     */
    public function indexPatientIllness(array $illnessData): void
    {
        $this->indexOneAsync(self::INDEX_PATIENT_ILLNESS, $illnessData);
    }

    /**
     * Index visit patient data.
     */
    public function indexVisitPatient(array $visitData): void
    {
        $this->indexOneAsync(self::INDEX_VISIT_PATIENT, $visitData);
    }

    /**
     * Index billing data.
     */
    public function indexBilling(array $billingData): void
    {
        $this->indexOneAsync(self::INDEX_BILLING, $billingData);
    }

    /**
     * Index invoice data.
     */
    public function indexInvoice(array $invoiceData): void
    {
        $this->indexOneAsync(self::INDEX_INVOICE, $invoiceData);
    }

    /**
     * Bulk index multiple types at once.
     */
    public function bulkIndexMultiple(array $bulks): void
    {
        if (!config('elasticsearch.enabled', false)) {
            return;
        }

        $datas = [];
        foreach ($bulks as $bulk) {
            $datas[] = [
                'index' => $this->getIndexName($bulk['index_type']),
                'data' => $bulk['data'],
            ];
        }

        dispatch(new ElasticJob([
            'type' => 'BULK',
            'datas' => $datas,
        ]))
        ->onQueue('elasticsearch')
        ->onConnection('rabbitmq');
    }
}
