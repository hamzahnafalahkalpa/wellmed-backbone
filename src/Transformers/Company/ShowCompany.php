<?php

namespace Projects\WellmedBackbone\Transformers\Company;

use Hanafalah\ModulePayer\Resources\Company\ShowCompany as CompanyShowCompany;

class ShowCompany extends ViewCompany
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'payment_summary' => $this->relationValidation('paymentSummary',function(){
                return $this->paymentSummary->toShowApi()->resolve();
            })
        ];
        $show = $this->resolveNow(new CompanyShowCompany($this));
        $arr = $this->mergeArray(parent::toArray($request), $show, $arr);
        return $arr;
    }
}
