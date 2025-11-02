<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.show.title') }}
        </h2>
    </x-slot>

    {{-- ส่วนเนื้อหา --}}
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @can('view', $patient)
                @php($empty = __('patients.empty') === 'patients.empty' ? '-' : __('patients.empty'))

                <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-6">

                    {{-- สารบัญย่อ --}}
                    <div class="border rounded-md p-4 bg-gray-50">
                        <h3 class="text-base font-semibold text-gray-800 mb-2">{{ __('patients.show.toc') }}</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 grid sm:grid-cols-2 gap-y-1">
                            <li><a class="hover:underline" href="#identity">{{ __('patients.section.identity') }}</a></li>
                            <li><a class="hover:underline" href="#demographic">{{ __('patients.section.demographic') }}</a>
                            </li>
                            <li><a class="hover:underline" href="#blood">{{ __('patients.section.blood') }}</a></li>
                            <li><a class="hover:underline"
                                    href="#address_contact">{{ __('patients.section.address_contact') }}</a></li>
                            <li><a class="hover:underline"
                                    href="#emergency_contact">{{ __('patients.section.emergency_contact') }}</a></li>
                            <li><a class="hover:underline" href="#insurance">{{ __('patients.section.insurance') }}</a></li>
                            <li><a class="hover:underline" href="#consent">{{ __('patients.section.consent') }}</a></li>
                            <li><a class="hover:underline" href="#notes">{{ __('patients.section.notes_general') }}</a>
                            </li>
                        </ul>
                    </div>

                    {{-- กลุ่ม: ข้อมูลระบุตัวตนพื้นฐาน --}}
                    <section id="identity" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.identity') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.hn') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->hn }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.cid') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->cid ?: $empty }}</div>
                            </div>
                            <div class="flex sm:col-span-2">
                                <div class="w-40 text-gray-600">{{ __('patients.full_name') }}</div>
                                <div class="font-medium text-gray-900">
                                    {{ trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')) ?: $empty }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: ข้อมูลประชากร --}}
                    <section id="demographic" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.demographic') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.dob') }}</div>
                                <div class="font-medium text-gray-900">
                                    {{ $patient->dob ? $patient->dob->format('d/m/Y') : $empty }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.sex') }}</div>
                                <div class="font-medium text-gray-900">
                                    {{ $patient->sex ? __('patients.' . $patient->sex) : $empty }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: หมู่เลือด & Rh --}}
                    <section id="blood" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.blood') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.blood_group') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->blood_group ?: $empty }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.rh_factor') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->rh_factor ?: $empty }}</div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: ที่อยู่ & การติดต่อ --}}
                    <section id="address_contact" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.address_contact') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.phone') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->phone ?: $empty }}</div>
                            </div>
                            <div class="flex sm:col-span-2">
                                <div class="w-40 text-gray-600">{{ __('patients.address_full') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->address_full ?: $empty }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.address_short') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->address_short ?: $empty }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.postal_code') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->postal_code ?: $empty }}</div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: ผู้ติดต่อฉุกเฉิน --}}
                    <section id="emergency_contact" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.emergency_contact') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.emergency_contact_name') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->emergency_contact_name ?: $empty }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.emergency_contact_relation') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->emergency_contact_relation ?: $empty }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.emergency_contact_phone') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->emergency_contact_phone ?: $empty }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: สิทธิ์ประกัน --}}
                    <section id="insurance" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.insurance') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.insurance_scheme') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->insurance_scheme ?: $empty }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.insurance_no') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->insurance_no ?: $empty }}</div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: ความยินยอม --}}
                    <section id="consent" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.consent') }}</h3>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex">
                                <div class="w-40 text-gray-600">{{ __('patients.consent_at') }}</div>
                                <div class="font-medium text-gray-900">
                                    {{ $patient->consent_at ? $patient->consent_at->format('d/m/Y H:i') : $empty }}
                                </div>
                            </div>
                            <div class="flex sm:col-span-2">
                                <div class="w-40 text-gray-600">{{ __('patients.consent_note') }}</div>
                                <div class="font-medium text-gray-900">{{ $patient->consent_note ?: $empty }}</div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    {{-- กลุ่ม: หมายเหตุทั่วไป --}}
                    <section id="notes" class="scroll-mt-24">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('patients.section.notes_general') }}</h3>
                        <div class="mt-3">
                            <p class="text-gray-900 whitespace-pre-line">
                                {{ $patient->note_general ?: $empty }}
                            </p>
                        </div>
                    </section>

                    {{-- ปุ่มกลับ / แก้ไข / ลบ --}}
                    <div class="flex justify-between sm:justify-end sm:space-x-2 pt-2">
                        <a href="{{ route('patients.index') }}"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-gray-900">
                            {{ __('patients.actions.back') }}
                        </a>

                        @can('update', $patient)
                            <a href="{{ route('patients.edit', $patient->id) }}"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                {{ __('patients.actions.edit') }}
                            </a>
                        @endcan

                        @can('delete', $patient)
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('{{ __('patients.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    {{ __('patients.actions.delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
