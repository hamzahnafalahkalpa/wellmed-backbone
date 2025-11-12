<?php

namespace Projects\WellmedBackbone\Transformers\Employee;

use Hanafalah\ModuleEmployee\Resources\Employee\ViewEmployee as EmployeeViewEmployee;

class ViewEmployee extends EmployeeViewEmployee
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
