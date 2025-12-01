<?php

namespace Projects\WellmedBackbone\Schemas\SatuSehat;

use Hanafalah\SatuSehat\Schemas\SatuSehatLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\PatientIntegration as SatuSehatPatientIntegration;

class PatientIntegration extends SatuSehatLog implements SatuSehatPatientIntegration{
    protected string $__entity = 'PatientIntegration';

    public function patientIntegration(mixed $conditionals = null): Builder{
        return $this->satuSehatLog($conditionals);
    }
}