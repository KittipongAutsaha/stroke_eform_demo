<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * ดูข้อมูลผู้ป่วยแต่ละรายการ
     * admin, doctor, nurse → ดูได้
     */
    public function view(User $user, Patient $patient): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /**
     * เพิ่มข้อมูลผู้ป่วย
     * admin, doctor, nurse → เพิ่มได้
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /**
     * แก้ไขข้อมูลผู้ป่วย (ทั่วไป)
     * admin, doctor, nurse → แก้ได้
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /**
     * ลบข้อมูลผู้ป่วย
     * admin เท่านั้น
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * แก้ไขค่า HN
     * admin เท่านั้น
     */
    public function editHn(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin');
    }
}
