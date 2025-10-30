<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.edit') }}
        </h2>
    </x-slot>

    {{-- เนื้อหาหลัก --}}
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- แสดงข้อความ Error หากมี --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ฟอร์มแก้ไขข้อมูลผู้ป่วย --}}
            @can('update', $patient)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form action="{{ route('patients.update', $patient->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- HN --}}
                        <div class="mb-4">
                            <label for="hn" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.hn') }}
                            </label>

                            {{-- ใช้ Policy: admin เท่านั้นที่แก้ HN ได้ --}}
                            <input type="text" name="hn" id="hn" value="{{ old('hn', $patient->hn) }}"
                                placeholder="HN-1234567"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm {{ auth()->user()->cannot('editHn', $patient) ? 'bg-gray-100' : '' }}"
                                @cannot('editHn', $patient) readonly @endcannot required>
                            @cannot('editHn', $patient)
                                <p class="text-sm text-gray-500 mt-1">({{ __('patients.hn_locked') }})</p>
                            @endcannot
                        </div>

                        {{-- CID --}}
                        <div class="mb-4">
                            <label for="cid" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.cid') }}
                            </label>
                            <input type="text" name="cid" id="cid" inputmode="numeric" pattern="\d{13}"
                                maxlength="13" value="{{ old('cid', $patient->cid) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="กรอกเลขบัตรประชาชน 13 หลัก">
                            @error('cid')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ชื่อ --}}
                        <div class="mb-4">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.first_name') }}
                            </label>
                            <input type="text" name="first_name" id="first_name"
                                value="{{ old('first_name', $patient->first_name) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('first_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- นามสกุล --}}
                        <div class="mb-4">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.last_name') }}
                            </label>
                            <input type="text" name="last_name" id="last_name"
                                value="{{ old('last_name', $patient->last_name) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('last_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- วันเกิด --}}
                        <div class="mb-4">
                            <label for="dob" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.dob') }}
                            </label>
                            <input type="date" name="dob" id="dob"
                                value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('dob')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- เพศ --}}
                        <div class="mb-4">
                            <label for="sex" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.sex') }}
                            </label>
                            <select name="sex" id="sex"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">{{ __('patients.select_sex') }}</option>
                                <option value="male" {{ old('sex', $patient->sex) == 'male' ? 'selected' : '' }}>ชาย
                                </option>
                                <option value="female" {{ old('sex', $patient->sex) == 'female' ? 'selected' : '' }}>หญิง
                                </option>
                                <option value="other" {{ old('sex', $patient->sex) == 'other' ? 'selected' : '' }}>อื่น ๆ
                                </option>
                                <option value="unknown" {{ old('sex', $patient->sex) == 'unknown' ? 'selected' : '' }}>
                                    ไม่ระบุ</option>
                            </select>
                            @error('sex')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ที่อยู่ --}}
                        <div class="mb-4">
                            <label for="address_short" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.address') }}
                            </label>
                            <textarea name="address_short" id="address_short" rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address_short', $patient->address_short) }}</textarea>
                            @error('address_short')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- หมายเหตุ --}}
                        <div class="mb-4">
                            <label for="note_general" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.note_general') }}
                            </label>
                            <textarea name="note_general" id="note_general" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note_general', $patient->note_general) }}</textarea>
                            @error('note_general')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
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
