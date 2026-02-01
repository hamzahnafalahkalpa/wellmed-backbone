<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Schemas\Patient as SchemasPatient;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\Patient as ModulePatientPatient;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Jobs\SendPatientToSatuSehatJob;
use Hanafalah\MicroTenant\Facades\MicroTenant;

class Patient extends SchemasPatient implements ModulePatientPatient
{
    /**
     * Hook executed after a patient is created.
     * Handles integration data, Satu Sehat payload preparation, and async sync dispatch.
     */
    protected function afterPatientCreated(Model &$patient, PatientData &$patient_dto): self
    {
        parent::afterPatientCreated($patient, $patient_dto);

        $this->handleIntegrationData($patient, $patient_dto);
        $this->loadPatientRelationships($patient);

        $payload = $this->prepareSatuSehatPayload($patient);
        $this->dispatchSatuSehatSync($patient, $payload);

        $this->fillingProps($patient, $patient_dto->props);
        $patient->save();
        return $this;
    }

    /**
     * Handle integration data attribute for the patient.
     */
    private function handleIntegrationData(Model &$patient, PatientData &$patient_dto): void
    {
        $patient_dto->props['integration'] = $patient->integration ?? $this->requestDTO(
            config('app.contracts.IntegrationData'),
            []
        );

        $integration = is_array($patient_dto->props['integration'])
            ? $patient_dto->props['integration']
            : $patient_dto->props['integration']->toArray();

        $patient->setAttribute('integration', $integration);
    }

    /**
     * Load necessary relationships for patient processing.
     */
    private function loadPatientRelationships(Model &$patient): void
    {
        $patient->load([
            'patientSatuSehat',
            'reference.addresses' => function ($query) {
                $query->with(['province', 'district', 'subdistrict', 'village']);
            }
        ]);
    }

    /**
     * Prepare the payload for Satu Sehat integration.
     */
    private function prepareSatuSehatPayload(Model $patient): array
    {
        $existingPayload = $patient->patientSatuSehat?->payload ?? [];
        $basePayload = $this->buildBasePatientPayload($patient);

        $payload = array_merge($existingPayload, $basePayload);
        $payload = $this->handleMotherNikLogic($payload, $patient);
        $payload['address'] = $this->buildAddressesPayload($patient, $payload['address'] ?? []);

        return $payload;
    }

    /**
     * Build base patient payload with essential patient information.
     */
    private function buildBasePatientPayload(Model $patient): array
    {
        return [
            'name'       => $patient->name,
            'gender'     => Str::lower($patient->prop_people['sex']),
            'nik'        => $patient->prop_people['card_identity']['nik'] ?? null,
            'passport'   => $patient->prop_people['card_identity']['passport'] ?? null,
            'birth_date' => $patient->prop_people['dob'] ?? null
        ];
    }

    /**
     * Handle the special case where nik_ibu (mother's NIK) is used instead of patient NIK.
     */
    private function handleMotherNikLogic(array $payload, Model $patient): array
    {
        if (isset($payload['nik_ibu'])) {
            unset($payload['nik']);
            $payload['nik_ibu'] = $patient->prop_people['card_identity']['nik_ibu'];
        }

        return $payload;
    }

    /**
     * Build addresses payload from patient reference addresses.
     */
    private function buildAddressesPayload(Model $patient, array $existingAddresses): array
    {
        $processedAddresses = [];

        foreach ($patient->reference->addresses ?? [] as $address) {
            $addressType = $this->getAddressType($address->flag);

            // Skip if address type already exists in payload or processed addresses
            if (!$addressType || isset($existingAddresses[$addressType]) || isset($processedAddresses[$addressType])) {
                continue;
            }

            // Only process addresses with complete location data
            if (!$this->hasCompleteLocationData($address)) {
                continue;
            }

            $processedAddresses[$addressType] = $this->transformAddressForSatuSehat($address);
        }

        return array_merge($existingAddresses, $processedAddresses);
    }

    /**
     * Map address flag to Satu Sehat address type.
     */
    private function getAddressType(string $flag): ?string
    {
        return match ($flag) {
            'KTP' => 'home',
            'RESIDENCE' => 'temp',
            default => null,
        };
    }

    /**
     * Check if address has complete location data (province, district, subdistrict, village).
     */
    private function hasCompleteLocationData($address): bool
    {
        return isset($address->province)
            && isset($address->district)
            && isset($address->subdistrict)
            && isset($address->village);
    }

    /**
     * Transform address model to Satu Sehat format.
     */
    private function transformAddressForSatuSehat($address): array
    {
        return [
            'name'          => $address->name,
            'city'          => $address->district->name ?? null,
            'postal_code'   => $address->zip_code,
            'province_code' => $this->cleanLocationCode($address->province->code ?? null),
            'city_code'     => $this->cleanLocationCode($address->district->code ?? null),
            'district_code' => $this->cleanLocationCode($address->subdistrict->code ?? null),
            'village_code'  => $this->cleanLocationCode($address->village->code ?? null),
            'rw'            => $address->rw,
            'rt'            => $address->rt,
        ];
    }

    /**
     * Clean location code by removing dots.
     */
    private function cleanLocationCode(?string $code): ?string
    {
        return $code ? Str::replace('.', '', $code) : null;
    }

    /**
     * Dispatch patient data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $patient, array $payload): void
    {
        if (!config('module-patient.satu-sehat.enable', true)) {
            return;
        }

        $tenant_id = tenancy()->tenant->getKey();
        $patient_id = $patient->getKey();
        try {
            dispatch(new SendPatientToSatuSehatJob(
                $tenant_id,
                $patient_id,
                $payload
            ))->onQueue('satusehat')->onConnection(config('queue.default','rabbitmq'));

            Log::channel('satu-sehat')->info('Patient queued for Satu Sehat sync', [
                'patient_id' => $patient_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            Log::channel('satu-sehat')->error('Failed to queue patient for Satu Sehat', [
                'patient_id' => $patient_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}