<?php

namespace Projects\WellmedBackbone\Models\ModuleExamination\Examination;

use Hanafalah\ModuleExamination\Models\Examination\ExaminationTreatment as ExaminationExaminationTreatment;

class ExaminationTreatment extends ExaminationExaminationTreatment
{
    public function transactionItem(){return $this->hasOneModel('PosTransactionItem', 'item_id', 'reference_id')->where('item_type', $this->reference_type);}
}
