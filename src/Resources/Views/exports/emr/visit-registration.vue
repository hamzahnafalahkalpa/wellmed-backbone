<script setup lang="ts">
interface Props {
    data: any
}

const props = defineProps<Props>()
const reportEl = ref<HTMLElement>()
const { printEl } = usePrintReport()

// --- Data accessors ---
const patient = computed(() => props.data?.visit_patient?.patient)
const people = computed(() => patient.value?.people)
const emr = computed(() => props.data?.examination_summary?.emr)

const subjectNote = computed(() => emr.value?.SubjectNote)
const objectNote = computed(() => emr.value?.ObjectNote)
const assessmentNote = computed(() => emr.value?.AssessmentNote)
const planNote = computed(() => emr.value?.PlanNote)
const painScale = computed(() => emr.value?.PainScale)
const vitalSign = computed(() => emr.value?.VitalSign)
const anthropometry = computed(() => emr.value?.Anthropometry)
const symptoms = computed(() => emr.value?.Symptom || [])
const allergies = computed(() => emr.value?.Allergy || [])
const historyIllness = computed(() => emr.value?.HistoryIllness || [])
const familyIllness = computed(() => emr.value?.FamilyIllness || [])
const basicDiagnose = computed(() => emr.value?.BasicDiagnose || [])
const basicPrescription = computed(() => emr.value?.BasicPrescription || [])
const clinicalTreatment = computed(() => emr.value?.ClinicalTreatment || [])

// --- Formatting helpers ---
const fmtDate = (date: string) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const fmtPrice = (value: number) => {
    if (value == null) return '-'
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}

const fmtNow = () => {
    return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// --- Status / badge helpers ---
const vitalStatusClass = (status: string) => {
    if (!status) return ''
    if (status === 'NORMAL') return 'status-normal'
    if (status.includes('CRISIS') || status === 'CRITICAL') return 'status-critical'
    return 'status-warning'
}

const diagnoseClass = (type: string) => {
    if (type === 'InitialDiagnose') return 'badge-initial'
    if (type === 'PrimaryDiagnose') return 'badge-primary'
    return 'badge-secondary'
}

const diagnoseLabel = (type: string) => {
    if (type === 'InitialDiagnose') return 'Awal'
    if (type === 'PrimaryDiagnose') return 'Utama'
    return 'Sekunder'
}

const rxTypeTag = (type: string) => {
    if (type === 'MedicinePrescription') return { label: 'Obat', cls: 'rpt-tag-blue' }
    if (type === 'MixPrescription') return { label: 'Racikan', cls: 'rpt-tag-purple' }
    return { label: 'Alat Medis', cls: 'rpt-tag-yellow' }
}

// --- Exposed print method ---
const print = () => {
    if (!reportEl.value) return
    printEl(reportEl.value, 'Hasil Pemeriksaan')
}

defineExpose({ print })
</script>

<template>
    <!-- Hidden container; only used to capture innerHTML for the print window -->
    <div ref="reportEl" style="display: none">
        <div class="rpt-wrap">

            <!-- ====== HEADER ====== -->
            <div class="rpt-header">
                <h1>Hasil Pemeriksaan Medis</h1>
                <div class="rpt-subtitle">{{ props.data?.medic_service?.name || 'Pelayanan Umum' }}</div>
                <div class="rpt-meta">
                    <span>Tanggal: {{ fmtDate(props.data?.visit_patient?.visited_at) }}</span>
                    <span>Dokter: {{ props.data?.practitioner_evaluation?.name }} ({{ props.data?.practitioner_evaluation?.profession?.name }})</span>
                    <span>Peran: {{ props.data?.practitioner_evaluation?.role_as }}</span>
                </div>
            </div>

            <!-- ====== PATIENT INFO ====== -->
            <div class="rpt-info-box">
                <h3>Informasi Pasien</h3>
                <div class="rpt-info-grid">
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Nama</span>
                        <span class="rpt-info-val">{{ people?.name || '-' }}</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">NIK</span>
                        <span class="rpt-info-val">{{ people?.card_identity?.nik || '-' }}</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Tanggal Lahir</span>
                        <span class="rpt-info-val">{{ fmtDate(people?.dob) }} ({{ people?.age }} Thn)</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Jenis Kelamin</span>
                        <span class="rpt-info-val">{{ people?.sex === 'Male' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">Tempat Lahir</span>
                        <span class="rpt-info-val">{{ people?.pob || '-' }}</span>
                    </div>
                    <div class="rpt-info-row">
                        <span class="rpt-info-lbl">No. IHS</span>
                        <span class="rpt-info-val">{{ patient?.card_identity?.ihs_number || '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- ====== SOAP + Pain Scale ====== -->
            <div class="rpt-section">
                <div class="rpt-sec-title">Catatan Klinis (SOAP)</div>
                <div class="rpt-sec-body">
                    <div class="rpt-soap-grid">
                        <div class="rpt-soap-card rpt-soap-S">
                            <div class="rpt-soap-lbl">S – Subjektif</div>
                            <div class="rpt-soap-txt">{{ subjectNote?.exam?.note || '-' }}</div>
                        </div>
                        <div class="rpt-soap-card rpt-soap-O">
                            <div class="rpt-soap-lbl">O – Objektif</div>
                            <div class="rpt-soap-txt">{{ objectNote?.exam?.note || '-' }}</div>
                        </div>
                        <div class="rpt-soap-card rpt-soap-A">
                            <div class="rpt-soap-lbl">A – Assessment</div>
                            <div class="rpt-soap-txt">{{ assessmentNote?.exam?.note || '-' }}</div>
                        </div>
                        <div class="rpt-soap-card rpt-soap-P">
                            <div class="rpt-soap-lbl">P – Plan</div>
                            <div class="rpt-soap-txt">{{ planNote?.exam?.note || '-' }}</div>
                        </div>
                    </div>

                    <!-- Pain Scale bar -->
                    <div v-if="painScale" class="rpt-pain-bar">
                        <span class="pain-lbl">Skala Nyeri:</span>
                        <span class="pain-val">{{ painScale.exam?.rating_scale }}</span>
                        <span class="pain-desc">{{ painScale.exam?.scale_result }}</span>
                    </div>
                </div>
            </div>

            <!-- ====== VITAL SIGNS ====== -->
            <div v-if="vitalSign" class="rpt-section">
                <div class="rpt-sec-title">Tanda Vital</div>
                <div class="rpt-sec-body">
                    <div class="rpt-vitals-grid">
                        <!-- Temperature -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Suhu Tubuh</div>
                            <div class="rpt-vital-val">{{ vitalSign.exam?.temperature }}°C</div>
                            <div class="rpt-vital-unit">{{ vitalSign.exam?.temperature_type }}</div>
                            <div v-if="vitalSign.exam?.temperature_status"
                                class="rpt-vital-status" :class="vitalStatusClass(vitalSign.exam.temperature_status)">
                                {{ vitalSign.exam.temperature_status }}
                            </div>
                        </div>
                        <!-- Blood Pressure -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Tekanan Darah</div>
                            <div class="rpt-vital-val">{{ vitalSign.exam?.systolic }}/{{ vitalSign.exam?.diastolic }}</div>
                            <div class="rpt-vital-unit">mmHg</div>
                            <div v-if="vitalSign.exam?.blood_pressure_status"
                                class="rpt-vital-status" :class="vitalStatusClass(vitalSign.exam.blood_pressure_status)">
                                {{ vitalSign.exam.blood_pressure_status }}
                            </div>
                        </div>
                        <!-- Pulse -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Detak Nadi</div>
                            <div class="rpt-vital-val">{{ vitalSign.exam?.pulse_rate }}</div>
                            <div class="rpt-vital-unit">bpm</div>
                        </div>
                        <!-- Respiration -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Frekuensi Napas</div>
                            <div class="rpt-vital-val">{{ vitalSign.exam?.respiration_rate }}</div>
                            <div class="rpt-vital-unit">bpm</div>
                        </div>
                        <!-- O2 Sat -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Saturasi O2</div>
                            <div class="rpt-vital-val">{{ vitalSign.exam?.oxygen_saturation }}%</div>
                            <div class="rpt-vital-unit">SpO2</div>
                            <div v-if="vitalSign.exam?.oxygen_status"
                                class="rpt-vital-status" :class="vitalStatusClass(vitalSign.exam.oxygen_status)">
                                {{ vitalSign.exam.oxygen_status }}
                            </div>
                        </div>
                        <!-- LOC -->
                        <div class="rpt-vital">
                            <div class="rpt-vital-lbl">Kesadaran</div>
                            <div class="rpt-vital-val" style="font-size:12px">{{ vitalSign.exam?.loc?.name || '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== ANTHROPOMETRY ====== -->
            <div v-if="anthropometry" class="rpt-section">
                <div class="rpt-sec-title">Antropometri</div>
                <div class="rpt-sec-body">
                    <div class="rpt-anthro-grid">
                        <div class="rpt-anthro-item"><span class="a-lbl">Berat Badan</span><span class="a-val">{{ anthropometry.exam?.weight }} kg</span></div>
                        <div class="rpt-anthro-item"><span class="a-lbl">Tinggi Badan</span><span class="a-val">{{ anthropometry.exam?.height }} cm</span></div>
                        <div class="rpt-anthro-item"><span class="a-lbl">BMI</span><span class="a-val">{{ anthropometry.exam?.bmi }} ({{ anthropometry.exam?.bmi_category }})</span></div>
                        <div class="rpt-anthro-item"><span class="a-lbl">WHR</span><span class="a-val">{{ anthropometry.exam?.whr }} ({{ anthropometry.exam?.whr_risk }})</span></div>
                        <div class="rpt-anthro-item"><span class="a-lbl">Habitus Tubuh</span><span class="a-val">{{ anthropometry.exam?.body_frame }}</span></div>
                        <div class="rpt-anthro-item"><span class="a-lbl">BB Ideal</span><span class="a-val">{{ anthropometry.exam?.ideal_weight }} kg</span></div>
                        <!-- Circumferences (conditional) -->
                        <div v-if="anthropometry.exam?.head_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Kepala</span><span class="a-val">{{ anthropometry.exam.head_circumference }} cm</span></div>
                        <div v-if="anthropometry.exam?.chest_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Dada</span><span class="a-val">{{ anthropometry.exam.chest_circumference }} cm</span></div>
                        <div v-if="anthropometry.exam?.waist_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Pinggang</span><span class="a-val">{{ anthropometry.exam.waist_circumference }} cm</span></div>
                        <div v-if="anthropometry.exam?.hip_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Panggul</span><span class="a-val">{{ anthropometry.exam.hip_circumference }} cm</span></div>
                        <div v-if="anthropometry.exam?.arm_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Lengan</span><span class="a-val">{{ anthropometry.exam.arm_circumference }} cm</span></div>
                        <div v-if="anthropometry.exam?.calf_circumference" class="rpt-anthro-item"><span class="a-lbl">Lingkar Betis</span><span class="a-val">{{ anthropometry.exam.calf_circumference }} cm</span></div>
                    </div>
                </div>
            </div>

            <!-- ====== SYMPTOMS ====== -->
            <div v-if="symptoms.length" class="rpt-section">
                <div class="rpt-sec-title">Keluhan / Simtom</div>
                <div class="rpt-sec-body">
                    <span v-for="s in symptoms" :key="s.id" class="rpt-tag rpt-tag-blue">{{ s.exam?.name }}</span>
                </div>
            </div>

            <!-- ====== ALLERGIES ====== -->
            <div v-if="allergies.length" class="rpt-section">
                <div class="rpt-sec-title">Alergi</div>
                <div class="rpt-sec-body">
                    <div v-for="a in allergies" :key="a.id" class="rpt-allergy-row">
                        <div class="a-tags">
                            <span class="rpt-tag rpt-tag-red">{{ a.exam?.allergy_type?.label }}</span>
                            <span class="a-name">{{ a.exam?.name }}</span>
                            <span class="rpt-tag rpt-tag-yellow">{{ a.exam?.allergy_scale_spell }}</span>
                        </div>
                        <div class="rpt-allergy-sub">Allergen: {{ a.exam?.allergen || '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- ====== MEDICAL HISTORY ====== -->
            <div v-if="historyIllness.length || familyIllness.length" class="rpt-section">
                <div class="rpt-sec-title">Riwayat Penyakit</div>
                <div class="rpt-sec-body">
                    <div v-if="historyIllness.length">
                        <div class="rpt-hist-label">Pribadi:</div>
                        <div v-for="item in historyIllness" :key="item.id" class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-blue">{{ item.exam?.disease_code }}</span>
                            <span>{{ item.exam?.name }}</span>
                        </div>
                    </div>
                    <div v-if="familyIllness.length" style="margin-top: 8px">
                        <div class="rpt-hist-label">Keluarga:</div>
                        <div v-for="item in familyIllness" :key="item.id" class="rpt-hist-row">
                            <span class="rpt-tag rpt-tag-purple">{{ item.exam?.disease_code }}</span>
                            <span>{{ item.exam?.name }}</span>
                            <span class="rpt-hist-sub">({{ item.exam?.family_name }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== DIAGNOSES ====== -->
            <div v-if="basicDiagnose.length" class="rpt-section">
                <div class="rpt-sec-title">Diagnosa</div>
                <div class="rpt-sec-body">
                    <div v-for="dg in basicDiagnose" :key="dg.id" class="rpt-dg-row">
                        <span class="rpt-dg-badge" :class="diagnoseClass(dg.exam?.type)">{{ diagnoseLabel(dg.exam?.type) }}</span>
                        <div>
                            <div class="rpt-dg-code">{{ dg.exam?.disease_code }} – {{ dg.exam?.name }}</div>
                            <div class="rpt-dg-name">{{ dg.exam?.classification_disease?.local_name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== PRESCRIPTIONS ====== -->
            <div v-if="basicPrescription.length" class="rpt-section">
                <div class="rpt-sec-title">Resep</div>
                <div class="rpt-sec-body">
                    <div v-for="(rx, idx) in basicPrescription" :key="rx.id || idx" class="rpt-rx-card">
                        <div class="rpt-rx-header">
                            <span class="rpt-rx-badge">Rx</span>
                            <span class="rpt-rx-name">{{ rx.exam?.name || rx.exam?.card_stock?.name }}</span>
                            <span class="rpt-tag" :class="rxTypeTag(rx.exam?.type).cls">{{ rxTypeTag(rx.exam?.type).label }}</span>
                        </div>
                        <div class="rpt-rx-details">
                            <div><span class="d-lbl">Jumlah:</span> <span class="d-val">{{ rx.exam?.qty }} unit</span></div>
                            <div><span class="d-lbl">Frekuensi:</span> <span class="d-val">{{ rx.exam?.frequency_qty }}x / {{ rx.exam?.frequency_division }} jam</span></div>
                            <div><span class="d-lbl">Waktu:</span> <span class="d-val">{{ (rx.exam?.dosage_instruction?.divided_times || []).filter(Boolean).join(', ') || '-' }}</span></div>
                            <div><span class="d-lbl">Konsumsi:</span> <span class="d-val">{{ rx.exam?.dosage_instruction?.consume_at?.value || '-' }}</span></div>
                            <div><span class="d-lbl">Iterasi:</span> <span class="d-val">{{ rx.exam?.iteration }}</span></div>
                            <div><span class="d-lbl">Durasi:</span> <span class="d-val">{{ rx.exam?.treatment_duration }} hari</span></div>
                            <div><span class="d-lbl">Harga:</span> <span class="d-val">{{ fmtPrice(rx.exam?.price) }}</span></div>
                        </div>
                        <div v-if="rx.exam?.indication" class="rpt-rx-indication">
                            Indikasi: {{ rx.exam.indication }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== CLINICAL TREATMENTS ====== -->
            <div v-if="clinicalTreatment.length" class="rpt-section">
                <div class="rpt-sec-title">Tindakan Klinis</div>
                <div class="rpt-sec-body">
                    <div v-for="(t, idx) in clinicalTreatment" :key="t.id || idx" class="rpt-treatment-row">
                        <div>
                            <span class="rpt-treatment-name">{{ t.exam?.name }}</span>
                            <span class="rpt-treatment-qty">x{{ t.exam?.qty }}</span>
                        </div>
                        <div class="rpt-treatment-right">
                            <span v-if="t.exam?.result" class="rpt-treatment-result">{{ t.exam.result }}</span>
                            <span class="rpt-tag" :class="t.exam?.status === 'DRAFT' ? 'rpt-tag-yellow' : 'rpt-tag-green'">{{ t.exam?.status }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== FOOTER / SIGNATURE ====== -->
            <div class="rpt-footer">
                <div class="rpt-sig-block">
                    <div class="rpt-sig-line"></div>
                    <div class="rpt-sig-name">{{ props.data?.practitioner_evaluation?.name }}</div>
                    <div class="rpt-sig-title">{{ props.data?.practitioner_evaluation?.profession?.name }} – {{ props.data?.practitioner_evaluation?.role_as }}</div>
                </div>
                <div class="rpt-footer-note">
                    Laporan dicetak pada {{ fmtNow() }}<br>
                    Dokumen resmi dari sistem WELLMED
                </div>
            </div>

        </div><!-- end rpt-wrap -->
    </div><!-- end hidden container -->
</template>