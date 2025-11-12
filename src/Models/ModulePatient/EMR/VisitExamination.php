<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\EMR;

use Hanafalah\ModulePatient\Models\EMR\VisitExamination as EMRVisitExamination;

class VisitExamination extends EMRVisitExamination
{
    public function pharmacySale(){return $this->morphOneModel('PharmacySale', 'reference');}
}
