<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;
use Hanafalah\ModulePatient\Schemas\VisitPatient as SchemasVisitPatient;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitPatient as ModulePatientVisitPatient;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\{
    Model
};

class VisitPatient extends SchemasVisitPatient implements ModulePatientVisitPatient
{
    protected function afterVisitPatientCreated(Model &$visit_patient_model, VisitPatientData &$visit_patient_dto): self{
        parent::afterVisitPatientCreated($visit_patient_model, $visit_patient_dto);
        return $this;
    }
}
