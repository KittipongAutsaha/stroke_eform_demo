<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * แสดงรายชื่อผู้ใช้ + ฟิลเตอร์ only_pending (เฉพาะที่ยังไม่อนุมัติ)
     */
    public function index(Request $request)
    {
        $q = User::query()->with('roles');

        // ถ้ามีการติ๊ก “Pending Approval” ในหน้า admin
        if ($request->boolean('only_pending')) {
            $q->whereNull('approved_at');
        }

        $users = $q->orderByRaw('approved_at IS NULL DESC')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * อนุมัติผู้ใช้:
     * - เซ็ต approved_at = now()
     * - ถ้า email_verified_at ยังเป็น null ให้ตั้งค่าเป็น now()
     * - Assign Spatie role จริง (จาก role ที่ admin เลือก หรือ requested_role เดิม)
     */
    public function approve(Request $request, User $user)
    {
        // ถ้าอนุมัติแล้ว ไม่ต้องทำซ้ำ
        if ($user->approved_at) {
            return back()->with('status', __('User is already approved.'));
        }

        // รับ role ที่ admin เลือกจากฟอร์ม (optional)
        $pickedRole = trim((string) $request->input('role', ''));

        // ถ้า admin ไม่ได้เลือก → ใช้ requested_role เดิมของ user
        $roleName = $pickedRole !== '' ? $pickedRole : $user->requested_role;

        // ถ้าไม่มี role เลย → error
        if (empty($roleName)) {
            throw ValidationException::withMessages([
                'requested_role' => __('User has no requested role.'),
            ]);
        }

        // ตรวจว่า role ที่ส่งมามีอยู่จริงในระบบ Spatie หรือไม่
        if (! Role::where('name', $roleName)->exists()) {
            throw ValidationException::withMessages([
                'role' => __('Requested role is invalid: :role', ['role' => $roleName]),
            ]);
        }

        // ทำทั้งหมดใน transaction เดียว
        DB::transaction(function () use ($user, $roleName) {
            // 1) ตั้ง approved_at
            $user->approved_at = now();

            // 2) ถ้ายังไม่ได้ verify email → ให้ verify ทันที
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }

            $user->save();

            // 3) Assign role จริงจาก requested_role หรือจากที่ admin เลือก
            $user->syncRoles([$roleName]);

            // ❗ ไม่ล้าง requested_role — เก็บไว้เป็นประวัติย้อนหลัง
        });

        return back()->with('status', __('User approved successfully.'));
    }
}
