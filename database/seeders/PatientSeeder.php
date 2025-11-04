<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // พยายามใช้ admin เป็นผู้สร้าง/แก้ไข (ถ้ามี)
        $userId = User::where('requested_role', 'admin')->value('id');

        // สำรอง: ถ้าไม่มี admin (เช่นรัน seeder นี้เดี่ยว ๆ) ให้ใช้ user แรก หรือ null ถ้าไม่มีผู้ใช้เลย
        $userId = $userId ?? User::query()->value('id');

        // จำนวนผู้ป่วย: 10 ราย
        Patient::factory()
            ->count(10)
            ->create([
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
    }
}
