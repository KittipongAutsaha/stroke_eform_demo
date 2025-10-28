<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างผู้ป่วยจำลอง 10 ราย
        Patient::factory()
            ->count(10)
            ->create([
                'created_by' => 1, // user id 1 (Admin)
                'updated_by' => 1,
            ]);
    }
}
