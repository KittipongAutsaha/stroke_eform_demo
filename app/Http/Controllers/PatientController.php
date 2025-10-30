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
        $patients = Patient::latest()->paginate(20);

        return view('patients.index', compact('patients'));
    }

    /**
     * แสดงฟอร์มเพิ่มผู้ป่วย
     */
    public function create(): View
    {
        return view('patients.create');
    }

    /**
     * บันทึกข้อมูลผู้ป่วยใหม่
     */
    public function store(PatientStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Patient::create($data);

        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.created_success'));
    }

    /**
     * แสดงรายละเอียดผู้ป่วย
     */
    public function show(Patient $patient): View
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * แสดงฟอร์มแก้ไขผู้ป่วย
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
        $data = $request->validated();
        $data['updated_by'] = Auth::id();

        // ตรวจสิทธิ์เฉพาะกรณีมีการแก้ฟิลด์ HN (เฉพาะ admin เท่านั้น)
        if ($request->has('hn')) {
            $this->authorize('editHn', $patient);
        }

        $patient->update($data);

        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.updated_success'));
    }

    /**
     * ลบข้อมูลผู้ป่วย (Soft Delete)
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', __('patients.deleted_success'));
    }
}
