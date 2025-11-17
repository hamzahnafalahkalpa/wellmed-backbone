<?php

namespace Projects\WellmedBackbone\Models\ModuleMedicService;

use Hanafalah\LaravelFeature\Concerns\HasRestrictionFeature;
use Hanafalah\LaravelSupport\Models\Unicode\Unicode;
use Hanafalah\ModuleMedicService\Resources\MedicService\{ShowMedicService, ViewMedicService};

class MedicService extends Unicode
{
    use HasRestrictionFeature;

    public function usedRestrictionAs(): array{
        return ['model'];
    }

    protected $table = 'unicodes';

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            $query->is_restricted ??= false;
        });
    }

    public function isUsingService(): bool{
        return true;
    }

    public function getViewResource(){
        return ViewMedicService::class;
    }

    public function getShowResource(){
        return ShowMedicService::class;
    }
}
