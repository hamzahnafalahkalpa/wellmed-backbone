<?php

namespace Projects\WellmedBackbone\Transformers\MedicalSupport;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ViewMedicalSupport extends ApiResource{
    public function toArray(Request $request): array
    {
        return [
            'visit_examination_id' => null,
            'medical_support' => [
                    
            ]
        ];
    }
}