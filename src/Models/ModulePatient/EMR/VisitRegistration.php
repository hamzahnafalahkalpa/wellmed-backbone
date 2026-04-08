<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\EMR;

use Hanafalah\ModulePatient\Models\EMR\VisitRegistration as EMRVisitRegistration;

class VisitRegistration extends EMRVisitRegistration
{
    protected $casts = [
        'service_label_id'             => 'string',
        'patient_id'                   => 'string',
        'patient_name'                 => 'string',
        'patient_nik'                  => 'string',
        'medic_service_id'             => 'string',
        'medic_service_name'           => 'string',
        'medic_service_label'          => 'string',
        'service_cluster_label'        => 'string',
        'visit_patient_reference_type' => 'string',
        'warehouse_type'               => 'string',
        'warehouse_id'                 => 'string',
        'warehouse_name'               => 'string',
        'created_at'                   => 'date',
        'status'                       => 'string',
        // Props-based fields
        'patient_medical_record'       => 'string',
        'practitioner_name'            => 'string',
    ];

    /**
     * Props query mapping for virtual attributes
     * Extends parent getPropsQuery with additional fields
     */
    public function getPropsQuery(): array
    {
        return array_merge(parent::getPropsQuery(), [
            'warehouse_name'          => 'props->prop_warehouse->name',
            'patient_medical_record'  => 'props->prop_visit_patient->patient->medical_record',
            'practitioner_name'       => 'props->prop_practitioner_evaluation->prop_practitioner->name',
        ]);
    }

    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => false,
        'index_name' => 'visit_registration',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'visit_registration_code',
            'status',

            // === Visit Reference ===
            'visit_patient_id',
            'visit_patient_type',
            'visit_patient_reference_type',

            // === Patient Information (from props) ===
            'patient_id',
            'patient_name',
            'patient_nik',
            'patient_medical_record',

            // === Service Information (from props) ===
            'service_cluster_id',
            'service_cluster_label',
            'medic_service_id',
            'medic_service_label',

            // === Warehouse/Location (from props) ===
            'warehouse_type',
            'warehouse_id',
            'warehouse_name',

            // === Practitioner (from props) ===
            'practitioner_name',

            // === Timestamps ===
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
