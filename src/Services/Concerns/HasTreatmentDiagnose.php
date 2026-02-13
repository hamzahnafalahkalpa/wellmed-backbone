<?php

namespace Projects\WellmedBackbone\Services\Concerns;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasTreatmentDiagnose{
    /**
     * Increment new patient count when a patient is created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewTreatmentDiagnose(array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementTreatmentDiagnose($periodType, $data, $tenantId, $workspaceId);
        }            
        return $results;
    }

    /**
     * Increment a specific statistic counter.
     *
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementTreatmentDiagnose(string $periodType, array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['diagnosis_treatment'])){
                return [
                    'success' => false,
                    'error' => "Treatment and diagnose are not configured"
                ];
            }

            $currentData['diagnosis_treatment'] ??= [];
            array_unshift($currentData['diagnosis_treatment'],$data);
            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment treatment and diagnose', [
                'error' => $e->getMessage(),
                'treatment_diagnose' => 'Treatment and diagnose',
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }    
}