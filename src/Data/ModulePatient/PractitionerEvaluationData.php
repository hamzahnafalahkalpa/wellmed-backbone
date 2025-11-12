<?php

namespace Projects\WellmedBackbone\Data\ModulePatient;

use Hanafalah\ModulePatient\Data\PractitionerEvaluationData as ModulePatientDataPractitionerEvaluationData;
use Hanafalah\ModulePayment\Data\PaymentDetailData;
use Projects\WellmedBackbone\Contracts\Data\ModulePatient\PractitionerEvaluationData as DataPractitionerEvaluationData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PractitionerEvaluationData extends ModulePatientDataPractitionerEvaluationData implements DataPractitionerEvaluationData{
    #[MapInputName('payment_details')]
    #[MapName('payment_details')]
    #[DataCollectionOf(PaymentDetailData::class)]
    public ?array $payment_details = null;

    public static function before(array &$attributes){
        $new = static::new();

    }

    public static function after(mixed $data): PractitionerEvaluationData{
        $new = static::new();
        return $data;
    }
}