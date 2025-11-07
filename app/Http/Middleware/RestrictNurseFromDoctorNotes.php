<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RestrictNurseFromDoctorNotes
{
    /**
     * บล็อกบทบาท Nurse ไม่ให้เข้าถึงเส้นทางของ Doctor Notes
     * ใช้เฉพาะกับ route group ของ patients.doctor-notes.*
     * - ถ้าเป็น Nurse → บันทึก Log แล้วตอบกลับ 404 เพื่อซ่อนการมีอยู่ของหน้า
     * - Admin/Doctor ผ่านได้
     * - Staff ควรถูกกันด้วย RestrictStaffFromPatientArea อยู่แล้ว (อีกชั้น)
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // ถ้ายังไม่ล็อกอิน ปล่อยให้ middleware 'auth' จัดการต่อ
        if (! $user) {
            return $next($request);
        }

        if ($user->hasRole('nurse')) {
            Log::warning('Unauthorized access attempt by Nurse to Doctor Notes', [
                'user_id'    => $user->id,
                'name'       => $user->name ?? 'unknown',
                'route'      => $request->path(),
                'method'     => $request->method(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'time'       => now()->toISOString(),
            ]);

            // ใช้ 404 เพื่อไม่เปิดเผยว่าเส้นทางมีอยู่
            abort(404);
        }

        return $next($request);
    }
}
