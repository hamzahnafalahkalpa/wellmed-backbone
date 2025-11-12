<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Schemas\PractitionerEvaluation as SchemasPractitionerEvaluation;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\PractitionerEvaluation as ContractPractitionerEvaluation;

class PractitionerEvaluation extends SchemasPractitionerEvaluation implements ContractPractitionerEvaluation 
{
    protected string $__entity = 'PractitionerEvaluation';
    public $practitioner_evaluation;

    public function prepareStorePractitionerEvaluation(mixed $practitioner_evaluation_dto): Model{
        $practitioner_evaluation = parent::prepareStorePractitionerEvaluation($practitioner_evaluation_dto);
        if (isset($practitioner_evaluation_dto->payment_details) && count($practitioner_evaluation_dto->payment_details) > 0) {
            foreach ($practitioner_evaluation_dto->payment_details as $payment_detail) {
                
            }
        }
        return $this->practitioner_evaluation = $practitioner_evaluation;
    }
}
