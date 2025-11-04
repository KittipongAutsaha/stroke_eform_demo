<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorNote;

/**
 * กำหนดสิทธิ์การเข้าถึง Doctor Notes ตามบทบาท:
 * - Admin: ทำได้ทุกอย่าง ยกเว้น "แก้ไข" เมื่อบันทึกถูกปิดเคสแล้ว
 * - Doctor: ดูได้ทั้งหมด / แก้ไขได้เฉพาะของตนเองและยังไม่ปิดเคส
 * - Nurse: ดูได้เท่านั้น
 * ป้องกัน cross-patient เพื่อไม่ให้เข้าถึงข้อมูลคนไข้ข้ามกัน
 */
class DoctorNotePolicy
{
    /** อนุญาตให้เห็นรายการบันทึกแพทย์ทั้งหมด */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /** อนุญาตให้ดูบันทึกแพทย์เฉพาะของผู้ป่วยใน route เดียวกัน */
    public function view(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /** อนุญาตให้สร้างบันทึกแพทย์เฉพาะ Admin และ Doctor */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /** อนุญาตให้อัปเดตบันทึกแพทย์ตามเงื่อนไขบทบาท */
    public function update(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
            return false;
        }

        // หากปิดเคสแล้ว (signed_off/cancelled) ห้ามแก้ไขทุกบทบาท รวมถึงแอดมิน
        if ($doctorNote->isLocked()) {
            return false;
        }

        // Admin แก้ได้เมื่อยังไม่ปิดเคส
        if ($user->hasRole('admin')) {
            return true;
        }

        // Doctor แก้ได้เฉพาะของตัวเอง และยังไม่ปิดเคส
        return $user->hasRole('doctor') && (int) $doctorNote->doctor_id === (int) $user->id;
    }

    /** อนุญาตให้ลบเฉพาะ Admin */
    public function delete(User $user, DoctorNote $doctorNote): bool
    {
        if ($this->isCrossPatient($doctorNote)) {
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
