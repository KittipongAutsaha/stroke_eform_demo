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

        $scheduledFor = null;
        $recordedAt = null;
        $signedOffAt = null;

        if ($status === DoctorNote::STATUS_PLANNED) {
            $scheduledFor = $this->faker->dateTimeBetween('+1 hour', '+3 days');
            $recordedAt = Carbon::instance($scheduledFor);
        } elseif ($status === DoctorNote::STATUS_IN_PROGRESS) {
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', 'now');
            $recordedAt = $this->faker->dateTimeBetween($scheduledFor, 'now');
        } elseif ($status === DoctorNote::STATUS_SIGNED_OFF) {
            $scheduledFor = $this->faker->dateTimeBetween('-3 days', '-2 hours');
            $recordedAt = $this->faker->dateTimeBetween($scheduledFor, '-1 hour');
            $signedOffAt = $this->faker->dateTimeBetween($recordedAt, 'now');
        } else {
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', '+2 days');
            $recordedAt = Carbon::now();
            $signedOffAt = Carbon::now();
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
            'clinical_summary' => $this->faker->boolean(70)
                ? ('อาการเริ่มเมื่อ ' . $this->faker->numberBetween(1, 6) . ' ชั่วโมงก่อนมา ร่วมกับ ' . $this->faker->randomElement($chiefs) . ' มีปัจจัยเสี่ยง HT/DM ตามประวัติญาติให้ข้อมูล')
                : null,
            'physical_exam' => $this->faker->randomElement($examSnippets),
            'nihss_score' => $this->faker->boolean(80) ? $nihss : null,
            'gcs_score' => $this->faker->boolean(80) ? $gcs : null,
            'imaging_summary' => $this->faker->boolean(80) ? $this->faker->randomElement($imagingSnippets) : null,
            'lvo_suspected' => $this->faker->boolean(80) ? $lvo : null,
            'treatment_plan' => $this->faker->boolean(70) ? $this->faker->randomElement($plans) : null,
            'orders' => $this->faker->boolean(70) ? $this->faker->randomElement($ordersList) : null,
            'prescription_note' => $this->faker->boolean(70) ? $this->faker->randomElement($rxNotes) : null,
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
            'scheduled_for' => Carbon::now()->addDay(),
            'recorded_at' => Carbon::now()->addDay(),
            'signed_off_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_IN_PROGRESS,
            'scheduled_for' => Carbon::now()->subHours(2),
            'recorded_at' => Carbon::now(),
            'signed_off_at' => null,
        ]);
    }

    public function signedOff(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_SIGNED_OFF,
            'scheduled_for' => Carbon::now()->subHours(2),
            'recorded_at' => Carbon::now()->subHour(),
            'signed_off_at' => Carbon::now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => DoctorNote::STATUS_CANCELLED,
            'scheduled_for' => Carbon::now()->addHours(6),
            'recorded_at' => Carbon::now(),
            'signed_off_at' => Carbon::now(),
        ]);
    }
}
