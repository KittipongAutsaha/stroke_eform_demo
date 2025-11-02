<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // ดึง user id แรก ถ้าไม่มี user ให้เป็น null
        $userId = User::query()->value('id'); // ไม่มี user → null

        // สร้างผู้ป่วยจำลอง 10 ราย
        Patient::factory()
            ->count(10)
            ->create([
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
    }
}
