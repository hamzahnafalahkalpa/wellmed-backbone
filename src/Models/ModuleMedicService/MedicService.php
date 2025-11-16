<?php

namespace Projects\WellmedBackbone\Models\ModuleMedicService;

use Hanafalah\LaravelFeature\Concerns\HasRestrictionFeature;
use Hanafalah\LaravelSupport\Models\Unicode\Unicode;

class MedicService extends Unicode
{
    use HasRestrictionFeature;

    public function usedRestrictionAs(): array{
        return ['model'];
    }

    protected $table = 'unicodes';
}
