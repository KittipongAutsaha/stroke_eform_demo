<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ตรวจสอบข้อมูลก่อน "สร้าง" Doctor Note
 * - ใช้กับ: POST /patients/{patient}/doctor-notes
 * - datetime-local ใช้รูปแบบ Y-m-d\TH:i
 * - ห้ามส่ง signed_off_at (ระบบจะตั้งให้เองเมื่อปิดเคส)
 */
class DoctorNoteStoreRequest extends FormRequest
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
            'chief_complaint' => ['required', 'string', 'max:500'],
            'diagnosis'       => ['required', 'string', 'max:500'],
            'physical_exam'   => ['required', 'string'],

            // ฟิลด์เสริม
            'differential_diagnosis' => ['nullable', 'string', 'max:500'],
            'clinical_summary'       => ['nullable', 'string'],
            'nihss_score'            => ['nullable', 'integer', 'min:0', 'max:42'],
            'gcs_score'              => ['nullable', 'integer', 'min:0', 'max:15'],
            'imaging_summary'        => ['nullable', 'string'],
            'lvo_suspected'          => ['nullable', 'boolean'],
            'treatment_plan'         => ['nullable', 'string'],
            'orders'                 => ['nullable', 'string'],
            'prescription_note'      => ['nullable', 'string'],

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
            'chief_complaint.required' => __('validation.chief_complaint_required'),
            'diagnosis.required'       => __('validation.diagnosis_required'),
            'physical_exam.required'   => __('validation.physical_exam_required'),
            'status.required'          => __('validation.status_required'),
            'recorded_at.required'     => __('validation.recorded_at_required'),

            'status.in'                 => __('validation.doctor_notes.status_in'),
            'recorded_at.date_format'   => __('validation.doctor_notes.recorded_at_date_format'),
            'scheduled_for.date_format' => __('validation.doctor_notes.scheduled_for_date_format'),

            'signed_off_at.prohibited'  => __('validation.doctor_notes.signed_off_at_prohibited'),

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
