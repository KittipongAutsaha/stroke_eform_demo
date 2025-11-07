<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RestrictStaffFromPatientArea
{
    /**
     * บล็อกผู้ใช้ที่มีบทบาท Staff ไม่ให้เข้าพื้นที่ผู้ป่วยและโน้ต
     * - ใช้กับกลุ่มเส้นทางที่เกี่ยวกับผู้ป่วย/โน้ตเท่านั้น (กำหนดใน routes)
     * - บันทึกการพยายามเข้าถึง และตอบกลับเป็น 404 เพื่อซ่อนการมีอยู่ของหน้า
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasRole('staff')) {
            Log::warning('Unauthorized access attempt by Staff', [
                'user_id'    => $user->id,
                'name'       => $user->name ?? 'unknown',
                'route'      => $request->path(),
                'method'     => $request->method(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'time'       => now()->toISOString(),
            ]);

            abort(404);
        }

        return $next($request);
    }
}
