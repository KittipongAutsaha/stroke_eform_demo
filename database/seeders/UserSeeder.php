<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างหมอเดโม่ใหม่ (3 คน)
        $doctors = User::factory()->count(3)->doctor()->create([
            'approved_at'       => now(),
            'email_verified_at' => now(),
        ]);

        // มอบ role 'doctor' ให้หมอทุกคน
        foreach ($doctors as $doctor) {
            $doctor->assignRole('doctor');
        }

        // สร้าง staff ที่อนุมัติแล้ว 1 คน
        $staff = User::factory()->staff()->create([
            'name'              => 'Demo Staff 1',
            'email'             => 'demo_staff1@example.com',
            'approved_at'       => now(),
            'email_verified_at' => now(),
        ]);
        $staff->assignRole('staff');

        // เพิ่ม staff ที่ยังไม่อนุมัติ 1 คน (pending)
        $pendingStaff = User::factory()->staff()->create([
            'name'              => 'Demo Staff 2',
            'email'             => 'demo_staff2@example.com',
            'approved_at'       => null,
            'email_verified_at' => now(),
        ]);
        $pendingStaff->assignRole('staff');
    }
}
