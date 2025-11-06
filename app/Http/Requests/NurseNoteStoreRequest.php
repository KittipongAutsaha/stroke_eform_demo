<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ตรวจสอบข้อมูลก่อน "สร้าง" Nurse Note
 * - ใช้กับ: POST /patients/{patient}/nurse-notes
 * - datetime-local ใช้รูปแบบ Y-m-d\TH:i
 * - ห้ามส่ง signed_off_at (ระบบจะตั้งให้เองเมื่อปิดเคส)
 */
class NurseNoteStoreRequest extends FormRequest
{
    private const DATE_FMT = 'Y-m-d\TH:i';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ฟิลด์หลัก (ต้องมีทุกครั้ง)
            'nursing_assessment'   => ['required', 'string'],
            'vital_signs_summary'  => ['required', 'string', 'max:500'],
            'nursing_care_plan'    => ['required', 'string'],

            // ฟิลด์เสริม
            'nursing_diagnosis'        => ['nullable', 'string', 'max:500'],
            'interventions_summary'    => ['nullable', 'string'],
            'progress_note'            => ['nullable', 'string'],
            'education_or_safety_note' => ['nullable', 'string'],
            'sign_note'                => ['nullable', 'string', 'max:500'],

            // ตอน "สร้าง" อนุญาตเฉพาะ planned หรือ in_progress (ปิดเคสไม่ได้ในขั้นสร้าง)
            'status'        => ['required', 'in:planned,in_progress'],

            // วัน-เวลา (จาก input type="datetime-local")
            'recorded_at'   => ['required', 'date_format:' . self::DATE_FMT],
            'scheduled_for' => ['nullable', 'date_format:' . self::DATE_FMT],

            // เวลาลงนาม: ห้ามส่งมา (ระบบจะตั้งเองเมื่อปิดเคส)
            'signed_off_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'nursing_assessment.required'  => __('validation.nurse_notes.nursing_assessment_required'),
            'vital_signs_summary.required' => __('validation.nurse_notes.vital_signs_summary_required'),
            'nursing_care_plan.required'   => __('validation.nurse_notes.nursing_care_plan_required'),
            'status.required'              => __('validation.status_required'),
            'recorded_at.required'         => __('validation.recorded_at_required'),

            'status.in'                 => __('validation.nurse_notes.status_in'),
            'recorded_at.date_format'   => __('validation.nurse_notes.recorded_at_date_format'),
            'scheduled_for.date_format' => __('validation.nurse_notes.scheduled_for_date_format'),

            'signed_off_at.prohibited'  => __('validation.nurse_notes.signed_off_at_prohibited'),
        ];
    }
}
