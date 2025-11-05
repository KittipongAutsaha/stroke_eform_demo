<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\NurseNote;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Carbon;

class NurseNoteFactory extends Factory
{
    protected $model = NurseNote::class;

    public function definition(): array
    {
        // ข้อมูลตัวอย่างแบบย่อ ให้ใกล้เคียงระดับความละเอียดของหมอ
        $assessments = [
            'ผู้ป่วยรู้สึกตัวดี พูดช้าเล็กน้อย กลืนลำบากเล็กน้อย แขนขวาอ่อนแรงระดับเล็กน้อย',
            'สติสัมปชัญญะดี GCS 15 เดินไม่มั่นคง เสี่ยงล้ม',
            'สื่อสารได้ดี Vital stable มีไอสำลักเวลาดื่มน้ำ',
            'นอนพักบนเตียง ยกขาซ้ายได้ไม่สุด ปวดศีรษะเล็กน้อย',
        ];
        $vitals = [
            'BP 130/80, PR 78, RR 18, Temp 36.8, SpO2 98%',
            'BP 150/90, PR 84, RR 20, Temp 37.0, SpO2 97%',
            'BP 120/75, PR 72, RR 16, Temp 36.6, SpO2 99%',
            'BP 140/85, PR 80, RR 18, Temp 36.7, SpO2 98%',
        ];
        $nursingDx = [
            'เสี่ยงต่อการหกล้มจากกล้ามเนื้ออ่อนแรง',
            'ไม่ปลอดภัยต่อการสำลักจากกลไกการกลืนบกพร่อง',
            'บกพร่องในการเคลื่อนไหวจากอ่อนแรงครึ่งซีก',
            'ปวดระดับเล็กน้อยจากภาวะกล้ามเนื้อเกร็ง',
        ];
        $plans = [
            'จัดท่าศีรษะสูง 30 องศา เฝ้าระวังการสำลัก สอนญาติการป้อนอาหาร',
            'ประเมินสัญญาณชีพทุก 4 ชม. ฝึกกลืนกับ SIP test ติดตามผล',
            'ป้องกันหกล้ม ติดตั้ง bed rail แนะนำใช้ call bell ก่อนลุก',
            'กายภาพบำบัดเบื้องต้น ฝึกพลิกตะแคงทุก 2 ชม. ป้องกันแผลกดทับ',
        ];
        $interventions = [
            'ช่วยทำความสะอาดช่องปาก จัดท่านั่งก่อนรับประทานอาหาร 30 นาที',
            'ประเมิน pain score ทุก 4 ชม. ให้การพยาบาลลดปวดตามแผน',
            'สอนการหายใจลึก/ไออย่างมีประสิทธิภาพ เฝ้าระวังเสมหะเหนียว',
            'ประคับประคองจิตใจ ให้ข้อมูลผู้ป่วยและญาติ ลดความกังวล',
        ];
        $progresses = [
            'ผู้ป่วยตอบสนองดี ไม่มีไอสำลักหลังปรับท่าทาง สัญญาณชีพคงที่',
            'เดินในห้องด้วย walker ได้ 10 เมตร โดยมีผู้ช่วยพยุง ไม่มีอุบัติเหตุ',
            'รับประทานอ่อนนิ่มได้ดี ไม่มีอาเจียน รายงานแพทย์ผลการดูแล',
            'ปวดลดลงจาก 5 เหลือ 2 หลังให้การพยาบาลและประคบอุ่น',
        ];
        $eduOrSafety = [
            'แนะนำการใช้กริ่งเรียกก่อนลุกจากเตียง ญาติรับทราบ',
            'สอนวิธีป้อนอาหารอย่างปลอดภัย หลีกเลี่ยงของเหลวใส',
            'ให้ความรู้การป้องกันแผลกดทับ เปลี่ยนท่าทุก 2 ชม.',
            'อธิบายการเฝ้าระวังอาการเตือน stroke ซ้ำ ญาติสามารถทวนได้',
        ];

        // สุ่มสถานะตาม flow เดียวกับหมอ (อนุญาต planned→cancelled / in_progress→signed_off)
        $status = $this->faker->randomElement([
            NurseNote::STATUS_PLANNED,
            NurseNote::STATUS_IN_PROGRESS,
            NurseNote::STATUS_SIGNED_OFF,
            NurseNote::STATUS_CANCELLED,
        ]);

        // ตั้งเวลาตามสถานะ (อ้างอิงโครงจาก DoctorNoteFactory)
        $scheduledFor = null;
        $recordedAt   = null;
        $signedOffAt  = null;

        if ($status === NurseNote::STATUS_PLANNED) {
            $scheduledFor = $this->faker->dateTimeBetween('+1 hour', '+3 days');
            $recordedAt   = Carbon::instance($scheduledFor);
        } elseif ($status === NurseNote::STATUS_IN_PROGRESS) {
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', 'now');
            $recordedAt   = $this->faker->dateTimeBetween($scheduledFor, 'now');
        } elseif ($status === NurseNote::STATUS_SIGNED_OFF) {
            $scheduledFor = $this->faker->dateTimeBetween('-3 days', '-2 hours');
            $recordedAt   = $this->faker->dateTimeBetween($scheduledFor, '-1 hour');
            $signedOffAt  = $this->faker->dateTimeBetween($recordedAt, 'now');
        } else { // CANCELLED
            $scheduledFor = $this->faker->dateTimeBetween('-2 days', '+2 days');
            $recordedAt   = Carbon::now();
            $signedOffAt  = Carbon::now();
        }

        // ผูกความสัมพันธ์
        $patientId = Patient::inRandomOrder()->value('id') ?? Patient::factory();
        $nurseId   = User::where('requested_role', 'nurse')->inRandomOrder()->value('id')
            ?? User::factory()->state(['requested_role' => 'nurse']);

        return [
            'patient_id'               => $patientId,
            'nurse_id'                 => $nurseId,
            'status'                   => $status,
            'scheduled_for'            => $scheduledFor,
            'recorded_at'              => $recordedAt,
            'signed_off_at'            => $signedOffAt,
            'nursing_assessment'       => $this->faker->randomElement($assessments),
            'vital_signs_summary'      => $this->faker->randomElement($vitals),
            'nursing_diagnosis'        => $this->faker->boolean(70) ? $this->faker->randomElement($nursingDx) : null,
            'nursing_care_plan'        => $this->faker->randomElement($plans),
            'interventions_summary'    => $this->faker->boolean(70) ? $this->faker->randomElement($interventions) : null,
            'progress_note'            => $this->faker->randomElement($progresses),
            'education_or_safety_note' => $this->faker->boolean(60) ? $this->faker->randomElement($eduOrSafety) : null,
            'sign_note'                => $this->faker->boolean(50) ? ('RN ' . $this->faker->firstName()) : null,
            'created_by_ip'            => $this->faker->ipv4(),
            'updated_by_ip'            => $this->faker->ipv4(),
            'created_at'               => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at'               => now(),
        ];
    }

    // ---------- States (อ้างอิงรูปแบบจาก DoctorNoteFactory) ----------

    public function planned(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'        => NurseNote::STATUS_PLANNED,
            'scheduled_for' => Carbon::now()->addDay(),
            'recorded_at'   => Carbon::now()->addDay(),
            'signed_off_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'        => NurseNote::STATUS_IN_PROGRESS,
            'scheduled_for' => Carbon::now()->subHours(2),
            'recorded_at'   => Carbon::now(),
            'signed_off_at' => null,
        ]);
    }

    public function signedOff(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'        => NurseNote::STATUS_SIGNED_OFF,
            'scheduled_for' => Carbon::now()->subHours(2),
            'recorded_at'   => Carbon::now()->subHour(),
            'signed_off_at' => Carbon::now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'        => NurseNote::STATUS_CANCELLED,
            'scheduled_for' => Carbon::now()->addHours(6),
            'recorded_at'   => Carbon::now(),
            'signed_off_at' => Carbon::now(),
        ]);
    }
}
