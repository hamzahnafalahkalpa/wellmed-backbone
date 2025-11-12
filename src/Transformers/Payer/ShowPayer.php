<?php

namespace Projects\WellmedBackbone\Transformers\Payer;

use Hanafalah\ModulePayer\Resources\Payer\ShowPayer as PayerShowPayer;
use Projects\WellmedBackbone\Transformers\Company\ShowCompany;

class ShowPayer extends ViewPayer
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'user_wallet' => $this->relationValidation('userWallet',function(){
                return $this->userWallet->toShowApi();
            }),
            'payment_summary' => $this->relationValidation('paymentSummary',function(){
                return $this->paymentSummary->toShowApi()->resolve();
            })
        ];
        $show = $this->resolveNow(new PayerShowPayer($this));
        $show_company = $this->resolveNow(new ShowCompany($this));
        $arr = $this->mergeArray(parent::toArray($request), $show_company, $show, $arr);
        return $arr;
    }
}
