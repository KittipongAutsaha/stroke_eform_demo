<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * รัน Seeder ทั้งหมดตามลำดับที่มีการพึ่งพากัน
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,       // สร้าง role พื้นฐานของระบบ
            AdminSeeder::class,       // เพิ่มผู้ดูแลระบบ
            UserSeeder::class,        // เพิ่มหมอและ staff ตัวอย่าง
            PatientSeeder::class,     // เพิ่มข้อมูลผู้ป่วยจำลอง
            DoctorNoteSeeder::class,  // เพิ่มบันทึกแพทย์เชื่อมกับผู้ป่วย
        ]);
    }
}
