<?php

namespace Projects\WellmedBackbone\Exports;

use Hanafalah\LaravelSupport\Jobs\ProcessExportJob;
use Hanafalah\LaravelSupport\Models\Export\Export;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitRegistrationEmrExport
{
    /**
     * The schema instance (VisitRegistration schema).
     *
     * @var mixed
     */
    protected $schema;

    /**
     * Create a new export instance.
     *
     * @param mixed $schema The VisitRegistration schema instance
     */
    public function __construct($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Handle the export request.
     * Creates an export record and dispatches the job.
     *
     * @return Export
     */
    public function handle(): Export
    {
        // Get the visit registration model from schema
        $visitRegistration = $this->schema->entityData();
        if (!$visitRegistration) {
            throw new \Exception('Visit registration not found');
        }

        // Get tenant ID
        $tenantId = tenancy()->tenant->id ?? null;

        if (!$tenantId) {
            throw new \Exception('Tenant context not found');
        }

        // Get current user ID (if available)
        $userId = auth()->user()->id ?? null;

        // Calculate expiration date (90 days / 3 months from now)
        $expiresAt = now()->addDays(90);

        // Create export record
        $export = app(config('database.models.Export'))->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'export_type' => 'VisitRegistrationEmr',
            'reference_type' => get_class($visitRegistration),
            'reference_id' => $visitRegistration->id,
            'status' => \Hanafalah\LaravelSupport\Enums\Export\ExportStatus::PENDING,
            'metadata' => [
                'visit_registration_code' => $visitRegistration->visit_registration_code ?? null,
            ],
            'expires_at' => $expiresAt,
        ]);
        // Dispatch job to process export
        ProcessExportJob::dispatch($export->id, $tenantId)->onConnection('sync');

        return $export;
    }

    /**
     * Generate the PDF export synchronously and return as response.
     * This method does not store the file to S3 or create export records.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateSync(): \Illuminate\Http\Response
    {
        // Get the visit registration model from schema
        $visitRegistration = $this->schema->entityData();
        if (!$visitRegistration) {
            throw new \Exception('Visit registration not found');
        }

        // Load all necessary data directly from visit registration
        $data = $this->loadDataFromVisitRegistration($visitRegistration);

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('wellmed::exports.emr.visit-registration', $data)
            ->setOptions([
                'enable_php' => true,
                'enable_remote' => true,
            ]);

        $dompdf = $pdf->getDomPDF();
        $pdf->render();

        // Add page footer with page numbers
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

        $canvas->page_text(
            40,
            820,
            "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak pada " . date('d/m/Y H:i'),
            $font,
            9,
            [0, 0, 0]
        );

        // Build filename
        $timestamp = now()->format('YmdHis');
        $visitCode = $visitRegistration->visit_registration_code ?? 'unknown';
        $fileName = "emr_{$visitCode}_{$timestamp}.pdf";

        // Get PDF content after manual rendering (preserves page footer)
        $pdfContent = $dompdf->output();

        // Return as response with proper headers
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($pdfContent),
        ]);
    }

    /**
     * Generate the PDF export.
     * Called by the ProcessExportJob.
     *
     * @param Export $export The export record
     * @return string The file path
     */
    public function generate(Model $export): string
    {
        // Load all necessary data
        $data = $this->loadData($export);
        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('wellmed::exports.emr.visit-registration', $data)
            ->setOptions([
                'enable_php' => true,
                'enable_remote' => true,
            ]);

        $dompdf = $pdf->getDomPDF();
        $pdf->render();

        // Add page footer with page numbers
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

        $canvas->page_text(
            40,
            820,
            "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak pada " . date('d/m/Y H:i'),
            $font,
            9,
            [0, 0, 0]
        );

        // Build S3 file path: exports/tenant_{id}/{year}/{month}/{filename}
        $timestamp = now()->format('YmdHis');
        $visitCode = $export->metadata['visit_registration_code'] ?? 'unknown';
        $tenantId = tenancy()->tenant->getKey();
        $year = now()->format('Y');
        $month = now()->format('m');

        $fileName = "emr_{$visitCode}_{$timestamp}.pdf";
        $filePath = "exports/tenant_{$tenantId}/{$year}/{$month}/{$fileName}";

        // Get PDF content as string and upload directly to S3
        // No ACL is set - bucket policy controls access
        $pdfContent = $dompdf->output();

        Storage::disk('s3')->put($filePath, $pdfContent, [
            'ContentType' => 'application/pdf',
        ]);

        return $filePath;
    }

    /**
     * Load all data needed for the export.
     *
     * @param Export $export
     * @return array
     */
    protected function loadData(Export $export): array
    {
        // Get the visit registration from reference
        $visitRegistration = $export->reference;

        if (!$visitRegistration) {
            throw new \Exception('Visit registration reference not found');
        }

        return $this->loadDataFromVisitRegistration($visitRegistration);
    }

    /**
     * Load all data needed for the export directly from visit registration model.
     *
     * @param mixed $visitRegistration
     * @return array
     */
    protected function loadDataFromVisitRegistration($visitRegistration): array
    {
        // Load workspace (tenant clinic data)
        $workspace = tenancy()->tenant->reference;
        $workspace = $workspace->load($workspace->showUsingRelation());
        $workspaceData = $workspace->toShowApi()->resolve();

        // Load visit registration with all necessary relations
        $visitRegistration->load([
            'examinationSummary',
            'medicService',
            'practitionerEvaluation.practitioner',
            'practitionerEvaluation.profession',
            'visitPatient' => function ($query) {
                $query->with([
                    'reference',
                    'patient' => function ($query) {
                        $query->with([
                            'reference.addresses',
                            'cardIdentities',
                            'patientType'
                        ]);
                    }
                ]);
            },
        ]);

        // Get EMR data from examination summary
        $emr = $visitRegistration->examinationSummary?->emr ?? [];

        return [
            'workspace' => $this->transformWorkspace($workspaceData),
            'visit_registration' => $this->transformVisitRegistration($visitRegistration),
            'patient' => $this->transformPatient($visitRegistration),
            'practitioner' => $this->transformPractitioner($visitRegistration),
            'vital_signs' => $this->transformVitalSigns($emr),
            'anthropometry' => $this->transformAnthropometry($emr),
            'pain_scale' => $this->transformPainScale($emr),
            'symptoms' => $this->transformSymptoms($emr),
            'allergies' => $this->transformAllergies($emr),
            'soap' => $this->transformSoap($emr),
            'diagnoses' => $this->transformDiagnoses($emr),
            'prescriptions' => $this->transformPrescriptions($emr),
            'treatments' => $this->transformTreatments($emr),
            'history_illnesses' => $this->transformHistoryIllnesses($emr),
            'family_illnesses' => $this->transformFamilyIllnesses($emr),
        ];
    }

    /**
     * Transform workspace data.
     */
    protected function transformWorkspace($workspace): array
    {
        return [
            'name' => $workspace['name'] ?? null,
            'logo' => $workspace['logo'] ?? null,
            'address' => $workspace['address'] ?? null,
            'phone' => $workspace['phone'] ?? null,
            'email' => $workspace['email'] ?? null,
        ];
    }

    /**
     * Transform visit registration data.
     */
    protected function transformVisitRegistration($visitRegistration): array
    {
        $visitedAt = $visitRegistration->visitPatient?->visited_at;

        return [
            'id' => $visitRegistration->id,
            'visit_registration_code' => $visitRegistration->visit_registration_code ?? '-',
            'visit_date' => $visitedAt ? Carbon::parse($visitedAt)->format('d F Y H:i') : '-',
            'status' => $visitRegistration->status ?? '-',
            'medic_service' => [
                'name' => $visitRegistration->medicService?->name ?? '-',
            ],
        ];
    }

    /**
     * Transform patient data.
     */
    protected function transformPatient($visitRegistration): array
    {
        $patient = $visitRegistration->visitPatient?->patient;
        $people = $patient?->reference ?? $patient?->people;

        return [
            'name' => $people?->name ?? $patient?->name ?? '-',
            'medical_record_number' => $patient?->medical_record ?? '-',
            'nik' => $people?->card_identity['nik'] ?? $patient?->cardIdentities?->firstWhere('card_type', 'nik')?->card_number ?? '-',
            'ihs_number' => $patient?->card_identity['ihs_number'] ?? $patient?->cardIdentities?->firstWhere('card_type', 'ihs_number')?->card_number ?? '-',
            'date_of_birth' => $people?->dob ? Carbon::parse($people->dob)->format('d F Y') : '-',
            'age' => $people?->age ?? null,
            'gender' => $people?->sex == 'Male' ? 'Laki-laki' : ($people?->sex == 'Female' ? 'Perempuan' : '-'),
            'blood_type' => $people?->blood_type ?? '-',
            'phone' => $people?->phone_1 ?? '-',
            'patient_type' => $patient?->patientType?->name ?? '-',
        ];
    }

    /**
     * Transform practitioner data.
     */
    protected function transformPractitioner($visitRegistration): array
    {
        $practitionerEvaluation = $visitRegistration->practitionerEvaluation;

        return [
            'name' => $practitionerEvaluation?->name ?? $practitionerEvaluation?->practitioner?->name ?? '-',
            'profession' => $practitionerEvaluation?->profession?->name ?? '-',
            'sip_number' => $practitionerEvaluation?->practitioner?->sip_number ?? null,
        ];
    }

    /**
     * Transform vital signs data.
     */
    protected function transformVitalSigns(array $emr): array
    {
        $vitalSign = $emr['VitalSign']['exam'] ?? null;
        if (!$vitalSign) return [];

        $vitals = [];

        if (isset($vitalSign['temperature'])) {
            $status = $vitalSign['temperature_status'] ?? null;
            $vitals[] = [
                'label' => 'Suhu Tubuh',
                'value' => $vitalSign['temperature'],
                'unit' => '°C',
                'status' => $status ? ucfirst(strtolower(str_replace('_', ' ', $status))) : null,
                'status_class' => $this->getStatusClass($status),
            ];
        }

        if (isset($vitalSign['systolic']) && isset($vitalSign['diastolic'])) {
            $status = $vitalSign['blood_pressure_status'] ?? null;
            $vitals[] = [
                'label' => 'Tekanan Darah',
                'value' => $vitalSign['systolic'] . '/' . $vitalSign['diastolic'],
                'unit' => 'mmHg',
                'status' => $status ? ucfirst(strtolower(str_replace('_', ' ', $status))) : null,
                'status_class' => $this->getStatusClass($status),
            ];
        }

        if (isset($vitalSign['pulse_rate'])) {
            $vitals[] = [
                'label' => 'Denyut Nadi',
                'value' => $vitalSign['pulse_rate'],
                'unit' => 'bpm',
            ];
        }

        if (isset($vitalSign['respiration_rate'])) {
            $vitals[] = [
                'label' => 'Frekuensi Napas',
                'value' => $vitalSign['respiration_rate'],
                'unit' => 'x/menit',
            ];
        }

        if (isset($vitalSign['oxygen_saturation'])) {
            $status = $vitalSign['oxygen_status'] ?? null;
            $vitals[] = [
                'label' => 'Saturasi Oksigen',
                'value' => $vitalSign['oxygen_saturation'],
                'unit' => '%',
                'status' => $status ? ucfirst(strtolower($status)) : null,
                'status_class' => $status === 'NORMAL' ? 'normal' : 'warning',
            ];
        }

        if (isset($vitalSign['loc']['name'])) {
            $vitals[] = [
                'label' => 'Kesadaran',
                'value' => $vitalSign['loc']['name'],
                'unit' => '',
            ];
        }

        return $vitals;
    }

    /**
     * Transform anthropometry data.
     */
    protected function transformAnthropometry(array $emr): array
    {
        $anthro = $emr['Anthropometry']['exam'] ?? null;
        if (!$anthro) return [];

        $data = [];

        if (isset($anthro['weight'])) {
            $data[] = ['label' => 'Berat Badan', 'value' => $anthro['weight'], 'unit' => 'kg'];
        }
        if (isset($anthro['height'])) {
            $data[] = ['label' => 'Tinggi Badan', 'value' => $anthro['height'], 'unit' => 'cm'];
        }
        if (isset($anthro['bmi'])) {
            $data[] = [
                'label' => 'BMI',
                'value' => number_format($anthro['bmi'], 2),
                'unit' => 'kg/m²',
                'interpretation' => $anthro['bmi_category'] ?? null,
                'interpretation_class' => $this->getBmiClass($anthro['bmi']),
            ];
        }
        if (isset($anthro['ideal_weight'])) {
            $data[] = ['label' => 'Berat Ideal', 'value' => $anthro['ideal_weight'], 'unit' => 'kg'];
        }

        return $data;
    }

    /**
     * Transform pain scale data.
     */
    protected function transformPainScale(array $emr): ?array
    {
        $painScale = $emr['PainScale']['exam'] ?? null;
        if (!$painScale) return null;

        $value = (int) ($painScale['rating_scale'] ?? 0);

        return [
            'value' => $value,
            'interpretation' => $painScale['scale_result'] ?? '-',
            'badge_class' => $value <= 3 ? 'success' : ($value <= 6 ? 'warning' : 'danger'),
        ];
    }

    /**
     * Transform symptoms data.
     */
    protected function transformSymptoms(array $emr): array
    {
        $symptoms = $emr['Symptom'] ?? [];
        if (!is_array($symptoms)) return [];

        return collect($symptoms)->map(function ($symptom) {
            return [
                'name' => $symptom['exam']['name'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Transform allergies data.
     */
    protected function transformAllergies(array $emr): array
    {
        $allergies = $emr['Allergy'] ?? [];
        if (!is_array($allergies)) return [];

        return collect($allergies)->map(function ($allergy) {
            $exam = $allergy['exam'] ?? [];
            return [
                'name' => $exam['name'] ?? '-',
                'allergy_type' => $exam['allergy_type']['name'] ?? null,
                'allergen' => $exam['allergen'] ?? null,
                'severity' => $exam['allergy_scale_spell'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Transform SOAP notes.
     */
    protected function transformSoap(array $emr): array
    {
        return [
            'subjective' => $emr['SubjectNote']['exam']['note'] ?? null,
            'objective' => $emr['ObjectNote']['exam']['note'] ?? null,
            'assessment' => $emr['AssessmentNote']['exam']['note'] ?? null,
            'plan' => $emr['PlanNote']['exam']['note'] ?? null,
        ];
    }

    /**
     * Transform diagnoses data.
     */
    protected function transformDiagnoses(array $emr): array
    {
        $diagnoses = $emr['BasicDiagnose'] ?? [];
        if (!is_array($diagnoses)) return [];

        $typeLabels = [
            'InitialDiagnose' => 'Diagnosis Awal',
            'PrimaryDiagnose' => 'Diagnosis Utama',
            'SecondaryDiagnose' => 'Diagnosis Sekunder',
        ];

        return collect($diagnoses)->map(function ($diagnosis) use ($typeLabels) {
            $exam = $diagnosis['exam'] ?? [];
            $type = $exam['type'] ?? 'SecondaryDiagnose';

            return [
                'type' => str_replace('Diagnose', '', $type),
                'type_label' => $typeLabels[$type] ?? 'Diagnosis',
                'code' => $exam['code'] ?? $exam['disease_code'] ?? '-',
                'name' => $exam['name'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Transform prescriptions data.
     */
    protected function transformPrescriptions(array $emr): array
    {
        $prescriptions = $emr['BasicPrescription'] ?? [];
        if (!is_array($prescriptions)) return [];

        return collect($prescriptions)->map(function ($prescription) {
            $exam = $prescription['exam'] ?? [];
            $instruction = $exam['dosage_instruction'] ?? [];

            $timing = '';
            if (isset($instruction['divided_times']) && is_array($instruction['divided_times'])) {
                $timing = implode(', ', array_filter($instruction['divided_times']));
            }
            if (isset($instruction['consume_at']['value'])) {
                $timing .= ($timing ? ' - ' : '') . $instruction['consume_at']['value'];
            }

            return [
                'name' => $exam['name'] ?? $exam['card_stock']['name'] ?? '-',
                'qty' => $exam['qty'] ?? '-',
                'frequency' => isset($exam['frequency_qty']) ? "{$exam['frequency_qty']}x sehari" : '-',
                'timing' => $timing ?: '-',
                'indication' => $exam['indication'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Transform clinical treatments data.
     */
    protected function transformTreatments(array $emr): array
    {
        $treatments = $emr['ClinicalTreatment'] ?? [];
        if (!is_array($treatments)) return [];

        return collect($treatments)->map(function ($treatment) {
            $exam = $treatment['exam'] ?? [];

            return [
                'name' => $exam['name'] ?? $exam['treatment']['name'] ?? '-',
                'qty' => $exam['qty'] ?? '1',
                'result' => $exam['result'] ?? '-',
                'note' => $exam['note'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Transform history illnesses data.
     */
    protected function transformHistoryIllnesses(array $emr): array
    {
        $illnesses = $emr['HistoryIllness'] ?? [];
        if (!is_array($illnesses)) return [];

        return collect($illnesses)->map(function ($illness) {
            $exam = $illness['exam'] ?? [];

            return [
                'code' => $exam['code'] ?? $exam['disease_code'] ?? '-',
                'name' => $exam['name'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Transform family illnesses data.
     */
    protected function transformFamilyIllnesses(array $emr): array
    {
        $illnesses = $emr['FamilyIllness'] ?? [];
        if (!is_array($illnesses)) return [];

        return collect($illnesses)->map(function ($illness) {
            $exam = $illness['exam'] ?? [];

            return [
                'family_name' => $exam['family_name'] ?? '-',
                'name' => $exam['name'] ?? '-',
                'code' => $exam['code'] ?? $exam['disease_code'] ?? '-',
            ];
        })->toArray();
    }

    /**
     * Get status class based on status string.
     */
    protected function getStatusClass(?string $status): string
    {
        if (!$status) return 'normal';

        $status = strtoupper($status);

        if (in_array($status, ['NORMAL', 'OPTIMAL'])) return 'normal';
        if (in_array($status, ['ELEVATED', 'PREHYPERTENSION', 'FEVER', 'MODERATE', 'WARNING'])) return 'warning';

        return 'danger';
    }

    /**
     * Get BMI interpretation class.
     */
    protected function getBmiClass(float $bmi): string
    {
        if ($bmi < 18.5) return 'warning';
        if ($bmi < 25) return 'success';
        if ($bmi < 30) return 'warning';

        return 'danger';
    }
}
