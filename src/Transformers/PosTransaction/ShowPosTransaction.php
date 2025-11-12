<?php

namespace Projects\WellmedBackbone\Transformers\PosTransaction;

class ShowPosTransaction extends ViewPosTransaction
{

    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'reference' => $this->relationValidation('reference', function () {
                return $this->propNil($this->reference->toShowApi()->resolve(),'transaction');
            }),
            'transaction_items' => $this->relationValidation('transactionItems', function () {
                return $this->transactionItems->map(function ($item) {
                    return $item->toViewApi();
                });
            }),
            'billing' => $this->relationValidation('billing', function () {
                return $this->propNil($this->billing->toShowApi()->resolve(),'has_transaction');
            }),
            'payment_summary' => $this->relationValidation('paymentSummary', function () {
                return $this->paymentSummary->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
