<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\EMR;

use Hanafalah\ModulePatient\Models\EMR\VisitRegistration as EMRVisitRegistration;

class VisitRegistration extends EMRVisitRegistration
{
    /**
     * Elasticsearch configuration
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => false,
        'index_name' => 'visit_registration',
        'variables' => [
            'id',
            'visit_registration_code',
            'patient_id',
            'patient_name',
            'patient_nik',
            'medic_service_id',
            'medic_service_label',
            'service_cluster_label',
            'status',
            'created_at'
        ],
        'hydrate' => false,
    ];
}
