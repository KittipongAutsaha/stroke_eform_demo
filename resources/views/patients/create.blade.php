<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.add_new') }}
        </h2>
    </x-slot>

    {{-- เนื้อหาหลัก --}}
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- แสดงข้อความ error (จาก Laravel FormRequest เท่านั้น) --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @can('create', \App\Models\Patient::class)
                {{-- ปิด HTML5 validation เพื่อไม่ให้ชนกับ Laravel --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form action="{{ route('patients.store') }}" method="POST" novalidate>
                        @csrf

                        {{-- ===== กลุ่ม: ข้อมูลระบุตัวตนพื้นฐาน ===== --}}
                        {{-- HN --}}
                        <div class="mb-4">
                            <label for="hn" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.hn') }}
                            </label>
                            <input type="text" name="hn" id="hn" value="{{ old('hn') }}"
                                placeholder="HN-1234567" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('hn')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CID --}}
                        <div class="mb-4">
                            <label for="cid" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.cid') }}
                            </label>
                            <input type="text" name="cid" id="cid" value="{{ old('cid') }}"
                                inputmode="numeric" maxlength="13"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="กรอกเลขบัตรประชาชน 13 หลัก">
                            @error('cid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ชื่อ / นามสกุล --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.first_name') }}
                                </label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.last_name') }}
                                </label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- วันเกิด / เพศ --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="dob" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.dob') }}
                                </label>
                                <input type="date" name="dob" id="dob" value="{{ old('dob') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('dob')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.sex') }}
                                </label>
                                <select name="sex" id="sex"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('patients.select_sex') }}</option>
                                    <option value="male" {{ old('sex') === 'male' ? 'selected' : '' }}>
                                        {{ __('patients.male') }}</option>
                                    <option value="female" {{ old('sex') === 'female' ? 'selected' : '' }}>
                                        {{ __('patients.female') }}</option>
                                    <option value="other" {{ old('sex') === 'other' ? 'selected' : '' }}>
                                        {{ __('patients.other') }}</option>
                                    <option value="unknown" {{ old('sex') === 'unknown' ? 'selected' : '' }}>
                                        {{ __('patients.unknown') }}</option>
                                </select>
                                @error('sex')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== กลุ่ม: ข้อมูลสุขภาพ ===== --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="blood_group" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.blood_group') }}
                                </label>
                                <select name="blood_group" id="blood_group"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('patients.select_blood') }}</option>
                                    @foreach (['A', 'B', 'AB', 'O'] as $group)
                                        <option value="{{ $group }}"
                                            {{ old('blood_group') === $group ? 'selected' : '' }}>{{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blood_group')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="rh_factor" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.rh_factor') }}
                                </label>
                                <select name="rh_factor" id="rh_factor"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('patients.select_rh') }}</option>
                                    <option value="+" {{ old('rh_factor') === '+' ? 'selected' : '' }}>+</option>
                                    <option value="-" {{ old('rh_factor') === '-' ? 'selected' : '' }}>-</option>
                                </select>
                                @error('rh_factor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== กลุ่ม: การติดต่อและที่อยู่ ===== --}}
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.phone') }}
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="address_full" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.address_full') }}
                                </label>
                                <textarea name="address_full" id="address_full" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address_full') }}</textarea>
                                @error('address_full')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.postal_code') }}
                                </label>
                                <input type="text" name="postal_code" id="postal_code"
                                    value="{{ old('postal_code') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== กลุ่ม: ผู้ติดต่อฉุกเฉิน ===== --}}
                        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">
                            {{ __('patients.emergency_contact') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.emergency_contact_name') }}
                                </label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                    value="{{ old('emergency_contact_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.emergency_contact_relation') }}
                                </label>
                                <input type="text" name="emergency_contact_relation" id="emergency_contact_relation"
                                    value="{{ old('emergency_contact_relation') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('emergency_contact_relation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.emergency_contact_phone') }}
                                </label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone"
                                    value="{{ old('emergency_contact_phone') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('emergency_contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== กลุ่ม: สิทธิ์ประกัน ===== --}}
                        <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">
                            {{ __('patients.insurance') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="insurance_scheme" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.insurance_scheme') }}
                                </label>
                                <input type="text" name="insurance_scheme" id="insurance_scheme"
                                    value="{{ old('insurance_scheme') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('insurance_scheme')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="insurance_no" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.insurance_no') }}
                                </label>
                                <input type="text" name="insurance_no" id="insurance_no"
                                    value="{{ old('insurance_no') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('insurance_no')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===== กลุ่ม: ความยินยอม ===== --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="consent_at" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.consent_at') }}
                                </label>
                                <input type="datetime-local" name="consent_at" id="consent_at"
                                    value="{{ old('consent_at') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('consent_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="consent_note" class="block text-sm font-medium text-gray-700">
                                    {{ __('patients.consent_note') }}
                                </label>
                                <textarea name="consent_note" id="consent_note" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('consent_note') }}</textarea>
                                @error('consent_note')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- หมายเหตุทั่วไป --}}
                        <div class="mb-6">
                            <label for="note_general" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.note_general') }}
                            </label>
                            <textarea name="note_general" id="note_general" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note_general') }}</textarea>
                            @error('note_general')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ปุ่ม --}}
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('patients.index') }}"
                                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
