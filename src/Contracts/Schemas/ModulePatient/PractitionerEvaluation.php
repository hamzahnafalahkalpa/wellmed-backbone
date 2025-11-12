<?php

namespace Projects\WellmedBackbone\Contracts\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Schemas\PractitionerEvaluation as SchemasPractitionerEvaluation;
use Illuminate\Database\Eloquent\Model;

interface PractitionerEvaluation extends SchemasPractitionerEvaluation{
    public function prepareStorePractitionerEvaluation(mixed $practitioner_evaluation_dto): Model;
}