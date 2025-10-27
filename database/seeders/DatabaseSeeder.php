<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // เรียกใช้ Seeder ที่สร้าง role และ user ตัวอย่าง
        $this->call([
            RolesAndAdminSeeder::class,
        ]);
    }
}
