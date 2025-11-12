<?php

namespace Projects\WellmedBackbone\Transformers\Examination;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowExamination extends ViewExamination{
    protected $__local_model;

    public function toArray(Request $request): array
    {
        $model = $this->__local_model = $this->getModel();
        $morph = Str::snake($model->getMorphClass());
        $arr = [
            'visit_examination_id' => null,
            'examination' => [
                $morph => [
                    'data' => $this->getAssessmentData()
                ],
            ]
        ];
        return $arr;
    }

}