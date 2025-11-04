<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\DoctorNote;

class DoctorNoteSeeder extends Seeder
{
    public function run(): void
    {
        // ดึงรายชื่อหมอและผู้ป่วยทั้งหมด
        $doctorIds = User::where('requested_role', 'doctor')->pluck('id')->all();
        $patients  = Patient::all('id');

        // ถ้าไม่มีหมอหรือผู้ป่วย ให้ข้าม (ป้องกัน error เวลา seed แยก)
        if (empty($doctorIds) || $patients->isEmpty()) {
            return;
        }

        foreach ($patients as $patient) {
            // จำนวนโน้ตต่อผู้ป่วย (2–4 รายการ)
            $count = fake()->numberBetween(2, 4);

            // 1️ planned — อย่างน้อย 1 รายการ
            DoctorNote::factory()
                ->planned()
                ->for($patient)
                ->state([
                    'doctor_id' => $doctorIds[array_rand($doctorIds)],
                ])
                ->create();

            // 2️ signed_off — อย่างน้อย 1 รายการ
            DoctorNote::factory()
                ->signedOff()
                ->for($patient)
                ->state([
                    'doctor_id' => $doctorIds[array_rand($doctorIds)],
                ])
                ->create();

            // 3️ ที่เหลือ (0–2 รายการ) — สุ่มสถานะ inProgress / cancelled
            $remaining = $count - 2;
            for ($i = 0; $i < $remaining; $i++) {
                $state = fake()->randomElement(['inProgress', 'cancelled']);

                DoctorNote::factory()
                    ->{$state}()
                    ->for($patient)
                    ->state([
                        'doctor_id' => $doctorIds[array_rand($doctorIds)],
                    ])
                    ->create();
            }
        }
    }
}
