<?php

namespace Projects\WellmedBackbone\Models\ModulePatient\Patient;

use Hanafalah\ModulePatient\Models\Patient\Patient as PatientPatient;
use Hanafalah\ModulePayment\Concerns\HasConsument;
use Projects\WellmedBackbone\Transformers\Patient\{ViewPatient,ShowPatient};

class Patient extends PatientPatient
{
    use HasConsument;

    public function getFilterCasts(): array{
        return collect($this->getCasts())
            ->except([
                'patient_occupation_name',
                'payer_id',
                'patient_type_name',
                'payer_name',
                'first_name',
                'last_name'
            ])
            ->toArray();
    }

    public function getCustomFilterOptions(string $filter_name): array{
        $result = [];
        switch ($filter_name) {
            case 'patient_occupation_id':
                $result = $this->PatientOccupationModel()->get()
                        ->map(fn ($item) => [
                            'value' => $item->id,
                            'label' => $item->name,
                        ])->toArray();
            break;
            case 'patient_type_id':
                $result = $this->PatientTypeModel()->get()
                        ->map(fn ($item) => [
                            'value' => $item->id,
                            'label' => $item->name,
                        ])->toArray();
            break;
        }
        return $result;
    }

    /**
     * Elasticsearch configuration
     *
     * Comprehensive field list for both search AND reporting.
     * Uses Approach 1: One unified index with all fields.
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'patient',
        'variables' => [
            // === Core Identity Fields ===
            'id',
            'name',
            'medical_record',
            'old_mr',

            // === Name Components (from props) ===
            'first_name',
            'last_name',

            // === Demographics (from props) ===
            'dob',                      // Date of birth (immutable_date)
            'pob',                      // Place of birth (if available in props)

            // === Identity Documents (from props) ===
            'nik',                      // Indonesian national ID
            'nik_ibu',                  // Mother's NIK
            'passport',                 // Passport number

            // === Patient Classification ===
            'reference_type',           // Type: People, Animal, etc.
            'reference_id',             // Reference to People/Animal model
            'patient_type_id',          // Patient type (UMUM, VIP, etc.)
            'patient_occupation_id',    // Occupation ID
            'patient_occupation_name',  // Occupation name (from props)

            // === Payer/Insurance (from props) ===
            'payer_name',              // Insurance/payer name

            // === Timestamps (for reporting date filters) ===
            'created_at',              // Registration date - important for reports!
            'updated_at',              // Last update date
        ],
        'hydrate' => false,
    ];

    public function showUsingRelation(): array{
        return $this->mergeArray(parent::viewUsingRelation(),parent::showUsingRelation(),['consument' => function($query){
            $query->with([
                'paymentSummary','userWallet'
            ]);
        }]);
    }

    public function getViewResource(){return ViewPatient::class;}
    public function getShowResource(){return ShowPatient::class;}

    public function patientSatuSehat(){return $this->morphOneModel('PatientSatuSehat','reference');}
}
