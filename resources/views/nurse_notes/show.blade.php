{{-- resources/views/nurse_notes/show.blade.php --}}
<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('nurse_notes.show.title') }}
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
                                💉 {{ __('nurse_notes.title') }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ __('nurse_notes.patient') }}:
                                <span class="font-medium text-gray-800">
                                    {{ trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')) ?: $patient->hn }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ __('nurse_notes.nurse') }}:
                                <span class="font-medium text-gray-800">{{ $note->nurse->name ?? '-' }}</span>
                            </p>
                        </div>

                        <div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                @if ($note->status === 'planned') bg-yellow-100 text-yellow-800
                                @elseif ($note->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif ($note->status === 'signed_off') bg-green-100 text-green-800
                                @elseif ($note->status === 'cancelled') bg-gray-200 text-gray-600 @endif">
                                {{ __('nurse_notes.status.' . $note->status) }}
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
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.recorded_at') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->recorded_at ?? null) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.signed_off_at') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->signed_off_at ?? null) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.scheduled_for') }}</div>
                            <div class="font-medium text-gray-900">{{ $fmt($note->scheduled_for ?? null) }}</div>
                        </div>
                    </div>

                    {{-- เนื้อหาที่พยาบาลบันทึก --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.nursing_assessment') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->nursing_assessment ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.vital_signs_summary') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->vital_signs_summary ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.nursing_diagnosis') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->nursing_diagnosis ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.nursing_care_plan') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->nursing_care_plan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.interventions_summary') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->interventions_summary ?? '-' }}</p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.progress_note') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->progress_note ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.education_or_safety_note') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->education_or_safety_note ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('nurse_notes.sign_note') }}</div>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $note->sign_note ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- ปุ่มนำทาง --}}
                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('patients.nurse-notes.index', $patient) }}"
                            class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">
                            {{ __('Back') }}
                        </a>

                        <div class="flex gap-2">
                            @if (!$note->isLocked() && Gate::check('update', $note))
                                <a href="{{ route('patients.nurse-notes.edit', [$patient, $note]) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    {{ __('nurse_notes.actions.edit') }}
                                </a>
                            @endif

                            @if (!$note->isLocked() && Gate::check('delete', $note))
                                <form method="POST"
                                    action="{{ route('patients.nurse-notes.destroy', [$patient, $note]) }}"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this note?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                        {{ __('nurse_notes.actions.delete') }}
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
