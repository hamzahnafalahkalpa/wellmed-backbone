<?php

namespace Projects\WellmedBackbone\Transformers\Patient;

use Hanafalah\ModulePatient\Resources\Patient\ShowPatient as PatientShowPatient;

class ShowPatient extends ViewPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $show = $this->resolveNow(new PatientShowPatient($this));
        $arr = $this->mergeArray(parent::toArray($request), $show, $arr);
        return $arr;
    }
}
