<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DoctorNote;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Carbon;

class DoctorNoteFactory extends Factory
{
    protected $model = DoctorNote::class;

    public function definition(): array
    {
        $chiefs = ['แขนขาอ่อนแรงข้างขวา', 'พูดไม่ชัด', 'ปากเบี้ยว', 'ชาครึ่งซีกซ้าย', 'เวียนศีรษะบ้านหมุน', 'ตามัวฉับพลัน'];
        $diagnoses = ['Ischemic stroke', 'Hemorrhagic stroke', 'TIA'];
        $diffs = ['อัมพาตจากไมเกรน', 'ชักตามด้วยอ่อนแรงชั่วคราว', 'Bell’s palsy', 'ภาวะน้ำตาลต่ำ', 'Peripheral vertigo'];
        $examSnippets = ['กลอกตาผิดปกติ', 'แรงกล้ามเนื้อลดลง MRC 3/5', 'Babinski positive', 'การรับรู้คำสั่งลดลง', 'พูดไม่ชัด dysarthria', 'กลืนลำบาก'];
        $imagingSnippets = ['NCCT ไม่พบเลือดออก', 'CTA พบการอุดตันที่ M1', 'MRI DWI hyperintensity บริเวณ MCA territory', 'พบจุดเลือดออกเล็กน้อยที่ basal ganglia', 'CTP พบ mismatch ชัดเจน'];
        $plans = ['เริ่มยาต้านเกล็ดเลือดและสแตติน', 'เตรียมพิจารณา IVT ตามข้อบ่งชี้', 'ส่งต่อทีม EVT ด่วน', 'ควบคุมความดันตามเป้าหมาย', 'เฝ้าระวังทางเดินหายใจและสำลัก'];
        $ordersList = ['Admit Stroke Unit', 'สั่งตรวจแลบ CBC, PT/INR, Creatinine, Glucose', 'เฝ้าระวัง NIHSS ทุก 4 ชม.', 'NPO จนกว่าจะผ่านการคัดกรองกลืน', 'ควบคุม BP ตาม protocol'];
        $rxNotes = ['ให้ Aspirin 300 mg stat, ตามด้วย 81 mg OD', 'ให้ Atorvastatin 40 mg HS', 'พิจารณา Clopidogrel loading ถ้าไม่มีข้อห้าม', 'ให้ยาละลายลิ่มเลือดตามน้ำหนักถ้าเข้าเกณฑ์'];

        $status = $this->faker->randomElement([
            DoctorNote::STATUS_PLANNED,
            DoctorNote::STATUS_IN_PROGRESS,
            DoctorNote::STATUS_SIGNED_OFF,
            DoctorNote::STATUS_CANCELLED,
        ]);

        $now = Carbon::now();
        $scheduledFor = null;
        $recordedAt = null;
        $signedOffAt = null;

        if ($status === DoctorNote::STATUS_PLANNED) {
            $scheduledFor = $this->faker->dateTimeBetween('+1 hour', '+3 days');
        } elseif ($status === DoctorNote::STATUS_IN_PROGRESS) {
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', 'now');
            $recordedAt = $this->faker->dateTimeBetween($scheduledFor, 'now');
        } elseif ($status === DoctorNote::STATUS_SIGNED_OFF) {
            $scheduledFor = $this->faker->dateTimeBetween('-3 days', '-2 hours');
            $recordedAt = $this->faker->dateTimeBetween($scheduledFor, '-1 hour');
            $signedOffAt = $this->faker->dateTimeBetween($recordedAt, 'now');
        } else {
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', '+2 days');
        }

        $patientId = Patient::inRandomOrder()->value('id') ?? Patient::factory();
        $doctorId = User::where('requested_role', 'doctor')->inRandomOrder()->value('id')
            ?? User::factory()->state(['requested_role' => 'doctor']);

        $nihss = $this->faker->numberBetween(0, 20);
        $gcs = $this->faker->numberBetween(10, 15);
        $lvo = $this->faker->boolean(40);

        return [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'status' => $status,
            'scheduled_for' => $scheduledFor,
            'recorded_at' => $recordedAt,
            'signed_off_at' => $signedOffAt,
            'chief_complaint' => $this->faker->randomElement($chiefs),
            'diagnosis' => $this->faker->randomElement($diagnoses),
            'differential_diagnosis' => $this->faker->randomElement($diffs),
            'clinical_summary' => 'อาการเริ่มเมื่อ ' . $this->faker->numberBetween(1, 6) . ' ชั่วโมงก่อนมา ร่วมกับ ' . $this->faker->randomElement($chiefs) . ' มีปัจจัยเสี่ยง HT/DM ตามประวัติญาติให้ข้อมูล',
            'physical_exam' => $this->faker->randomElement($examSnippets),
            'nihss_score' => $nihss,
            'gcs_score' => $gcs,
            'imaging_summary' => $this->faker->randomElement($imagingSnippets),
            'lvo_suspected' => $lvo,
            'treatment_plan' => $this->faker->randomElement($plans),
            'orders' => $this->faker->randomElement($ordersList),
            'prescription_note' => $this->faker->randomElement($rxNotes),
            'created_by_ip' => $this->faker->ipv4(),
            'updated_by_ip' => $this->faker->ipv4(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    public function planned(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_PLANNED,
            'recorded_at' => null,
            'signed_off_at' => null,
            'scheduled_for' => Carbon::now()->addDay(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_IN_PROGRESS,
            'recorded_at' => Carbon::now(),
            'signed_off_at' => null,
            'scheduled_for' => Carbon::now()->subHours(2),
        ]);
    }

    public function signedOff(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_SIGNED_OFF,
            'recorded_at' => Carbon::now()->subHour(),
            'signed_off_at' => Carbon::now(),
            'scheduled_for' => Carbon::now()->subHours(2),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_CANCELLED,
            'recorded_at' => null,
            'signed_off_at' => null,
        ]);
    }
}
