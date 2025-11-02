<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientStoreRequest extends FormRequest
{
    /**
     * ตรวจสอบสิทธิ์ผู้ใช้ที่สามารถบันทึกข้อมูลผู้ป่วยใหม่ได้
     */
    public function authorize(): bool
    {
        // อนุญาตเฉพาะ admin / doctor / nurse เท่านั้น
        // ป้องกันไม่ให้ staff หรือผู้ใช้ทั่วไปสร้างข้อมูลผู้ป่วย
        $user = $this->user();
        return $user && $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /**
     * ปรับข้อมูลก่อนทำการ validate
     */
    protected function prepareForValidation(): void
    {
        // แปลง HN ให้เป็นตัวพิมพ์ใหญ่ และตัดช่องว่าง
        // ลบอักขระที่ไม่ใช่ตัวเลขออกจาก CID
        $this->merge([
            'hn'  => isset($this->hn) ? strtoupper(trim($this->hn)) : null,
            'cid' => isset($this->cid) ? preg_replace('/\D/', '', $this->cid) : null,
        ]);
    }

    /**
     * กำหนดกฎการตรวจสอบข้อมูล (Validation Rules)
     */
    public function rules(): array
    {
        return [
            // --- กลุ่มข้อมูลระบุตัวตน ---
            'hn' => [
                'bail',
                'required',
                'string',
                'regex:/^HN-\d{7}$/',   // รูปแบบ HN ต้องเป็น HN-ตัวเลข 7 หลัก
                'unique:patients,hn',   // ห้ามซ้ำในตาราง patients
            ],
            'cid' => [
                'bail',
                'nullable',
                'numeric',
                'min_digits:13',
                'max_digits:13',
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'dob'        => ['bail', 'required', 'date', 'date_format:Y-m-d', 'before:today'],
            'sex'        => ['bail', 'required', 'in:male,female,other,unknown'],

            // --- กลุ่มข้อมูลติดต่อ ---
            'phone'          => ['nullable', 'regex:/^0\d{8,9}$/'],
            'address_short'  => ['nullable', 'string', 'max:255'],
            'address_full'   => ['nullable', 'string'],
            'postal_code'    => ['nullable', 'digits_between:4,10'],

            // --- กลุ่มข้อมูลเพิ่มเติม ---
            'blood_group'    => ['nullable', 'in:A,B,AB,O'],
            'rh_factor'      => ['nullable', 'in:+,-'],
            'insurance_scheme' => ['nullable', 'string'],
            'insurance_no'     => ['nullable', 'string'],
            'consent_at'       => ['nullable', 'date'],
            'consent_note'     => ['nullable', 'string'],
            'note_general'     => ['nullable', 'string', 'max:500'],

            // --- กลุ่มผู้ติดต่อฉุกเฉิน ---
            'emergency_contact_name'      => ['nullable', 'string'],
            'emergency_contact_relation'  => ['nullable', 'string'],
            'emergency_contact_phone'     => ['nullable', 'regex:/^0\d{8,9}$/'],
        ];
    }

    /**
     * กำหนดข้อความแจ้งเตือนเมื่อ validation ไม่ผ่าน
     */
    public function messages(): array
    {
        return [
            // HN
            'hn.required' => __('validation.hn_required'),
            'hn.string'   => __('validation.hn_string'),
            'hn.regex'    => __('validation.hn_regex'),
            'hn.unique'   => __('validation.hn_unique'),

            // CID
            'cid.numeric'     => __('validation.cid_numeric'),
            'cid.min_digits'  => __('validation.cid_min_digits'),
            'cid.max_digits'  => __('validation.cid_max_digits'),

            // ชื่อ-นามสกุล
            'first_name.required' => __('validation.first_name_required'),
            'first_name.string'   => __('validation.first_name_string'),
            'first_name.max'      => __('validation.first_name_max'),
            'last_name.required'  => __('validation.last_name_required'),
            'last_name.string'    => __('validation.last_name_string'),
            'last_name.max'       => __('validation.last_name_max'),

            // วันเกิด
            'dob.required'    => __('validation.dob_required'),
            'dob.date'        => __('validation.dob_date'),
            'dob.date_format' => __('validation.dob_date_format'),
            'dob.before'      => __('validation.dob_before'),

            // เพศ
            'sex.required' => __('validation.sex_required'),
            'sex.in'       => __('validation.sex_in'),

            // โทรศัพท์
            'phone.regex' => __('validation.phone_regex'),

            // รหัสไปรษณีย์
            'postal_code.digits_between' => __('validation.postal_code_digits_between'),

            // หมู่เลือด / Rh
            'blood_group.in' => __('validation.blood_group_in'),
            'rh_factor.in'   => __('validation.rh_factor_in'),

            // ที่อยู่
            'address_short.string' => __('validation.address_short_string'),
            'address_short.max'    => __('validation.address_short_max'),
            'address_full.string'  => __('validation.address_full_string'),

            // หมายเหตุ
            'note_general.string' => __('validation.note_general_string'),
            'note_general.max'    => __('validation.note_general_max'),

            // สิทธิ์ประกัน
            'insurance_scheme.string' => __('validation.insurance_scheme_string'),
            'insurance_no.string'     => __('validation.insurance_no_string'),

            // ความยินยอม
            'consent_at.date'   => __('validation.consent_at_date'),
            'consent_note.string' => __('validation.consent_note_string'),

            // ผู้ติดต่อฉุกเฉิน
            'emergency_contact_name.string'     => __('validation.emergency_contact_name_string'),
            'emergency_contact_relation.string' => __('validation.emergency_contact_relation_string'),
            'emergency_contact_phone.regex'     => __('validation.emergency_contact_phone_regex'),
        ];
    }
}
