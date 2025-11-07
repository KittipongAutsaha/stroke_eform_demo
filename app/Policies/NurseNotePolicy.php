<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NurseNote;

/**
 * กำหนดสิทธิ์การเข้าถึง Nurse Notes ตามบทบาท:
 * - Admin: ทำได้เกือบทั้งหมด ยกเว้น "แก้ไข" เมื่อบันทึกถูกปิดจบ (signed_off/cancelled);
 *          แต่ยังสามารถลบแบบ soft delete ได้เสมอ
 * - Nurse: สร้าง/แก้ไขได้เฉพาะของตนเองและเฉพาะตอนยังไม่ปิดจบ
 * - Doctor: ดูได้เท่านั้น (view-only)
 * - Staff: ไม่อนุญาต
 * ป้องกัน cross-patient ไม่ให้เข้าถึงข้อมูลคนไข้ข้ามกัน
 */
class NurseNotePolicy
{
    /** อนุญาตให้เห็นรายการบันทึกพยาบาลทั้งหมด (Admin/Doctor/Nurse) */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /** อนุญาตให้ดูบันทึกพยาบาลเฉพาะของผู้ป่วยเดียวกัน */
    public function view(User $user, NurseNote $nurseNote): bool
    {
        if ($this->isCrossPatient($nurseNote)) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /** อนุญาตให้สร้างบันทึกพยาบาลเฉพาะ Admin และ Nurse */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'nurse']);
    }

    /** อนุญาตให้อัปเดตบันทึกพยาบาลตามเงื่อนไขบทบาท */
    public function update(User $user, NurseNote $nurseNote): bool
    {
        if ($this->isCrossPatient($nurseNote)) {
            return false;
        }

        // หากปิดจบแล้ว → ห้ามแก้ไขทุกบทบาท (รวม Admin)
        if ($nurseNote->isLocked()) {
            return false;
        }

        // Admin แก้ได้เมื่อยังไม่ปิดจบ
        if ($user->hasRole('admin')) {
            return true;
        }

        // Nurse แก้ได้เฉพาะของตนเอง
        if ($user->hasRole('nurse')) {
            return (int) $nurseNote->nurse_id === (int) $user->id;
        }

        // Doctor และบทบาทอื่นแก้ไม่ได้
        return false;
    }

    /** soft delete ได้เฉพาะ Admin */
    public function delete(User $user, NurseNote $nurseNote): bool
    {
        if ($this->isCrossPatient($nurseNote)) {
            return false;
        }

        return $user->hasRole('admin');
    }

    /** อนุญาตให้กู้คืนเฉพาะ Admin */
    public function restore(User $user, NurseNote $nurseNote): bool
    {
        return $user->hasRole('admin');
    }

    /** ไม่อนุญาตให้ลบถาวร */
    public function forceDelete(User $user, NurseNote $nurseNote): bool
    {
        return false;
    }

    /** ตรวจว่ากำลังเข้าถึงข้อมูลของ patient คนอื่นหรือไม่ */
    protected function isCrossPatient(NurseNote $nurseNote): bool
    {
        $patient = request()->route('patient');

        if (!$patient) {
            return false;
        }

        return (int) $nurseNote->patient_id !== (int) $patient->id;
    }
}
