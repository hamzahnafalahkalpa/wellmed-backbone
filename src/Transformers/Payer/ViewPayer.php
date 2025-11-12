<?php

namespace Projects\WellmedBackbone\Transformers\Payer;

use Hanafalah\ModulePayer\Resources\Payer\ViewPayer as PayerViewPayer;

class ViewPayer extends PayerViewPayer
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
