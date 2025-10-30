<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        // คู่ข้อมูลอำเภอ - จังหวัด (ภาษาไทย)
        $locations = [
            'อำเภอเมืองเชียงใหม่ จังหวัดเชียงใหม่',
            'อำเภอแม่ริม จังหวัดเชียงใหม่',
            'อำเภอสันทราย จังหวัดเชียงใหม่',
            'อำเภอเมืองขอนแก่น จังหวัดขอนแก่น',
            'อำเภอนางรอง จังหวัดบุรีรัมย์',
            'อำเภอหาดใหญ่ จังหวัดสงขลา',
            'อำเภอเมืองภูเก็ต จังหวัดภูเก็ต',
            'อำเภอบางละมุง จังหวัดชลบุรี',
            'อำเภอเมืองนครราชสีมา จังหวัดนครราชสีมา',
            'อำเภอเมืองพิษณุโลก จังหวัดพิษณุโลก',
        ];

        // ตัวอย่างหมายเหตุทั่วไป (เขียนโดยแพทย์/พยาบาล)
        $notes = [
            'แพ้อาหารทะเล',
            'แพ้ยาพาราเซตามอล',
            'เคยผ่าตัดไส้ติ่งเมื่อปี 2562',
            'มีประวัติความดันโลหิตสูง',
            'อยู่ระหว่างการติดตามผลเลือด',
            'แพ้น้ำยาทำความสะอาดบางชนิด',
            'ต้องระวังการให้ยาเพนิซิลลิน',
            'ไม่มีโรคประจำตัว',
            'มีอาการนอนไม่หลับเป็นบางครั้ง',
            'แนะนำให้มาตรวจซ้ำในอีก 3 เดือน',
        ];

        $dobDateTime = fake()->optional()->dateTimeBetween('-80 years', '-1 years');
        $dob = $dobDateTime ? $dobDateTime->format('Y-m-d') : null;

        return [
            // HN: รูปแบบ HN-1234567
            'hn'            => strtoupper('HN-' . fake()->unique()->numerify('#######')),

            // CID: 13 หลัก (อาจว่างได้)
            'cid'           => fake()->optional()->numerify('#############'),

            // ชื่อ-นามสกุล
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),

            // วันเกิด (ย้อนหลัง 1 - 80 ปี; อาจว่างได้)
            'dob'           => $dob,

            // เพศ
            'sex'           => fake()->randomElement(['male', 'female', 'other', 'unknown']),

            // ที่อยู่ย่อ (สุ่มจากอำเภอ+จังหวัด)
            'address_short' => fake()->randomElement($locations),

            // หมายเหตุทั่วไป (สุ่มจากข้อความแพทย์/พยาบาล)
            'note_general'  => fake()->randomElement($notes),

            // ผู้สร้างและแก้ไขข้อมูล (default = user id 1)
            'created_by'    => 1,
            'updated_by'    => 1,
        ];
    }
}
