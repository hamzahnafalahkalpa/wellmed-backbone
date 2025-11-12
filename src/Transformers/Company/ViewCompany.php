<?php

namespace Projects\WellmedBackbone\Transformers\Company;

use Hanafalah\ModulePayer\Resources\Company\ViewCompany as CompanyViewCompany;

class ViewCompany extends CompanyViewCompany
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'payment_summary' => $this->relationValidation('paymentSummary',function(){
                return $this->paymentSummary->toViewApi()->resolve();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
