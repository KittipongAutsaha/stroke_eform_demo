<?php

/** @method bool hasRole($role) */

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Requests\PatientStoreRequest;
use App\Http\Requests\PatientUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * แสดงรายชื่อผู้ป่วยทั้งหมด / ค้นหาผู้ป่วย
     * - ไม่มี q → แสดงรายการทั้งหมด (ล่าสุดก่อน)
     * - มี q → ค้นหาแบบ Exact ก่อน แล้วจึง LIKE และจัดลำดับผลลัพธ์ให้ Exact อยู่บนสุด
     * - กรณีพบเพียง 1 รายการเมื่อมี q → ไปหน้าแสดงรายละเอียด (เฉพาะบทบาทที่อนุญาตให้ดูได้)
     */
    public function index(Request $request): View|RedirectResponse
    {
        $q = trim((string) $request->query('q', ''));

        // กรณีไม่มีคำค้นหา → แสดงรายการทั้งหมดแบบ paginate
        if ($q === '') {
            $patients = Patient::latest()->paginate(20)->withQueryString();
            return view('patients.index', compact('patients', 'q'));
        }

        // กรณีมีคำค้นหา → สร้างเงื่อนไข Exact + LIKE
        $builder = Patient::query()
            ->where(function ($sub) use ($q) {
                // Exact matches: hn, cid, phone, full_name (first_name + ' ' + last_name)
                $sub->orWhere('hn', $q)
                    ->orWhere('cid', $q)
                    ->orWhere('phone', $q)
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) = ?", [$q]);

                // LIKE matches: first_name, last_name, hn (partial)
                $sub->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('hn', 'like', "%{$q}%");
            })
            // จัดลำดับ: Exact อยู่บนสุด แล้วค่อย LIKE
            ->orderByRaw("
                CASE
                    WHEN hn = ? THEN 0
                    WHEN cid = ? THEN 0
                    WHEN phone = ? THEN 0
                    WHEN CONCAT(first_name,' ',last_name) = ? THEN 0
                    WHEN first_name LIKE ? THEN 1
                    WHEN last_name LIKE ? THEN 1
                    WHEN hn LIKE ? THEN 1
                    ELSE 2
                END
            ", [$q, $q, $q, $q, "%{$q}%", "%{$q}%", "%{$q}%"])
            ->latest('id'); // ตัวกันกรณีคะแนนเท่ากัน ให้ล่าสุดมาก่อน

        // ตรวจจำนวนผลลัพธ์เบื้องต้น
        $peek = (clone $builder)->limit(2)->get(['id']);

        // ถ้าพบเพียง 1 รายการ:
        // - บทบาท staff → แสดงตาราง (ไม่ redirect)
        // - บทบาทอื่นที่อนุญาต → redirect ไปหน้า show
        if ($peek->count() === 1) {
            $user = Auth::user(); // มี auth middleware ครอบ จึงคาดว่ามีเสมอ
            if ($user && $user->hasRole('staff')) {
                $patients = $builder->paginate(20)->withQueryString();
                return view('patients.index', compact('patients', 'q'));
            }

            return redirect()->route('patients.show', ['patient' => $peek->first()->id]);
        }

        // ไม่พบผลลัพธ์เมื่อมีการค้นหา → ส่งหน้า index ว่างพร้อมข้อความ
        if ($peek->isEmpty()) {
            // แสดงหน้ารายการว่าง แต่คง q ไว้เพื่อให้ผู้ใช้แก้คำค้นได้
            // หมายเหตุ: ใช้ paginate บนคิวรีที่ไม่คืนผลลัพธ์ เพื่อให้ UX หน้าเดียวกัน
            $patients = Patient::whereRaw('1=0')->paginate(20)->withQueryString();
            return view('patients.index', compact('patients', 'q'))
                ->with('warning', __('No results found.'));
        }

        // พบหลายรายการ → แสดงตารางผลลัพธ์
        $patients = $builder->paginate(20)->withQueryString();
        return view('patients.index', compact('patients', 'q'));
    }

    /**
     * แสดงฟอร์มเพิ่มผู้ป่วยใหม่
     */
    public function create(): View
    {
        return view('patients.create');
    }

    /**
     * บันทึกข้อมูลผู้ป่วยใหม่ลงฐานข้อมูล
     */
    public function store(PatientStoreRequest $request): RedirectResponse
    {
        // ตรวจสอบความถูกต้องของข้อมูล
        $data = $request->validated();

        // ระบุผู้สร้างและผู้แก้ไขล่าสุด
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // สร้างข้อมูลผู้ป่วยใหม่
        Patient::create($data);

        // กลับไปหน้ารายชื่อ พร้อมข้อความสำเร็จ
        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.created_success'));
    }

    /**
     * แสดงรายละเอียดของผู้ป่วยแต่ละราย
     */
    public function show(Patient $patient): View
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลผู้ป่วย
     * หมายเหตุ: อนุญาตให้ non-admin เข้าหน้าแก้ไขได้
     * การควบคุมสิทธิ์แก้ HN จะทำในขั้น update() เท่านั้น
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * บันทึกการแก้ไขข้อมูลผู้ป่วย
     */
    public function update(PatientUpdateRequest $request, Patient $patient): RedirectResponse
    {
        // ตรวจสอบความถูกต้องของข้อมูล
        $data = $request->validated();

        // ระบุผู้แก้ไขล่าสุด
        $data['updated_by'] = Auth::id();

        // ตรวจสอบสิทธิ์การแก้ไข HN (เฉพาะ Admin เท่านั้น)
        if ($request->has('hn')) {
            $this->authorize('editHn', $patient);
        }

        // อัปเดตข้อมูลผู้ป่วย
        $patient->update($data);

        // กลับไปหน้ารายชื่อ พร้อมข้อความสำเร็จ
        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.updated_success'));
    }

    /**
     * ลบข้อมูลผู้ป่วย (Soft Delete)
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        // ทำ Soft Delete
        $patient->delete();

        // กลับไปหน้ารายชื่อ พร้อมข้อความสำเร็จ
        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.deleted_success'));
    }
}
