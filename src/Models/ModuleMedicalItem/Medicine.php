<?php

namespace Projects\WellmedBackbone\Models\ModuleMedicalItem;

use Hanafalah\ModuleMedicalItem\Models\Medicine as ModelsMedicine;

class Medicine extends ModelsMedicine
{
    protected function isUsingMedicalItem(): bool{
        return false;
    }
}
