<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $f = $this->faker;

        // ------------------------------------------------
        // โครงสร้างภูมิศาสตร์ไทย + รหัสไปรษณีย์ที่ถูกต้อง
        // province => [ district => ['tambons'=>[], 'postal'=>'xxxxx'], ... ]
        // ------------------------------------------------
        $geo = [
            'เชียงใหม่' => [
                'เมืองเชียงใหม่' => ['tambons' => ['สุเทพ', 'ช้างเผือก', 'ศรีภูมิ', 'แม่เหียะ'], 'postal' => '50000'],
                'แม่ริม'       => ['tambons' => ['ริมใต้', 'ริมเหนือ', 'สันโป่ง'], 'postal' => '50180'],
                'สันทราย'      => ['tambons' => ['สันทรายน้อย', 'สันทรายหลวง', 'แม่แฝก'], 'postal' => '50210'],
            ],
            'ขอนแก่น' => [
                'เมืองขอนแก่น' => ['tambons' => ['ในเมือง', 'บ้านเป็ด', 'บ้านค้อ'], 'postal' => '40000'],
                'บ้านฝาง'      => ['tambons' => ['บ้านฝาง', 'หนองบัว', 'ป่ามะนาว'], 'postal' => '40270'],
            ],
            'บุรีรัมย์' => [
                'นางรอง' => ['tambons' => ['นางรอง', 'สะเดา', 'ลำไทรโยง'], 'postal' => '31110'],
            ],
            'สงขลา' => [
                'หาดใหญ่' => ['tambons' => ['ควนลัง', 'คอหงส์', 'บ้านพรุ'], 'postal' => '90110'],
            ],
            'ภูเก็ต' => [
                'เมืองภูเก็ต' => ['tambons' => ['ตลาดใหญ่', 'ตลาดเหนือ', 'รัษฎา'], 'postal' => '83000'],
            ],
            'ชลบุรี' => [
                'บางละมุง' => ['tambons' => ['หนองปรือ', 'นาเกลือ', 'ตะเคียนเตี้ย'], 'postal' => '20150'],
            ],
            'นครราชสีมา' => [
                'เมืองนครราชสีมา' => ['tambons' => ['ในเมือง', 'หนองจะบก', 'หนองไผ่ล้อม'], 'postal' => '30000'],
            ],
            'พิษณุโลก' => [
                'เมืองพิษณุโลก' => ['tambons' => ['ในเมือง', 'บ้านคลอง', 'ท่าทอง'], 'postal' => '65000'],
            ],
        ];

        // เลือกลำดับชั้น → จังหวัด → อำเภอ → ตำบล + ไปรษณีย์
        $province = $f->randomElement(array_keys($geo));
        $district = $f->randomElement(array_keys($geo[$province]));
        $tambon   = $f->randomElement($geo[$province][$district]['tambons']);
        $postal   = $geo[$province][$district]['postal'];

        // address_short / address_full
        $houseNo = $f->numberBetween(1, 299);
        $moo     = $f->numberBetween(1, 12);
        $addressShort = "อำเภอ{$district} จังหวัด{$province}";
        $addressFull  = "บ้านเลขที่ {$houseNo} หมู่ {$moo} ตำบล{$tambon} อำเภอ{$district} จังหวัด{$province}";

        // ------------------------------------------------
        // ชื่อไทย + กติกาเพศตามชื่อ (ชายห้าม female, หญิงห้าม male)
        // ------------------------------------------------
        $thaiMaleFirst   = ['กิตติ', 'ณัฐพล', 'ปณชัย', 'ธีรดนย์', 'ภูริภัทร', 'ศุภกฤต', 'ธนกฤต', 'ชยุต', 'ปวริศ', 'วชิระ'];
        $thaiFemaleFirst = ['ศิริพร', 'ชนกานต์', 'วราภรณ์', 'ชลธิชา', 'อรณิชา', 'กมลชนก', 'พิชญ์สินี', 'อริสา', 'จิรัชญา', 'วโรชา'];
        $thaiLast        = ['สุวรรณภูมิ', 'จิตตรง', 'พงศ์พิพัฒน์', 'นภากาศ', 'จันทร์เพ็ญ', 'อมรเกียรติ', 'สีทะวงษ์', 'รัตนสกาว', 'ศรีรัตน์', 'ชลธี'];

        if ($f->boolean()) {
            // เลือกชื่อ "ชาย" → เพศที่อนุญาต: male | other | unknown
            $patientFirst = $f->randomElement($thaiMaleFirst);
            $patientSex   = $f->randomElement(['male', 'other', 'unknown']);
        } else {
            // เลือกชื่อ "หญิง" → เพศที่อนุญาต: female | other | unknown
            $patientFirst = $f->randomElement($thaiFemaleFirst);
            $patientSex   = $f->randomElement(['female', 'other', 'unknown']);
        }
        $patientLast = $f->randomElement($thaiLast);

        // ------------------------------------------------
        // ความสัมพันธ์ฉุกเฉิน + ตรรกะ "คู่สมรส"
        // ------------------------------------------------
        $relationFemaleOnly = ['มารดา', 'ภรรยา', 'บุตรสาว', 'พี่สาว', 'น้องสาว'];
        $relationMaleOnly   = ['บิดา', 'สามี', 'บุตรชาย', 'พี่ชาย', 'น้องชาย'];
        $relationNeutral    = ['คู่สมรส', 'ญาติ', 'เพื่อนร่วมงาน'];

        $relationPool = array_merge($relationFemaleOnly, $relationMaleOnly, $relationNeutral);
        $relation = $f->randomElement($relationPool);

        if ($relation === 'คู่สมรส') {
            if ($patientSex === 'male') {
                $ecFirst = $f->randomElement($thaiFemaleFirst);
            } elseif ($patientSex === 'female') {
                $ecFirst = $f->randomElement($thaiMaleFirst);
            } else {
                $ecFirst = $f->randomElement($f->boolean() ? $thaiMaleFirst : $thaiFemaleFirst);
            }
            $ecLast  = $patientLast; // ใช้นามสกุลเดียวกันเพื่อความสมจริง
        } elseif (in_array($relation, $relationFemaleOnly, true)) {
            $ecFirst = $f->randomElement($thaiFemaleFirst);
            $ecLast  = $f->randomElement($thaiLast);
        } elseif (in_array($relation, $relationMaleOnly, true)) {
            $ecFirst = $f->randomElement($thaiMaleFirst);
            $ecLast  = $f->randomElement($thaiLast);
        } else {
            $ecFirst = $f->randomElement($f->boolean() ? $thaiMaleFirst : $thaiFemaleFirst);
            $ecLast  = $f->randomElement($thaiLast);
        }
        $ecName = $ecFirst . ' ' . $ecLast;

        // ------------------------------------------------
        // ฟิลด์อื่น ๆ ตาม C10 (ไทยล้วน + realistic)
        // ------------------------------------------------

        // HN ต้องเป็น HN-ตัวเลข 7 หลัก (ตรงกับ validation)
        $hn  = 'HN-' . $f->unique()->numerify('#######');

        // CID 13 หลัก (อาจว่าง)
        $cid = $f->optional()->numerify('#############');

        // เบอร์โทรไทย (เลือกใช้รูปแบบขึ้นต้น 0 เพื่อให้สอดคล้อง regex ที่มักใช้)
        $thaiPhone = '0' . $f->numerify('#########');

        // วันเกิด (ย้อนหลัง 1–80 ปี; อาจว่าง)
        $dobDT = $f->optional()->dateTimeBetween('-80 years', '-1 years');
        $dob   = $dobDT ? $dobDT->format('Y-m-d') : null;

        // หมู่เลือด / Rh:
        // - อนุญาตให้ "มีหมู่เลือดแต่ไม่มี Rh" ได้
        // - ไม่อนุญาตให้ "มี Rh โดยไม่มีหมู่เลือด" → ถ้า blood_group เป็น null ให้ rh_factor เป็น null เสมอ
        $bloodGroup = $f->optional()->randomElement(['A', 'B', 'AB', 'O']);
        $rhFactor   = $bloodGroup ? $f->optional()->randomElement(['+', '-']) : null;

        return [
            // ระบุตัวตนพื้นฐาน
            'hn'         => $hn,
            'cid'        => $cid,
            'first_name' => $patientFirst,
            'last_name'  => $patientLast,
            'dob'        => $dob,
            'sex'        => $patientSex,

            // ชีวข้อมูล
            'blood_group' => $bloodGroup,
            'rh_factor'   => $rhFactor,

            // การติดต่อ / ที่อยู่ (ไทยทั้งหมด และไปรษณีย์ถูกต้อง)
            'phone'         => $f->optional()->randomElement([$thaiPhone, null]),
            'address_short' => $addressShort,
            'address_full'  => $f->optional()->randomElement([$addressFull, null]),
            'postal_code'   => $f->optional()->randomElement([$postal, null]),

            // ผู้ติดต่อฉุกเฉิน
            'emergency_contact_name'     => $f->optional()->randomElement([$ecName, null]),
            'emergency_contact_relation' => $f->optional()->randomElement([$relation, null]),
            'emergency_contact_phone'    => $f->optional()->randomElement([$thaiPhone, null]),

            // สัญชาติ / ภาษา
            'nationality'        => $f->optional()->randomElement(['ไทย']),
            'preferred_language' => $f->optional()->randomElement(['th-TH']),

            // สิทธิ์ประกัน (ไทย)
            'insurance_scheme' => $f->optional()->randomElement(['บัตรทอง (UCS)', 'ประกันสังคม (SSS)', 'ข้าราชการ (CSMBS)', 'เอกชน']),
            'insurance_no'     => $f->optional()->bothify('TH-INS########'),

            // ความยินยอม / บันทึกทั่วไป
            'consent_at'   => $f->optional()->dateTimeBetween('-2 years', 'now'),
            'consent_note' => $f->optional()->randomElement(['ยินยอมการใช้ข้อมูลเพื่อการรักษา', 'ยินยอมเฉพาะข้อมูลทางการแพทย์']),
            'note_general' => $f->optional()->randomElement([
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
            ]),

            // ระบบ (demo)
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
