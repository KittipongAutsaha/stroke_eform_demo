<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserApproval
{
    /**
     * บังคับให้ผู้ใช้ต้องผ่านการอนุมัติก่อนจึงจะเข้าถึงหน้าอื่น ๆ ได้
     * - ถ้ายังไม่อนุมัติ: redirect ไปหน้า pending-approval (หรือส่ง 403 JSON เมื่อเป็น API/AJAX)
     * - กันการวนลูป: อนุญาตให้เข้าหน้า pending-approval และเส้นทาง logout ได้
     * - ถ้าไม่ล็อกอิน ปล่อยให้ middleware 'auth' ตัวอื่นจัดการต่อ
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ยังไม่ล็อกอิน → ปล่อยผ่านไปให้ 'auth' ดูแล
        if (! $user) {
            return $next($request);
        }

        // ผู้ใช้ต้องได้รับการอนุมัติ
        if (! $user->isApproved()) {
            // อนุญาตเส้นทางที่จำเป็นเพื่อป้องกันลูป
            if ($request->routeIs('pending-approval') || $request->routeIs('logout') || $request->is('logout')) {
                return $next($request);
            }

            // ถ้าเป็นคำขอที่คาดหวัง JSON ให้ตอบ 403 แทนการ redirect
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is not approved yet'], 403);
            }

            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
