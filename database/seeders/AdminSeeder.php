<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างเฉพาะ admin คนเดียว
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'System Admin',
                'password'          => Hash::make('password'),
                'approved_at'       => now(),
                'email_verified_at' => now(),
                'requested_role'    => 'admin',
            ]
        );

        // มอบ role admin ให้ผู้ใช้
        $admin->assignRole('admin');
    }
}
