<?php

namespace Projects\WellmedBackbone\Schemas\ModuleExamination;

use Hanafalah\ModuleExamination\Contracts\Data\Examination\PatientIllnessData;
use Hanafalah\ModuleExamination\Schemas\Examination\PatientIllness as ExaminationPatientIllness;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleExamination\PatientIllness as ModuleExaminationPatientIllness;
use Projects\WellmedBackbone\Services\ReportingService;

class PatientIllness extends ExaminationPatientIllness implements ModuleExaminationPatientIllness
{
    public function prepareStorePatientIllness(PatientIllnessData $patient_illness_dto): Model
    {
        $patient_illness = parent::prepareStorePatientIllness($patient_illness_dto);
        $visit_examination_model = $patient_illness_dto->visit_examination_model;

        // Index to Elasticsearch when examination is signed off
        if (isset($visit_examination_model->sign_off_at) && config('elasticsearch.enabled', false)) {
            $reportingService = app(ReportingService::class);
            $reportingService->indexPatientIllness($patient_illness->toShowApi()->resolve());
        }

        return $this->patient_illness_model = $patient_illness;
    }
}
