<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorNote;

/**
 * กำหนดสิทธิ์การเข้าถึง Doctor Notes ตามบทบาท:
 * - Admin: ทำได้เกือบทั้งหมด ยกเว้น "แก้ไข" เมื่อบันทึกถูกปิดจบ (signed_off/cancelled);
 *          ยังสามารถลบแบบ soft delete ได้เสมอ
 * - Doctor: ดูได้ทั้งหมด / แก้ไขได้เฉพาะของตนเองและยังไม่ปิดจบ
 * - Nurse: ไม่เห็นข้อมูล Note ของหมอ
 * ป้องกัน cross-patient เพื่อไม่ให้เข้าถึงข้อมูลคนไข้ข้ามกัน
 */
class DoctorNotePolicy
{
    /** อนุญาตให้เห็นรายการบันทึกแพทย์ทั้งหมด (เฉพาะ Admin/Doctor) */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /** อนุญาตให้ดูบันทึกแพทย์เฉพาะของผู้ป่วยใน route เดียวกัน (เฉพาะ Admin/Doctor) */
    public function view(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /** อนุญาตให้สร้างบันทึกแพทย์เฉพาะ Admin และ Doctor */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /** อนุญาตให้อัปเดตบันทึกแพทย์ตามเงื่อนไขบทบาทและสถานะ */
    public function update(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
            return false;
        }

        // หากปิดจบแล้ว → ห้ามแก้ไขทุกบทบาท (รวม Admin)
        if ($doctorNote->isLocked()) {
            return false;
        }

        // Admin แก้ได้เมื่อยังไม่ปิดจบ
        if ($user->hasRole('admin')) {
            return true;
        }

        // Doctor แก้ได้เฉพาะของตัวเอง และยังไม่ปิดจบ
        return $user->hasRole('doctor') && (int) $doctorNote->doctor_id === (int) $user->id;
    }

    /** อนุญาตให้ลบ (soft delete) เฉพาะ Admin เสมอ ไม่ว่าปิดจบหรือไม่ */
    public function delete(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
            return false;
        }

        return $user->hasRole('admin');
    }

    /** อนุญาตให้กู้คืนเฉพาะ Admin */
    public function restore(User $user, DoctorNote $doctorNote): bool
    {
        return $user->hasRole('admin');
    }

    /** ไม่อนุญาตให้ลบถาวร */
    public function forceDelete(User $user, DoctorNote $doctorNote): bool
    {
        return false;
    }

    /** ป้องกันการเข้าถึงข้ามคนไข้ */
    protected function isCrossPatient(DoctorNote $doctorNote): bool
    {
        $patient = request()->route('patient');
        if (!$patient) {
            return false;
        }

        return (int) $doctorNote->patient_id !== (int) $patient->id;
    }
}
