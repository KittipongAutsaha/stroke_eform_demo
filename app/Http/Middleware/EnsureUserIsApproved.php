<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ถ้ายังไม่อนุมัติให้ redirect ไปหน้ารออนุมัติ
        if ($user && ! $user->isApproved()) {
            // กันกรณีเผลอไปผูก middleware นี้กว้าง ๆ ทั้งกลุ่ม แล้วทำให้ loop
            if (! $request->routeIs('pending-approval')) {
                return redirect()->route('pending-approval');
            }
        }

        return $next($request);
    }
}
