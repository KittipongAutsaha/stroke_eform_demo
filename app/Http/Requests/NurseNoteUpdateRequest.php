<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ตรวจสอบข้อมูลก่อน "อัปเดต" Nurse Note
 * - ใช้กับ: PUT/PATCH /patients/{patient}/nurse-notes/{nurseNote}
 * - ส่งเฉพาะฟิลด์ที่จะแก้ไข (ใช้ sometimes)
 * - datetime-local ใช้รูปแบบ Y-m-d\TH:i
 */
class NurseNoteUpdateRequest extends FormRequest
{
    private const DATE_FMT = 'Y-m-d\TH:i';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ฟิลด์หลัก (ถ้าส่งมา ต้องไม่ว่าง)
            'nursing_assessment'   => ['sometimes', 'required', 'string'],
            'vital_signs_summary'  => ['sometimes', 'required', 'string', 'max:500'],
            'nursing_care_plan'    => ['sometimes', 'required', 'string'],

            // ฟิลด์เสริม
            'nursing_diagnosis'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'interventions_summary'    => ['sometimes', 'nullable', 'string'],
            'progress_note'            => ['sometimes', 'nullable', 'string'],
            'education_or_safety_note' => ['sometimes', 'nullable', 'string'],
            'sign_note'                => ['sometimes', 'nullable', 'string', 'max:500'],

            // สถานะ (controller จะตรวจ flow อีกชั้น)
            'status'        => ['sometimes', 'required', 'in:planned,in_progress,signed_off,cancelled'],

            // วัน-เวลา
            'recorded_at'   => ['sometimes', 'required', 'date_format:' . self::DATE_FMT],
            'scheduled_for' => ['sometimes', 'nullable', 'date_format:' . self::DATE_FMT],

            // เวลาลงนาม: ไม่ให้ผู้ใช้ส่งมาเอง
            'signed_off_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'nursing_assessment.required'   => __('validation.nurse_notes.nursing_assessment_required'),
            'vital_signs_summary.required'  => __('validation.nurse_notes.vital_signs_summary_required'),
            'nursing_care_plan.required'    => __('validation.nurse_notes.nursing_care_plan_required'),
            'status.required'               => __('validation.status_required'),
            'recorded_at.required'          => __('validation.recorded_at_required'),

            'status.in'                     => __('validation.nurse_notes.status_in'),
            'recorded_at.date_format'       => __('validation.nurse_notes.recorded_at_date_format'),
            'scheduled_for.date_format'     => __('validation.nurse_notes.scheduled_for_date_format'),

            'signed_off_at.prohibited'      => __('validation.nurse_notes.signed_off_at_prohibited'),
        ];
    }
}
