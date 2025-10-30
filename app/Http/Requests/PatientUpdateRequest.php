<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // อนุญาตเฉพาะ admin / doctor / nurse (กัน staff)
        $user = $this->user();
        return $user && $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    protected function prepareForValidation(): void
    {
        // ปรับรูปแบบก่อนตรวจสอบ: hn เป็นตัวใหญ่, cid เก็บเฉพาะตัวเลข
        $this->merge([
            'hn'  => isset($this->hn) ? strtoupper(trim($this->hn)) : null,
            'cid' => isset($this->cid) ? preg_replace('/\D/', '', $this->cid) : null,
        ]);
    }

    public function rules(): array
    {
        // ใช้ route model binding ของ resource: /patients/{patient}
        $patient = $this->route('patient');
        $isAdmin = $this->user()?->hasRole('admin');

        return [
            // HN:
            // - ถ้าเป็น Admin: อนุญาตให้แก้ แต่ต้องตรงรูปแบบ และไม่ซ้ำ (ignore เรคคอร์ดเดิม)
            // - ถ้าไม่ใช่ Admin: ห้ามส่งฟิลด์นี้มา (prohibited)
            'hn' => $isAdmin
                ? [
                    'bail',
                    'required',
                    'string',
                    'regex:/^HN-\d{7}$/',
                    Rule::unique('patients', 'hn')->ignore($patient?->id),
                ]
                : ['prohibited'],

            // CID: ตัวเลข 13 หลักพอดี (ไม่ตรวจ checksum)
            'cid'           => ['bail', 'nullable', 'numeric', 'min_digits:13', 'max_digits:13'],

            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],

            // วันเกิด ต้องเป็นวันที่ก่อนวันนี้
            'dob'           => ['bail', 'required', 'date', 'date_format:Y-m-d', 'before:today'],

            // เพศ: จำกัดค่า
            'sex'           => ['bail', 'required', Rule::in(['male', 'female', 'other', 'unknown'])],

            'address_short' => ['nullable', 'string', 'max:255'],
            'note_general'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            // HN
            'hn.prohibited' => __('validation.hn_prohibited'),
            'hn.required'   => __('validation.hn_required'),
            'hn.string'     => __('validation.hn_string'),
            'hn.regex'      => __('validation.hn_regex'),
            'hn.unique'     => __('validation.hn_unique'),

            // CID
            'cid.numeric'      => __('validation.cid_numeric'),
            'cid.min_digits'   => __('validation.cid_min_digits'),
            'cid.max_digits'   => __('validation.cid_max_digits'),

            // ชื่อ-นามสกุล
            'first_name.required' => __('validation.first_name_required'),
            'first_name.string'   => __('validation.first_name_string'),
            'first_name.max'      => __('validation.first_name_max'),

            'last_name.required'  => __('validation.last_name_required'),
            'last_name.string'    => __('validation.last_name_string'),
            'last_name.max'       => __('validation.last_name_max'),

            // วันเกิด
            'dob.required'     => __('validation.dob_required'),
            'dob.date'         => __('validation.dob_date'),
            'dob.date_format'  => __('validation.dob_date_format'),
            'dob.before'       => __('validation.dob_before'),

            // เพศ
            'sex.required' => __('validation.sex_required'),
            'sex.in'       => __('validation.sex_in'),

            // ที่อยู่
            'address_short.string' => __('validation.address_short_string'),
            'address_short.max'    => __('validation.address_short_max'),

            // หมายเหตุ
            'note_general.string'  => __('validation.note_general_string'),
            'note_general.max'     => __('validation.note_general_max'),
        ];
    }
}
