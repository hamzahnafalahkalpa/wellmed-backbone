<?php

namespace Projects\WellmedBackbone\Transformers\Examination;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ViewExamination extends ApiResource{
    protected $__local_model;

    public function toArray(Request $request): array
    {
        $model = $this->__local_model = $this->getModel();
        return [
            'visit_examination_id' => null
        ];
    }

}