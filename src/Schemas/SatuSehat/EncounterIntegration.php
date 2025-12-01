<?php

namespace Projects\WellmedBackbone\Schemas\SatuSehat;

use Hanafalah\SatuSehat\Schemas\SatuSehatLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\EncounterIntegration as SatuSehatEncounterIntegration;

class EncounterIntegration extends SatuSehatLog implements SatuSehatEncounterIntegration{
    protected string $__entity = 'EncounterIntegration';

    public function encounterIntegration(mixed $conditionals = null): Builder{
        return $this->satuSehatLog($conditionals);
    }
}