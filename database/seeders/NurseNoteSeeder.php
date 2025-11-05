<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\NurseNote;

class NurseNoteSeeder extends Seeder
{
    public function run(): void
    {
        // ดึงรายชื่อ "พยาบาล" และ "ผู้ป่วย" ทั้งหมด
        $nurseIds = User::where('requested_role', 'nurse')->pluck('id')->all();
        $patients = Patient::all('id');

        // ถ้าไม่มีพยาบาลหรือผู้ป่วย ให้ข้าม (กัน error เวลา seed แยก)
        if (empty($nurseIds) || $patients->isEmpty()) {
            return;
        }

        foreach ($patients as $patient) {
            // จำนวนโน้ตต่อผู้ป่วย (2–4 รายการ)
            $count = fake()->numberBetween(2, 4);

            // 1) planned — อย่างน้อย 1 รายการ
            NurseNote::factory()
                ->planned()
                ->for($patient)
                ->state([
                    'nurse_id' => $nurseIds[array_rand($nurseIds)],
                ])
                ->create();

            // 2) signed_off — อย่างน้อย 1 รายการ
            NurseNote::factory()
                ->signedOff()
                ->for($patient)
                ->state([
                    'nurse_id' => $nurseIds[array_rand($nurseIds)],
                ])
                ->create();

            // 3) ที่เหลือ (0–2 รายการ) — สุ่มสถานะ inProgress / cancelled
            $remaining = $count - 2;
            for ($i = 0; $i < $remaining; $i++) {
                $state = fake()->randomElement(['inProgress', 'cancelled']);

                NurseNote::factory()
                    ->{$state}()
                    ->for($patient)
                    ->state([
                        'nurse_id' => $nurseIds[array_rand($nurseIds)],
                    ])
                    ->create();
            }
        }
    }
}
