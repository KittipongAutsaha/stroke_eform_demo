<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1) สร้าง roles (กันซ้ำด้วย firstOrCreate)
        $roles = collect(['admin', 'doctor', 'nurse', 'staff'])->map(function ($name) {
            return Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        });

        // 2) สร้างผู้ใช้ตัวอย่าง
        // Admin — อนุมัติแล้ว
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'        => 'System Admin',
                'password'    => Hash::make('password'), // เปลี่ยนทีหลังในโปรดักชัน
                'approved_at' => now(),
                'email_verified_at' => now(), // demo ให้เข้าได้เลย
                'requested_role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        // Doctor
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@example.com'],
            [
                'name'        => 'Demo Doctor',
                'password'    => Hash::make('password'),
                'approved_at' => now(),
                'email_verified_at' => now(),
                'requested_role' => 'doctor',
            ]
        );
        $doctor->assignRole('doctor');

        // Nurse
        $nurse = User::firstOrCreate(
            ['email' => 'nurse@example.com'],
            [
                'name'        => 'Demo Nurse',
                'password'    => Hash::make('password'),
                'approved_at' => now(),
                'email_verified_at' => now(),
                'requested_role' => 'nurse',
            ]
        );
        $nurse->assignRole('nurse');

        // Staff (ยังไม่อนุมัติ เพื่อทดสอบหน้า pending)
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name'        => 'Demo Staff',
                'password'    => Hash::make('password'),
                'approved_at' => null,         // ยังไม่อนุมัติ
                'email_verified_at' => now(),
                'requested_role' => 'staff',
            ]
        );
        $staff->assignRole('staff');
    }
}
