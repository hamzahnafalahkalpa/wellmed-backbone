<?php

namespace Projects\WellmedBackbone\Models\ModuleMedicalItem;

use Hanafalah\ModuleMedicalItem\Models\MedicTool as ModelsMedicTool;

class MedicTool extends ModelsMedicTool
{
    protected function isUsingMedicalItem(): bool{
        return false;
    }
}
