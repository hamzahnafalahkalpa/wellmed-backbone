<?php

namespace Projects\WellmedBackbone\Transformers\PosTransaction;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPosTransaction extends ApiResource
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
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'transaction_code'  => $this->transaction_code,
            'consument_id'      => $this->prop_consument['id'],
            // 'consument'         => $this->prop_consument['reference'],
            'reference_type'    => $this->reference_type,
            'reference'         => $this->relationValidation('reference', function () {
                return $this->propNil($this->reference->toViewApi()->resolve(),'transaction');
            }),
            'payment_summary' => $this->relationValidation('paymentSummary', function () {
                return $this->paymentSummary->toViewApi()->resolve();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if ($this->relationloaded('consument') && isset($this->consument)) {
            $consument = $this->consument;
            if (isset($consument->reference)){
                $arr['consument'] = $consument->reference->toShowApi()->resolve();
            }else{
                $arr['consument'] = $consument->toShowApi()->resolve();
            }
        }else{
            $arr['consument'] = $this->prop_consument['reference'];
        }

        return $arr;
    }
}
