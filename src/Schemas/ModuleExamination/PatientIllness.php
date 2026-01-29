<?php

namespace Projects\WellmedBackbone\Schemas\ModuleExamination;

use Hanafalah\ModuleExamination\Contracts\Data\Examination\PatientIllnessData;
use Hanafalah\ModuleExamination\Schemas\Examination\PatientIllness as ExaminationPatientIllness;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleExamination\PatientIllness as ModuleExaminationPatientIllness;
use Hanafalah\LaravelSupport\Jobs\ElasticJob;

class PatientIllness extends ExaminationPatientIllness implements ModuleExaminationPatientIllness
{
    public function prepareStorePatientIllness(PatientIllnessData $patient_illness_dto): Model
    {
        $patient_illness = parent::prepareStorePatientIllness($patient_illness_dto);
        $visit_examination_model = $patient_illness_dto->visit_examination_model;
        if (isset($visit_examination_model->sign_off_at) && config('elasticsearch.enabled', false)) {
            dispatch(new ElasticJob([
                'type'  => 'BULK',
                'datas' => [
                    [
                        'index' => config('app.elasticsearch.indexes.patient_illness.full_name'),
                        'data'  => [
                            $patient_illness->toShowApi()->resolve()
                        ]
                    ]                    
                ]
            ]))
            ->onQueue('elasticsearch')
            ->onConnection('rabbitmq');
        }            
        return $this->patient_illness_model = $patient_illness;
    }
}
