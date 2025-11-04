<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ตรวจสอบข้อมูลก่อน "อัปเดต" Doctor Note
 * - ใช้กับ: PUT/PATCH /patients/{patient}/doctor-notes/{doctorNote}
 * - ส่งเฉพาะฟิลด์ที่จะแก้ไข (ใช้ sometimes)
 * - datetime-local ใช้รูปแบบ Y-m-d\TH:i
 */
class DoctorNoteUpdateRequest extends FormRequest
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
            'chief_complaint'  => ['sometimes', 'required', 'string', 'max:500'],
            'diagnosis'        => ['sometimes', 'required', 'string', 'max:500'],
            'physical_exam'    => ['sometimes', 'required', 'string'],

            // ฟิลด์เสริม
            'differential_diagnosis' => ['sometimes', 'nullable', 'string', 'max:500'],
            'clinical_summary'       => ['sometimes', 'nullable', 'string'],
            'nihss_score'            => ['sometimes', 'nullable', 'integer', 'min:0', 'max:42'],
            'gcs_score'              => ['sometimes', 'nullable', 'integer', 'min:0', 'max:15'],
            'imaging_summary'        => ['sometimes', 'nullable', 'string'],
            'lvo_suspected'          => ['sometimes', 'nullable', 'boolean'],
            'treatment_plan'         => ['sometimes', 'nullable', 'string'],
            'orders'                 => ['sometimes', 'nullable', 'string'],
            'prescription_note'      => ['sometimes', 'nullable', 'string'],

            // สถานะ (ตรวจ flow ใน Controller)
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
            'chief_complaint.required' => __('validation.chief_complaint_required'),
            'diagnosis.required'       => __('validation.diagnosis_required'),
            'physical_exam.required'   => __('validation.physical_exam_required'),
            'status.required'          => __('validation.status_required'),
            'recorded_at.required'     => __('validation.recorded_at_required'),

            'status.in'                 => __('validation.doctor_notes.status_in'),
            'recorded_at.date_format'   => __('validation.doctor_notes.recorded_at_date_format'),
            'scheduled_for.date_format' => __('validation.doctor_notes.scheduled_for_date_format'),

            // ประเภทข้อมูล/ช่วงค่า
            'lvo_suspected.boolean' => __('validation.doctor_notes.lvo_suspected_boolean'),
            'nihss_score.integer'   => __('validation.doctor_notes.nihss_score_integer'),
            'nihss_score.min'       => __('validation.doctor_notes.nihss_score_min'),
            'nihss_score.max'       => __('validation.doctor_notes.nihss_score_max'),
            'gcs_score.integer'     => __('validation.doctor_notes.gcs_score_integer'),
            'gcs_score.min'         => __('validation.doctor_notes.gcs_score_min'),
            'gcs_score.max'         => __('validation.doctor_notes.gcs_score_max'),
        ];
    }
}
