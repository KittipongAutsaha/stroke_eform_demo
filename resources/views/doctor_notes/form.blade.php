{{-- resources/views/doctor_notes/form.blade.php --}}

{{-- ใช้ Policy-only คุมการเข้าถึงฟอร์มตั้งแต่ชั้น View --}}
@if ($mode === 'edit')
    @cannot('update', $note)
        @php return; @endphp
    @endcannot
@else
    @cannot('create', \App\Models\DoctorNote::class)
        @php return; @endphp
    @endcannot
@endif

<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $mode === 'edit' ? __('doctor_notes.edit.title') : __('doctor_notes.create.title') }}
        </h2>
    </x-slot>

    {{-- ส่วนเนื้อหา --}}
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                {{-- Flash message --}}
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 rounded-md">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    // สถานะและ lock logic ของฟอร์ม
                    // - create: ให้เลือก planned / in_progress
                    // - edit: จำกัด transition และถ้าเป็น signed_off/cancelled → lock ทั้งฟอร์ม
                    $current = old('status', $note->status);
                    if ($mode === 'create') {
                        $availableStatuses = ['planned', 'in_progress'];
                        $isLocked = false;
                    } else {
                        if ($current === 'in_progress') {
                            $availableStatuses = ['in_progress', 'signed_off'];
                        } elseif ($current === 'planned') {
                            $availableStatuses = ['planned', 'in_progress', 'cancelled'];
                        } else {
                            // signed_off / cancelled → ปิดเคสแล้ว
                            $availableStatuses = [$current];
                        }
                        $isLocked = in_array($current, ['signed_off', 'cancelled'], true);
                    }
                    // ถ้า lock แล้วให้ทำฟอร์ม read-only (disable ทุก input)
                    $readOnly = $mode === 'edit' && $isLocked;
                    $fmt = fn($dt) => $dt ? $dt->format('Y-m-d\TH:i') : '';
                @endphp

                {{-- ฟอร์มหลัก --}}
                <form
                    @if ($mode === 'edit') action="{{ route('patients.doctor-notes.update', [$patient, $note]) }}"
                    @else
                        action="{{ route('patients.doctor-notes.store', $patient) }}" @endif
                    method="POST" class="space-y-6" novalidate>
                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif

                    {{-- อาการสำคัญ --}}
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700">
                            {{ __('doctor_notes.chief_complaint') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="chief_complaint" name="chief_complaint" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('chief_complaint', $note->chief_complaint) }}</textarea>
                    </div>

                    {{-- การวินิจฉัย --}}
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700">
                            {{ __('doctor_notes.diagnosis') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="diagnosis" name="diagnosis" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('diagnosis', $note->diagnosis) }}</textarea>
                    </div>

                    {{-- การวินิจฉัยแยกโรค --}}
                    <div>
                        <label for="differential_diagnosis" class="block text-sm font-medium text-gray-700">
                            {{ __('doctor_notes.differential_diagnosis') }}
                        </label>
                        <textarea id="differential_diagnosis" name="differential_diagnosis" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('differential_diagnosis', $note->differential_diagnosis) }}</textarea>
                    </div>

                    {{-- สรุปทางคลินิก / ตรวจร่างกาย --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="clinical_summary" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.clinical_summary') }}
                            </label>
                            <textarea id="clinical_summary" name="clinical_summary" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('clinical_summary', $note->clinical_summary) }}</textarea>
                        </div>
                        <div>
                            <label for="physical_exam" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.physical_exam') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="physical_exam" name="physical_exam" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('physical_exam', $note->physical_exam) }}</textarea>
                        </div>
                    </div>

                    {{-- คะแนน NIHSS / GCS --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nihss_score" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.nihss_score') }}
                            </label>
                            <input type="number" id="nihss_score" name="nihss_score"
                                value="{{ old('nihss_score', $note->nihss_score) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                        </div>
                        <div>
                            <label for="gcs_score" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.gcs_score') }}
                            </label>
                            <input type="number" id="gcs_score" name="gcs_score"
                                value="{{ old('gcs_score', $note->gcs_score) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                        </div>
                    </div>

                    {{-- สรุปผลภาพถ่าย / สงสัย LVO --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="imaging_summary" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.imaging_summary') }}
                            </label>
                            <textarea id="imaging_summary" name="imaging_summary" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('imaging_summary', $note->imaging_summary) }}</textarea>
                        </div>
                        <div class="flex items-center gap-3 mt-6 sm:mt-0">
                            <input type="hidden" name="lvo_suspected" value="0" @disabled($readOnly)>
                            <input id="lvo_suspected" name="lvo_suspected" type="checkbox" value="1"
                                @checked(old('lvo_suspected', $note->lvo_suspected))
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                @disabled($readOnly)>
                            <label for="lvo_suspected" class="text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.lvo_suspected') }}
                            </label>
                        </div>
                    </div>

                    {{-- แผนการรักษา / คำสั่งการรักษา --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="treatment_plan" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.treatment_plan') }}
                            </label>
                            <textarea id="treatment_plan" name="treatment_plan" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('treatment_plan', $note->treatment_plan) }}</textarea>
                        </div>
                        <div>
                            <label for="orders" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.orders') }}
                            </label>
                            <textarea id="orders" name="orders" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('orders', $note->orders) }}</textarea>
                        </div>
                    </div>

                    {{-- บันทึกจ่ายยา --}}
                    <div>
                        <label for="prescription_note" class="block text-sm font-medium text-gray-700">
                            {{ __('doctor_notes.prescription_note') }}
                        </label>
                        <textarea id="prescription_note" name="prescription_note" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('prescription_note', $note->prescription_note) }}</textarea>
                    </div>

                    {{-- วันนัดหมาย / เวลาบันทึก --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="scheduled_for" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.scheduled_for') }}
                            </label>
                            <input type="datetime-local" id="scheduled_for" name="scheduled_for"
                                value="{{ old('scheduled_for', $fmt($note->scheduled_for ?? null)) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                        </div>
                        <div>
                            <label for="recorded_at" class="block text-sm font-medium text-gray-700">
                                {{ __('doctor_notes.recorded_at') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="recorded_at" name="recorded_at"
                                value="{{ old('recorded_at', $fmt($note->recorded_at ?? now())) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                        </div>
                    </div>

                    {{-- สถานะ --}}
                    <div class="sm:w-72">
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            {{ __('doctor_notes.status') }} <span class="text-red-500">*</span>
                        </label>

                        @if ($readOnly)
                            {{-- ปิดเคสแล้ว: แสดงสถานะปัจจุบันแบบอ่านอย่างเดียว --}}
                            <input type="text" readonly value="{{ __('doctor_notes.status.' . $current) }}"
                                class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 text-gray-700 shadow-sm">
                        @else
                            <select id="status" name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                                @foreach ($availableStatuses as $s)
                                    <option value="{{ $s }}" @selected($current === $s)>
                                        {{ __('doctor_notes.status.' . $s) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- ปุ่มควบคุม --}}
                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('patients.doctor-notes.index', $patient) }}"
                            class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">
                            {{ __('Back') }}
                        </a>

                        <div class="flex gap-2">
                            @php
                                // ถ้าปิดเคสแล้วให้ซ่อนปุ่มบันทึก (read-only)
                                $canSubmit = !($mode === 'edit' && $readOnly);
                            @endphp

                            @if ($canSubmit)
                                @if ($mode === 'edit')
                                    @can('update', $note)
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            {{ __('doctor_notes.actions.update') }}
                                        </button>
                                    @endcan
                                @else
                                    @can('create', \App\Models\DoctorNote::class)
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            {{ __('doctor_notes.actions.save') }}
                                        </button>
                                    @endcan
                                @endif
                            @else
                                {{-- บันทึกถูกปิดเคสแล้ว --}}
                                <span class="text-sm text-gray-600">{{ __('doctor_notes.locked') }}</span>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
