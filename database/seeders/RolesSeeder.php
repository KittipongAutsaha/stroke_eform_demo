<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // เคลียร์ cache ของ spatie ก่อน กัน role/permission ค้าง
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // สร้าง role พื้นฐานที่ระบบใช้
        foreach (['admin', 'doctor', 'nurse', 'staff'] as $name) {
            Role::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        // เคลียร์ cache อีกครั้งหลังสร้างเสร็จ
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
