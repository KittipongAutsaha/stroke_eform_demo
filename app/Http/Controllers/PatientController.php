<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Requests\PatientStoreRequest;
use App\Http\Requests\PatientUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * แสดงรายชื่อผู้ป่วยทั้งหมด
     */
    public function index(): View
    {
        // แสดงรายการผู้ป่วยทั้งหมด (เรียงจากใหม่ไปเก่า)
        $patients = Patient::latest()->paginate(20);

        return view('patients.index', compact('patients'));
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
