{{-- resources/views/nurse_notes/form.blade.php --}}

{{-- ใช้ Policy-only คุมการเข้าถึงฟอร์มตั้งแต่ชั้น View --}}
@if ($mode === 'edit')
    @cannot('update', $note)
        @php return; @endphp
    @endcannot
@else
    @cannot('create', \App\Models\NurseNote::class)
        @php return; @endphp
    @endcannot
@endif

<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $mode === 'edit' ? __('nurse_notes.edit.title') : __('nurse_notes.create.title') }}
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
                    // ควบคุมสถานะ + lock logic
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
                    // ถ้า lock แล้ว → read-only (disable ทุก input)
                    $readOnly = $mode === 'edit' && $isLocked;
                    $fmt = fn($dt) => $dt ? $dt->format('Y-m-d\TH:i') : '';
                @endphp

                {{-- ฟอร์มหลัก --}}
                <form
                    @if ($mode === 'edit') action="{{ route('patients.nurse-notes.update', [$patient, $note]) }}"
                    @else
                        action="{{ route('patients.nurse-notes.store', $patient) }}" @endif
                    method="POST" class="space-y-6" novalidate>
                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif

                    {{-- การประเมินทางการพยาบาล --}}
                    <div>
                        <label for="nursing_assessment" class="block text-sm font-medium text-gray-700">
                            {{ __('nurse_notes.nursing_assessment') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="nursing_assessment" name="nursing_assessment" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('nursing_assessment', $note->nursing_assessment) }}</textarea>
                    </div>

                    {{-- สรุปสัญญาณชีพ --}}
                    <div>
                        <label for="vital_signs_summary" class="block text-sm font-medium text-gray-700">
                            {{ __('nurse_notes.vital_signs_summary') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="vital_signs_summary" name="vital_signs_summary" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            @disabled($readOnly)>{{ old('vital_signs_summary', $note->vital_signs_summary) }}</textarea>
                    </div>

                    {{-- การวินิจฉัยทางการพยาบาล / แผนการพยาบาล --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nursing_diagnosis" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.nursing_diagnosis') }}
                            </label>
                            <textarea id="nursing_diagnosis" name="nursing_diagnosis" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('nursing_diagnosis', $note->nursing_diagnosis) }}</textarea>
                        </div>
                        <div>
                            <label for="nursing_care_plan" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.nursing_care_plan') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="nursing_care_plan" name="nursing_care_plan" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('nursing_care_plan', $note->nursing_care_plan) }}</textarea>
                        </div>
                    </div>

                    {{-- กิจกรรมการพยาบาล / บันทึกความก้าวหน้า --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="interventions_summary" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.interventions_summary') }}
                            </label>
                            <textarea id="interventions_summary" name="interventions_summary" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('interventions_summary', $note->interventions_summary) }}</textarea>
                        </div>
                        <div>
                            <label for="progress_note" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.progress_note') }}
                            </label>
                            <textarea id="progress_note" name="progress_note" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('progress_note', $note->progress_note) }}</textarea>
                        </div>
                    </div>

                    {{-- การให้ความรู้/ความปลอดภัย และบันทึกลายเซ็น --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="education_or_safety_note" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.education_or_safety_note') }}
                            </label>
                            <textarea id="education_or_safety_note" name="education_or_safety_note" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('education_or_safety_note', $note->education_or_safety_note) }}</textarea>
                        </div>
                        <div>
                            <label for="sign_note" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.sign_note') }}
                            </label>
                            <textarea id="sign_note" name="sign_note" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>{{ old('sign_note', $note->sign_note) }}</textarea>
                        </div>
                    </div>

                    {{-- วันนัดหมาย / เวลาบันทึก --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="scheduled_for" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.scheduled_for') }}
                            </label>
                            <input type="datetime-local" id="scheduled_for" name="scheduled_for"
                                value="{{ old('scheduled_for', $fmt($note->scheduled_for ?? null)) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                        </div>
                        <div>
                            <label for="recorded_at" class="block text-sm font-medium text-gray-700">
                                {{ __('nurse_notes.recorded_at') }} <span class="text-red-500">*</span>
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
                            {{ __('nurse_notes.status') }} <span class="text-red-500">*</span>
                        </label>

                        @if ($readOnly)
                            {{-- ปิดเคสแล้ว: แสดงสถานะปัจจุบันแบบอ่านอย่างเดียว --}}
                            <input type="text" readonly value="{{ __('nurse_notes.status.' . $current) }}"
                                class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 text-gray-700 shadow-sm">
                        @else
                            <select id="status" name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                @disabled($readOnly)>
                                @foreach ($availableStatuses as $s)
                                    <option value="{{ $s }}" @selected($current === $s)>
                                        {{ __('nurse_notes.status.' . $s) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- ปุ่มควบคุม --}}
                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('patients.nurse-notes.index', $patient) }}"
                            class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">
                            {{ __('Back') }}
                        </a>

                        <div class="flex gap-2">
                            @php
                                // ปิดเคสแล้ว → ซ่อนปุ่มบันทึก
                                $canSubmit = !($mode === 'edit' && $readOnly);
                            @endphp

                            @if ($canSubmit)
                                @if ($mode === 'edit')
                                    @can('update', $note)
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            {{ __('nurse_notes.actions.update') }}
                                        </button>
                                    @endcan
                                @else
                                    @can('create', \App\Models\NurseNote::class)
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            {{ __('nurse_notes.actions.save') }}
                                        </button>
                                    @endcan
                                @endif
                            @else
                                {{-- บันทึกถูกปิดเคสแล้ว --}}
                                <span class="text-sm text-gray-600">{{ __('nurse_notes.locked') }}</span>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
