<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\EMR;

use Hanafalah\ModulePatient\Models\EMR\VisitExamination as EMRVisitExamination;

class VisitExamination extends EMRVisitExamination
{
    protected $casts = [
        'created_at'            => 'date',
        'visit_registration_id' => 'string',
        'visit_patient_id'      => 'string',
        'patient_id'            => 'string',
        'is_commit'             => 'boolean',
        'sign_off_at'           => 'datetime',
        // Props-based fields
        'patient_name'          => 'string',
        'patient_medical_record' => 'string',
        'patient_nik'           => 'string',
        'medic_service_label'   => 'string',
        'warehouse_name'        => 'string',
    ];

    /**
     * Props query mapping for virtual attributes
     */
    public function getPropsQuery(): array
    {
        return [
            'patient_name'           => 'props->prop_patient->name',
            'patient_medical_record' => 'props->prop_patient->medical_record',
            'patient_nik'            => 'props->prop_patient->prop_people->card_identity->nik',
            'medic_service_label'    => 'props->prop_visit_registration->prop_medic_service->label',
            'warehouse_name'         => 'props->prop_visit_registration->prop_warehouse->name',
        ];
    }

    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'visit_examination',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'visit_examination_code',
            'status',

            // === Visit References ===
            'visit_patient_id',
            'visit_registration_id',
            'patient_id',

            // === Patient Info (from props) ===
            'patient_name',
            'patient_medical_record',
            'patient_nik',

            // === Service Info (from props) ===
            'medic_service_label',
            'warehouse_name',

            // === Examination Status ===
            'is_commit',
            'is_addendum',
            'sign_off_at',

            // === Timestamps ===
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];

    public function pharmacySale(){return $this->morphOneModel('PharmacySale', 'reference');}
}
