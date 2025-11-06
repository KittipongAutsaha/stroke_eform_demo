<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// นำเข้าโมเดลและโพลิซีที่ต้องแม็ป
use App\Models\Patient;
use App\Policies\PatientPolicy;
use App\Models\DoctorNote;
use App\Policies\DoctorNotePolicy;
use App\Models\NurseNote;
use App\Policies\NurseNotePolicy;

/**
 * ลงทะเบียน Policy และตั้งค่าเกตพื้นฐานสำหรับบทบาทผู้ดูแลระบบ
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * แม็ปโมเดลกับโพลิซีของระบบ
     */
    protected $policies = [
        Patient::class    => PatientPolicy::class,
        DoctorNote::class => DoctorNotePolicy::class,
        NurseNote::class  => NurseNotePolicy::class,
    ];

    /**
     * บูตระบบกำหนดสิทธิ์
     * - เปิดสิทธิ์ทุกความสามารถให้ผู้ใช้ที่มีบทบาท admin ล่วงหน้าด้วย Gate::before
     * - ยกเว้นกรณี update บน DoctorNote/NurseNote ที่ปิดเคสแล้ว (isLocked) → ไม่อนุญาต
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability, ...$arguments) {
            if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
                return null;
            }

            // แอดมินห้าม "แก้ไข" ถ้า note ปิดเคสแล้ว (signed_off / cancelled)
            if ($ability === 'update' && isset($arguments[0])) {
                $target = $arguments[0];

                if ($target instanceof DoctorNote && method_exists($target, 'isLocked') && $target->isLocked()) {
                    return false;
                }

                if ($target instanceof NurseNote && method_exists($target, 'isLocked') && $target->isLocked()) {
                    return false;
                }
            }

            // นอกนั้น แอดมินผ่านทั้งหมด (รวมถึงลบ)
            return true;
        });
    }
}
