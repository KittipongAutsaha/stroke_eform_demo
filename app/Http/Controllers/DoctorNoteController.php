<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorNoteStoreRequest;
use App\Http\Requests\DoctorNoteUpdateRequest;
use App\Models\DoctorNote;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * จัดการบันทึกแพทย์ (Doctor Notes) แบบ CRUD
 * - ผูก Policy อัตโนมัติด้วย authorizeResource
 * - รองรับ nested route: /patients/{patient}/doctor-notes/...
 * - ตรวจสอบความสอดคล้อง patient ↔ doctorNote ทุกครั้ง
 */
class DoctorNoteController extends Controller
{
    /** ผูก Policy ให้กับ resource controller ทั้งชุด */
    public function __construct()
    {
        // 'doctor_note' ต้องตรงกับชื่อพารามิเตอร์ใน Route::resource
        $this->authorizeResource(DoctorNote::class, 'doctor_note');
    }

    /**
     * แสดงรายการบันทึกแพทย์ของผู้ป่วยที่ระบุ
     */
    public function index(Patient $patient): View
    {
        $notes = $patient->doctorNotes()
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('doctor_notes.index', [
            'patient' => $patient,
            'notes'   => $notes,
        ]);
    }

    /**
     * แสดงฟอร์มสร้างบันทึกแพทย์ใหม่
     */
    public function create(Patient $patient): View
    {
        return view('doctor_notes.form', [
            'patient' => $patient,
            'note'    => new DoctorNote(),
            'mode'    => 'create',
        ]);
    }

    /**
     * บันทึกบันทึกแพทย์ใหม่
     * - จำกัดสถานะตอนสร้างให้เป็น planned หรือ in_progress เท่านั้น
     * - ไม่รับ/ไม่ตั้งค่า signed_off_at จากฟอร์ม (ปิดเคสไม่ได้ในขั้นสร้าง)
     */
    public function store(DoctorNoteStoreRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();

        // จำกัดสถานะตอนสร้าง
        $allowedCreateStatuses = ['planned', 'in_progress'];
        if (! isset($data['status']) || ! in_array($data['status'], $allowedCreateStatuses, true)) {
            $data['status'] = 'in_progress';
        }

        // ไม่ให้ตั้งค่าเวลาลงนามจากฟอร์มตอนสร้าง
        unset($data['signed_off_at']);

        // กำหนดข้อมูลอ้างอิงผู้ป่วย/แพทย์และบันทึก IP สำหรับ audit
        $data['patient_id']     = $patient->id;
        $data['doctor_id']      = $request->user()->id;
        $data['created_by_ip']  = $request->ip();
        $data['updated_by_ip']  = $request->ip();

        $note = DoctorNote::create($data);

        return redirect()
            ->route('patients.doctor-notes.show', [$patient, $note])
            ->with('success', __('doctor_notes.created'));
    }

    /**
     * แสดงรายละเอียดบันทึกแพทย์
     */
    public function show(Patient $patient, DoctorNote $doctor_note): View
    {
        abort_unless((int) $doctor_note->patient_id === (int) $patient->id, 404);

        return view('doctor_notes.show', [
            'patient' => $patient,
            'note'    => $doctor_note,
        ]);
    }

    /**
     * แสดงฟอร์มแก้ไขบันทึกแพทย์
     */
    public function edit(Patient $patient, DoctorNote $doctor_note): View
    {
        abort_unless((int) $doctor_note->patient_id === (int) $patient->id, 404);

        return view('doctor_notes.form', [
            'patient' => $patient,
            'note'    => $doctor_note,
            'mode'    => 'edit',
        ]);
    }

    /**
     * อัปเดตบันทึกแพทย์
     * - ล็อกการแก้ไขเมื่อบันทึกถูกปิดเคสแล้ว (isLocked())
     * - อนุญาตการเปลี่ยนสถานะตาม flow เท่านั้น:
     *     • planned     → in_progress (เริ่มรักษา; ไม่ตั้ง signed_off_at)
     *     • planned     → cancelled   → ตั้ง signed_off_at = now()
     *     • in_progress → signed_off  → ตั้ง signed_off_at = now()
     * - กรองค่า null ออกจาก validated data เพื่อป้องกันทับค่าเดิมด้วย null
     */
    public function update(DoctorNoteUpdateRequest $request, Patient $patient, DoctorNote $doctor_note): RedirectResponse
    {
        abort_unless((int) $doctor_note->patient_id === (int) $patient->id, 404);

        // ถ้าบันทึกถูกปิดเคสแล้ว (isLocked) → ห้ามแก้ไข
        if ($doctor_note->isLocked()) {
            return redirect()
                ->route('patients.doctor-notes.show', [$patient, $doctor_note])
                ->with('error', __('doctor_notes.locked'));
        }

        $data = $request->validated();

        // จัดการการเปลี่ยนสถานะตาม flow
        if (isset($data['status'])) {
            $current = $doctor_note->status;
            $next    = $data['status'];

            $allowed = false;

            // planned → in_progress (เริ่มรักษา; ไม่ตั้งเวลา signed_off_at)
            if ($current === 'planned' && $next === 'in_progress') {
                $allowed = true;
                unset($data['signed_off_at']);
            }

            // planned → cancelled (ปิดเคสและลงเวลาอัตโนมัติ)
            if ($current === 'planned' && $next === 'cancelled') {
                $allowed = true;
                $data['signed_off_at'] = now();
            }

            // in_progress → signed_off (ปิดเคสและลงเวลาอัตโนมัติ)
            if ($current === 'in_progress' && $next === 'signed_off') {
                $allowed = true;
                $data['signed_off_at'] = now();
            }

            // ถ้าไม่ได้อยู่ใน transition ที่อนุญาต → ไม่ให้เปลี่ยนสถานะ
            if (! $allowed) {
                unset($data['status']);
                unset($data['signed_off_at']);
            }
        }

        // ไม่ให้ตั้งค่าเวลาลงนามจากฟอร์มนอกเหนือจากกรณีที่ระบบกำหนดเอง
        if (isset($data['signed_off_at']) && ! isset($data['status'])) {
            unset($data['signed_off_at']);
        }

        // กรองคีย์ที่มีค่า null ออกก่อนอัปเดต (กัน ConvertEmptyStringsToNull)
        $data = array_filter($data, static fn($value) => !is_null($value));

        $data['updated_by_ip'] = $request->ip();

        $doctor_note->update($data);

        return redirect()
            ->route('patients.doctor-notes.show', [$patient, $doctor_note])
            ->with('success', __('doctor_notes.updated'));
    }

    /**
     * ลบบันทึกแพทย์ (soft delete)
     */
    public function destroy(Patient $patient, DoctorNote $doctor_note): RedirectResponse
    {
        abort_unless((int) $doctor_note->patient_id === (int) $patient->id, 404);

        // ย้ำตรวจสิทธิ์ลบระดับ Action (กันกรณี authorizeResource ไม่ทำงานจากชื่อพารามิเตอร์ route)
        $this->authorize('delete', $doctor_note);

        $doctor_note->delete();

        return redirect()
            ->route('patients.doctor-notes.index', $patient)
            ->with('success', __('doctor_notes.deleted'));
    }
}
