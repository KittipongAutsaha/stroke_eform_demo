<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NurseNote;

/**
 * กำหนดสิทธิ์การเข้าถึง Nurse Notes ตามบทบาท:
 * - Admin, Nurse: สร้าง/แก้ไขได้ (แต่ห้ามแก้เมื่อบันทึกถูกปิดเคสแล้ว)
 * - Doctor: ดูได้เท่านั้น (view-only)
 * - Staff: ไม่อนุญาต
 * ป้องกัน cross-patient เพื่อไม่ให้เข้าถึงข้อมูลคนไข้ข้ามกัน
 */
class NurseNotePolicy
{
    /** อนุญาตให้เห็นรายการบันทึกพยาบาลทั้งหมด */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /** อนุญาตให้ดูบันทึกพยาบาลเฉพาะของผู้ป่วยใน route เดียวกัน */
    public function view(User $user, NurseNote $nurseNote): bool
    {
        if ($this->isCrossPatient($nurseNote)) {
            return false;
        }

        // Doctor/Nurse/Admin ดูได้
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

        // หากปิดเคสแล้ว (signed_off/cancelled) ห้ามแก้ไขทุกบทบาท รวมถึงแอดมิน
        if (method_exists($nurseNote, 'isLocked') && $nurseNote->isLocked()) {
            return false;
        }

        // Admin แก้ได้เมื่อยังไม่ปิดเคส
        if ($user->hasRole('admin')) {
            return true;
        }

        // Nurse แก้ได้เฉพาะของตัวเอง และยังไม่ปิดเคส
        if ($user->hasRole('nurse')) {
            return (int) $nurseNote->nurse_id === (int) $user->id;
        }

        // Doctor และบทบาทอื่นห้ามแก้ไข
        return false;
    }

    /** อนุญาตให้ลบเฉพาะ Admin */
    public function delete(User $user, NurseNote $nurseNote): bool
    {
        if ($this->isCrossPatient($nurseNote)) {
            return false;
        }

        // Admin ลบได้ทุกกรณี
        if ($user->hasRole('admin')) {
            return true;
        }

        // บทบาทอื่นห้ามลบ
        return false;
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

    /** ป้องกันการเข้าถึงข้ามคนไข้ */
    protected function isCrossPatient(NurseNote $nurseNote): bool
    {
        $patient = request()->route('patient');
        if (!$patient) {
            return false;
        }
        return (int) $nurseNote->patient_id !== (int) $patient->id;
    }
}
