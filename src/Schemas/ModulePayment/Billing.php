<?php

namespace Projects\WellmedBackbone\Schemas\ModulePayment;

use Projects\WellmedBackbone\Contracts\Schemas\ModulePayment\Billing as ContractBilling;
use Hanafalah\ModulePayment\Schemas\Billing as SchemasBilling;
use Hanafalah\ModulePayment\Contracts\Data\BillingData;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Services\DashboardMetricsService;
use Illuminate\Support\Facades\Log;

class Billing extends SchemasBilling implements ContractBilling
{
    protected function afterBillingCreated(Model &$billing, BillingData &$billing_dto): self{
        parent::afterBillingCreated($billing, $billing_dto);

        // Update dashboard statistics for new billing
        if ($billing->debt == 0){
            $this->updateDashboardStatistics($billing,'omzet');
        }
        if ($this->is_recently_created) {
            // $this->updateDashboardStatistics($billing,'pending-transaction');
        }
        return $this;
    }

    protected function afterBillingReported(Model &$billing, BillingData &$billing_dto): self{
        return $this;
    }

    /**
     * @param Model $billing
     * @return void
     */
    private function updateDashboardStatistics(Model $billing,string $type,?array $data = []): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }
            $dashboardService = app(DashboardMetricsService::class);
            switch ($type) {
                case 'omzet': 
                    $dashboardService->incrementNewOmzet($billing->amount);
                break;
                case 'pending-transaction': 
                    // $dashboardService->incrementNewPending();
                break;
            }

            Log::channel('elasticsearch')->info('Dashboard statistics updated for new billing', [
                'billing_id' => $billing->getKey(),
            ]);

        } catch (\Throwable $e) {
            // Don't fail billing creation if dashboard update fails
            Log::channel('elasticsearch')->error('Failed to update dashboard statistics', [
                'billing_id' => $billing->getKey(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
