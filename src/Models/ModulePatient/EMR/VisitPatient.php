<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\EMR;

use Hanafalah\ModulePatient\Models\EMR\VisitPatient as EMRVisitPatient;

class VisitPatient extends EMRVisitPatient
{
    protected $casts = [
        'patient_id'          => 'string',
        'name'                => 'string',
        'queue_number'        => 'string',
        'created_at'          => 'datetime',
        'nik'                 => 'string',
        'dob'                 => 'immutable_date',
        'medical_record'      => 'string',
        'visited_at'          => 'datetime',
        'reported_at'         => 'datetime',
        'consument_name'      => 'string',
        'consument_phone'     => 'string',
        'medic_service_label' => 'string',
        // Props-based fields
        'payer_name'          => 'string',
        'warehouse_name'      => 'string',
        'practitioner_evaluation_name' => 'string'
    ];

    /**
     * Props query mapping for virtual attributes
     * Extends parent getPropsQuery with additional fields
     */
    public function getPropsQuery(): array
    {
        return array_merge(parent::getPropsQuery(), [
            'consument_name'  => 'props->prop_consument->name',
            'consument_phone' => 'props->prop_consument->phone',
            'payer_name'      => 'props->prop_payer->name',
            'warehouse_name'  => 'props->prop_visit_registration->prop_warehouse->name',
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
        'enabled' => true,
        'index_name' => 'visit_patient',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'visit_code',
            'queue_number',
            'flag',
            'status',

            // === Patient Information (from props) ===
            'patient_id',
            'name',
            'medical_record',
            'nik',
            'dob',

            // === Visit Details ===
            'reference_type',
            'reference_id',
            'reservation_id',
            'patient_type_service_id',

            // === Consument Info (from props) ===
            'consument_name',
            'consument_phone',

            // === Payer Info (from props) ===
            'payer_name',

            // === Service Information (from props) ===
            'medic_service_label',
            'warehouse_name',

            // === Timestamps ===
            'visited_at',
            'reported_at',
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
