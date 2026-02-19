<?php

namespace Projects\WellmedBackbone\Models\ModuleExamination;

use Hanafalah\ModuleExamination\Models\Examination\PatientIllness as ExaminationPatientIllness;

class PatientIllness extends ExaminationPatientIllness
{
    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'patient_illness',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'name',

            // === Patient Information ===
            'patient_id',

            // === Disease Information ===
            'disease_type',
            'disease_id',
            'disease_name',
            'classification_disease_id',

            // === Reference ===
            'reference_type',
            'reference_id',

            // === Summary References ===
            'examination_summary_id',
            'patient_summary_id',

            // === Timestamps ===
            'created_at',
            'updated_at',
        ],
        'hydrate' => false,
    ];
}
