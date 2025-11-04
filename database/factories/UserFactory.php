<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * ตัวนับแยกตามแต่ละ role
     */
    protected static int $doctorSeq = 0;
    protected static int $nurseSeq = 0;
    protected static int $staffSeq = 0;

    /**
     * ค่าเริ่มต้น (default) — ไม่สุ่มอีเมลหรือ role
     * เพื่อให้แต่ละ role กำหนด state ของตัวเอง
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'requested_role' => null,
        ];
    }

    /**
     * สถานะ: หมอ (Demo Doctor)
     * เช่น demo_doctor1@example.com, demo_doctor2@example.com
     */
    public function doctor(): static
    {
        return $this->state(function () {
            self::$doctorSeq++;
            $n = self::$doctorSeq;

            return [
                'requested_role' => 'doctor',
                'name' => "Demo Doctor {$n}",
                'email' => "demo_doctor{$n}@example.com",
            ];
        });
    }

    /**
     * สถานะ: พยาบาล (Demo Nurse)
     * เช่น demo_nurse1@example.com, demo_nurse2@example.com
     */
    public function nurse(): static
    {
        return $this->state(function () {
            self::$nurseSeq++;
            $n = self::$nurseSeq;

            return [
                'requested_role' => 'nurse',
                'name' => "Demo Nurse {$n}",
                'email' => "demo_nurse{$n}@example.com",
            ];
        });
    }

    /**
     * สถานะ: เจ้าหน้าที่ทั่วไป (Demo Staff)
     * เช่น demo_staff1@example.com, demo_staff2@example.com
     */
    public function staff(): static
    {
        return $this->state(function () {
            self::$staffSeq++;
            $n = self::$staffSeq;

            return [
                'requested_role' => 'staff',
                'name' => "Demo Staff {$n}",
                'email' => "demo_staff{$n}@example.com",
            ];
        });
    }

    /**
     * สถานะ: ยังไม่ยืนยันอีเมล
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
