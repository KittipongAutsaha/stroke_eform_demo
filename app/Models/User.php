<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Attributes ที่สามารถบันทึกผ่าน Mass Assignment ได้
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'requested_role',
    ];

    /**
     * Attributes ที่ซ่อนระหว่าง serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts — ใช้แปลงชนิดข้อมูลอัตโนมัติ
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ตรวจสอบว่า user ผ่านการอนุมัติหรือยัง
     */
    public function isApproved(): bool
    {
        return ! is_null($this->approved_at);
    }

    // ----------------------------------------------------------------------
    // ความสัมพันธ์กับ DoctorNote
    // ----------------------------------------------------------------------

    /**
     * ความสัมพันธ์: ผู้ใช้ (หมอ) มี doctor notes หลายรายการ
     */
    public function doctorNotes()
    {
        return $this->hasMany(DoctorNote::class, 'doctor_id');
    }

    // ----------------------------------------------------------------------
    // ความสัมพันธ์กับ NurseNote
    // ----------------------------------------------------------------------

    /**
     * ความสัมพันธ์: ผู้ใช้ (พยาบาล) มี nurse notes หลายรายการ
     */
    public function nurseNotes()
    {
        return $this->hasMany(NurseNote::class, 'nurse_id');
    }
}
