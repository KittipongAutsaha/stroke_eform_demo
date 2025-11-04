{{-- resources/views/doctor_notes/show.blade.php --}}
<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('doctor_notes.show.title') }}
        </h2>
    </x-slot>

    {{-- ส่วนเนื้อหา --}}
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @can('view', $note)
                {{-- Flash message --}}
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-6">
                    {{-- หัวข้อ + สถานะ --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                🩺 {{ __('doctor_notes.title') }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ __('doctor_notes.patient') }}:
                                <span class="font-medium text-gray-800">
                                    {{ trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')) ?: $patient->hn }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ __('doctor_notes.doctor') }}:
                                <span class="font-medium text-gray-800">{{ $note->doctor->name ?? '-' }}</span>
                            </p>
                        </div>

                        <div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                @if ($note->status === 'planned') bg-yellow-100 text-yellow-800
                                @elseif ($note->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif ($note->status === 'signed_off') bg-green-100 text-green-800
                                @elseif ($note->status === 'cancelled') bg-gray-200 text-gray-600 @endif">
                                {{ __('doctor_notes.status.' . $note->status) }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    {{-- เวลาเกี่ยวข้อง --}}
                    @php
                        $fmt = fn($dt) => $dt ? $dt->format('Y-m-d H:i') : '-';
                    @endphp
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.recorded_at') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->recorded_at ?? null) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.signed_off_at') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->signed_off_at ?? null) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.scheduled_for') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->scheduled_for ?? null) }}</div>
                        </div>
                    </div>

                    {{-- เนื้อหาแพทย์บันทึก --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.chief_complaint') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->chief_complaint ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.diagnosis') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->diagnosis ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.differential_diagnosis') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->differential_diagnosis ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.clinical_summary') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->clinical_summary ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.physical_exam') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->physical_exam ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.imaging_summary') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->imaging_summary ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- คะแนน/ธงทางคลินิก --}}
                    <div class="grid sm:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.nihss_score') }}</div>
                            <p class="mt-1 text-gray-900">{{ $note->nihss_score ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.gcs_score') }}</div>
                            <p class="mt-1 text-gray-900">{{ $note->gcs_score ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.lvo_suspected') }}</div>
                            <p class="mt-1 text-gray-900">
                                {{ (int) ($note->lvo_suspected ?? 0) === 1 ? __('Yes') : __('No') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.treatment_plan') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->treatment_plan ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('doctor_notes.orders') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->orders ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-600">{{ __('doctor_notes.prescription_note') }}</div>
                        <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->prescription_note ?? '-' }}</p>
                    </div>

                    {{-- ปุ่มนำทาง --}}
                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('patients.doctor-notes.index', $patient) }}"
                            class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">
                            {{ __('Back') }}
                        </a>

                        <div class="flex gap-2">
                            @if (!$note->isLocked() && Gate::check('update', $note))
                                <a href="{{ route('patients.doctor-notes.edit', [$patient, $note]) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    {{ __('doctor_notes.actions.edit') }}
                                </a>
                            @endif

                            @if (!$note->isLocked() && Gate::check('delete', $note))
                                <form method="POST"
                                    action="{{ route('patients.doctor-notes.destroy', [$patient, $note]) }}"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this note?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                        {{ __('doctor_notes.actions.delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
