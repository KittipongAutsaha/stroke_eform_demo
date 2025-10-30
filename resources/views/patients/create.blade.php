<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.add_new') }}
        </h2>
    </x-slot>

    {{-- ส่วนเนื้อหา --}}
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- กล่องแสดงข้อความ error --}}
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
                {{-- ฟอร์มเพิ่มข้อมูลผู้ป่วย --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form action="{{ route('patients.store') }}" method="POST">
                        @csrf

                        {{-- HN --}}
                        <div class="mb-4">
                            <label for="hn" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.hn') }}
                            </label>
                            <input type="text" name="hn" id="hn" value="{{ old('hn') }}"
                                placeholder="HN-1234567" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                            @error('hn')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CID (เลขบัตรประชาชน 13 หลัก) --}}
                        <div class="mb-4">
                            <label for="cid" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.cid') }}
                            </label>
                            <input type="text" name="cid" id="cid" value="{{ old('cid') }}"
                                inputmode="numeric" pattern="\d{13}" maxlength="13"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('cid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ชื่อ --}}
                        <div class="mb-4">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.first_name') }}
                            </label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- นามสกุล --}}
                        <div class="mb-4">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.last_name') }}
                            </label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- วันเกิด --}}
                        <div class="mb-4">
                            <label for="dob" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.dob') }}
                            </label>
                            <input type="date" name="dob" id="dob" value="{{ old('dob') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('dob')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                <option value="male" {{ old('sex') === 'male' ? 'selected' : '' }}>ชาย</option>
                                <option value="female" {{ old('sex') === 'female' ? 'selected' : '' }}>หญิง</option>
                                <option value="other" {{ old('sex') === 'other' ? 'selected' : '' }}>อื่น ๆ</option>
                                <option value="unknown" {{ old('sex') === 'unknown' ? 'selected' : '' }}>ไม่ระบุ</option>
                            </select>
                            @error('sex')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ที่อยู่ --}}
                        <div class="mb-4">
                            <label for="address_short" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.address') }}
                            </label>
                            <textarea name="address_short" id="address_short" rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address_short') }}</textarea>
                            @error('address_short')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- หมายเหตุทั่วไป --}}
                        <div class="mb-4">
                            <label for="note_general" class="block text-sm font-medium text-gray-700">
                                {{ __('patients.note_general') }}
                            </label>
                            <textarea name="note_general" id="note_general" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note_general') }}</textarea>
                            @error('note_general')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ปุ่มบันทึก / ยกเลิก --}}
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
