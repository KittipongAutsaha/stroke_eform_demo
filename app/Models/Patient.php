<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // ระบุตัวตนพื้นฐาน
        'hn',
        'cid',
        'first_name',
        'last_name',
        'dob',
        'sex',

        // ชีวข้อมูล
        'blood_group',
        'rh_factor',

        // การติดต่อ / ที่อยู่
        'phone',
        'address_short',
        'address_full',
        'postal_code',

        // ผู้ติดต่อฉุกเฉิน
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',

        // สัญชาติ / ภาษา
        'nationality',
        'preferred_language',

        // สิทธิ์ประกัน
        'insurance_scheme',
        'insurance_no',

        // ความยินยอม / บันทึกทั่วไป
        'consent_at',
        'consent_note',
        'note_general',

        // ระบบ
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dob' => 'date',
        'consent_at' => 'datetime',
    ];

    // Accessor: รวมชื่อเต็ม (First Name + Last Name)
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // Accessor: รวมที่อยู่แบบเต็ม (address_full + postal_code)
    // ใช้สำหรับแสดงผลในหน้าโปรไฟล์ / รายงาน / export
    public function getFullAddressAttribute(): ?string
    {
        if (!$this->address_full && !$this->postal_code) {
            return null;
        }

        return trim("{$this->address_full} {$this->postal_code}");
    }

    // ความสัมพันธ์: ผู้ป่วยมี doctor notes หลายรายการ
    public function doctorNotes()
    {
        return $this->hasMany(DoctorNote::class);
    }

    // ความสัมพันธ์: รายชื่อหมอทั้งหมดที่เคยดูแลผู้ป่วยรายนี้ (distinct ผ่านตาราง doctor_notes)
    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_notes', 'patient_id', 'doctor_id')->distinct();
    }
}
