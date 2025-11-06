<?php

namespace App\Http\Controllers;

use App\Http\Requests\NurseNoteStoreRequest;
use App\Http\Requests\NurseNoteUpdateRequest;
use App\Models\NurseNote;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * จัดการบันทึกพยาบาล (Nurse Notes) แบบ CRUD
 * - ผูก Policy อัตโนมัติด้วย authorizeResource
 * - รองรับ nested route: /patients/{patient}/nurse-notes/...
 * - ตรวจสอบความสอดคล้อง patient ↔ nurseNote ทุกครั้ง
 */
class NurseNoteController extends Controller
{
    /** ผูก Policy ให้กับ resource controller ทั้งชุด */
    public function __construct()
    {
        // 'nurse_note' ต้องตรงกับชื่อพารามิเตอร์ใน Route::resource
        $this->authorizeResource(NurseNote::class, 'nurse_note');
    }

    /**
     * แสดงรายการบันทึกพยาบาลของผู้ป่วยที่ระบุ
     */
    public function index(Patient $patient): View
    {
        $notes = $patient->nurseNotes()
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('nurse_notes.index', [
            'patient' => $patient,
            'notes'   => $notes,
        ]);
    }

    /**
     * แสดงฟอร์มสร้างบันทึกพยาบาลใหม่
     */
    public function create(Patient $patient): View
    {
        return view('nurse_notes.form', [
            'patient' => $patient,
            'note'    => new NurseNote(),
            'mode'    => 'create',
        ]);
    }

    /**
     * บันทึกบันทึกพยาบาลใหม่
     * - จำกัดสถานะตอนสร้างให้เป็น planned หรือ in_progress เท่านั้น
     * - ไม่รับ/ไม่ตั้งค่า signed_off_at จากฟอร์ม (ปิดเคสไม่ได้ในขั้นสร้าง)
     */
    public function store(NurseNoteStoreRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();

        // จำกัดสถานะตอนสร้าง
        $allowedCreateStatuses = ['planned', 'in_progress'];
        if (! isset($data['status']) || ! in_array($data['status'], $allowedCreateStatuses, true)) {
            $data['status'] = 'in_progress';
        }

        // ไม่ให้ตั้งค่าเวลาลงนามจากฟอร์มตอนสร้าง
        unset($data['signed_off_at']);

        // กำหนดข้อมูลอ้างอิงผู้ป่วย/พยาบาลและบันทึก IP สำหรับ audit
        $data['patient_id']     = $patient->id;
        $data['nurse_id']       = $request->user()->id;
        $data['created_by_ip']  = $request->ip();
        $data['updated_by_ip']  = $request->ip();

        $note = NurseNote::create($data);

        return redirect()
            ->route('patients.nurse-notes.show', [$patient, $note])
            ->with('success', __('nurse_notes.created'));
    }

    /**
     * แสดงรายละเอียดบันทึกพยาบาล
     */
    public function show(Patient $patient, NurseNote $nurse_note): View
    {
        abort_unless((int) $nurse_note->patient_id === (int) $patient->id, 404);

        return view('nurse_notes.show', [
            'patient' => $patient,
            'note'    => $nurse_note,
        ]);
    }

    /**
     * แสดงฟอร์มแก้ไขบันทึกพยาบาล
     */
    public function edit(Patient $patient, NurseNote $nurse_note): View
    {
        abort_unless((int) $nurse_note->patient_id === (int) $patient->id, 404);

        return view('nurse_notes.form', [
            'patient' => $patient,
            'note'    => $nurse_note,
            'mode'    => 'edit',
        ]);
    }

    /**
     * อัปเดตบันทึกพยาบาล
     * - ล็อกการแก้ไขเมื่อบันทึกถูกปิดเคสแล้ว (status ∈ {signed_off, cancelled})
     * - อนุญาตการเปลี่ยนสถานะตาม flow เท่านั้น:
     *     • planned     → in_progress
     *     • planned     → cancelled   → ตั้ง signed_off_at = now()
     *     • in_progress → signed_off  → ตั้ง signed_off_at = now()
     */
    public function update(NurseNoteUpdateRequest $request, Patient $patient, NurseNote $nurse_note): RedirectResponse
    {
        abort_unless((int) $nurse_note->patient_id === (int) $patient->id, 404);

        // ถ้าบันทึกถูกปิดเคสแล้ว → ห้ามแก้ไข
        if (in_array($nurse_note->status, ['signed_off', 'cancelled'], true)) {
            return redirect()
                ->route('patients.nurse-notes.show', [$patient, $nurse_note])
                ->with('error', __('nurse_notes.locked'));
        }

        $data = $request->validated();

        // จัดการการเปลี่ยนสถานะตาม flow
        if (isset($data['status'])) {
            $current = $nurse_note->status;
            $next    = $data['status'];

            $allowed = false;

            if ($current === 'planned' && $next === 'in_progress') {
                $allowed = true;
                unset($data['signed_off_at']);
            }

            if ($current === 'planned' && $next === 'cancelled') {
                $allowed = true;
                $data['signed_off_at'] = now();
            }

            if ($current === 'in_progress' && $next === 'signed_off') {
                $allowed = true;
                $data['signed_off_at'] = now();
            }

            if (! $allowed) {
                unset($data['status']);
                unset($data['signed_off_at']);
            }
        }

        // ป้องกันการตั้งค่าเวลาลงนามเอง
        if (isset($data['signed_off_at']) && ! isset($data['status'])) {
            unset($data['signed_off_at']);
        }

        $data = array_filter($data, static fn($value) => !is_null($value));
        $data['updated_by_ip'] = $request->ip();

        $nurse_note->update($data);

        return redirect()
            ->route('patients.nurse-notes.show', [$patient, $nurse_note])
            ->with('success', __('nurse_notes.updated'));
    }

    /**
     * ลบบันทึกพยาบาล (soft delete)
     */
    public function destroy(Patient $patient, NurseNote $nurse_note): RedirectResponse
    {
        abort_unless((int) $nurse_note->patient_id === (int) $patient->id, 404);
        $this->authorize('delete', $nurse_note);

        $nurse_note->delete();

        return redirect()
            ->route('patients.nurse-notes.index', $patient)
            ->with('success', __('nurse_notes.deleted'));
    }
}
