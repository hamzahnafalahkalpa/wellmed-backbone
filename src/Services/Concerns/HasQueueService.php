<?php

namespace Projects\WellmedBackbone\Services\Concerns;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait HasQueueService{
    private $polyclinic_colors = [
        'UMUM' => [
            'bgColor' => 'bg-blue-50',
            'textColor' => 'text-blue-600',
            'borderColor' => 'border-blue-200',
        ],
        'ORTHOPEDI' => [
            'bgColor' => 'bg-indigo-50',
            'textColor' => 'text-indigo-600',
            'borderColor' => 'border-indigo-200',
        ],
        'SUNAT' => [
            'bgColor' => 'bg-emerald-50',
            'textColor' => 'text-emerald-600',
            'borderColor' => 'border-emerald-200',
        ],
        'KECANTIKAN' => [
            'bgColor' => 'bg-pink-50',
            'textColor' => 'text-pink-600',
            'borderColor' => 'border-pink-200',
        ],
        'MATA' => [
            'bgColor' => 'bg-cyan-50',
            'textColor' => 'text-cyan-600',
            'borderColor' => 'border-cyan-200',
        ],
        'THT' => [
            'bgColor' => 'bg-teal-50',
            'textColor' => 'text-teal-600',
            'borderColor' => 'border-teal-200',
        ],
        'INTERNIS' => [
            'bgColor' => 'bg-sky-50',
            'textColor' => 'text-sky-600',
            'borderColor' => 'border-sky-200',
        ],
        'GIGI & MULUT' => [
            'bgColor' => 'bg-violet-50',
            'textColor' => 'text-violet-600',
            'borderColor' => 'border-violet-200',
        ],
        'KIA' => [
            'bgColor' => 'bg-rose-50',
            'textColor' => 'text-rose-600',
            'borderColor' => 'border-rose-200',
        ],
        'LANSIA' => [
            'bgColor' => 'bg-amber-50',
            'textColor' => 'text-amber-700',
            'borderColor' => 'border-amber-200',
        ],
        'ADMIN' => [
            'bgColor' => 'bg-gray-100',
            'textColor' => 'text-gray-700',
            'borderColor' => 'border-gray-300',
        ],
        'VACCINE' => [
            'bgColor' => 'bg-lime-50',
            'textColor' => 'text-lime-700',
            'borderColor' => 'border-lime-200',
        ],
        'MTBS' => [
            'bgColor' => 'bg-green-50',
            'textColor' => 'text-green-700',
            'borderColor' => 'border-green-200',
        ],
        'PATOLOGI KLINIK' => [
            'bgColor' => 'bg-fuchsia-50',
            'textColor' => 'text-fuchsia-600',
            'borderColor' => 'border-fuchsia-200',
        ],
        'PATOLOGI ANATOMI' => [
            'bgColor' => 'bg-purple-50',
            'textColor' => 'text-purple-600',
            'borderColor' => 'border-purple-200',
        ],
        'MCU' => [
            'bgColor' => 'bg-yellow-50',
            'textColor' => 'text-yellow-700',
            'borderColor' => 'border-yellow-200',
        ],
        'RUANG TINDAKAN' => [
            'bgColor' => 'bg-orange-50',
            'textColor' => 'text-orange-700',
            'borderColor' => 'border-orange-200',
        ],
        'RADIOLOGI' => [
            'bgColor' => 'bg-slate-50',
            'textColor' => 'text-slate-700',
            'borderColor' => 'border-slate-200',
        ],
        'RAWAT INAP' => [
            'bgColor' => 'bg-stone-50',
            'textColor' => 'text-stone-700',
            'borderColor' => 'border-stone-200',
        ],
        'INSTALASI FARMASI' => [
            'bgColor' => 'bg-emerald-100',
            'textColor' => 'text-emerald-800',
            'borderColor' => 'border-emerald-300',
        ],
        'VK' => [
            'bgColor' => 'bg-red-50',
            'textColor' => 'text-red-700',
            'borderColor' => 'border-red-200',
        ],
        'UGD' => [
            'bgColor' => 'bg-red-100',
            'textColor' => 'text-red-800',
            'borderColor' => 'border-red-300',
        ],
    ];

    /**
     * Increment new patient count when a patient is created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewQueueService(array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementQueueService($periodType, $data, $tenantId, $workspaceId);
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
    public function incrementQueueService(string $periodType, array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);
            if (!isset($currentData['queue_services'])){
                return [
                    'success' => false,
                    'error' => "Queue service are not configured"
                ];
            }
            $currentData['queue_services'] ??= [];
            $queueIndex = $this->findQueueIndex($currentData['queue_services'],$data['service_label']);

            $colors = $this->polyclinic_colors[$data['service_label']];
            $data = array_merge($data,$colors);
            if (isset($currentData[$queueIndex])){
                $currentSelectedData = $currentData[$queueIndex];
                $data['currentQueue'] = $currentSelectedData['currentQueue'] + 1;
            }else{
                $data['currentQueue'] = 1;
            }

            // currentQueue: 12,
            // waiting: 8,
            // serving: 4,
            array_unshift($currentData['queue_services'],$data);

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment queue and service', [
                'error' => $e->getMessage(),
                'treatment_diagnose' => 'Queue service',
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }    

    /**
     * Find statistic index by ID in statistics array.
     *
     * @param array $statistics
     * @param string $statisticId
     * @return int|null
     */
    protected function findQueueIndex(array $queues, string $service_label): ?int
    {
        foreach ($queues as $index => $stat) {
            if (($stat['service_label'] ?? '') === $service_label) {
                return $index;
            }
        }
        return null;
    }
}