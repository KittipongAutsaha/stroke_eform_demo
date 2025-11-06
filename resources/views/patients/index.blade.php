<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.title') }}
        </h2>
    </x-slot>

    {{-- ส่วนเนื้อหา --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- แถบค้นหา: รับ q ผ่าน query string แล้วส่งกลับมาที่หน้านี้ --}}
            <div class="mb-4">
                <form action="{{ route('patients.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="q" value="{{ $q ?? '' }}"
                        placeholder="{{ __('Search patient by HN / Name / CID / Phone') }}"
                        class="w-full sm:w-2/3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ __('Search') }}
                    </button>
                </form>

                {{-- แสดงหัวข้อผลการค้นหาเมื่อมี q --}}
                @if (isset($q) && $q !== '')
                    <p class="mt-2 text-sm text-gray-600">ผลการค้นหา: <span
                            class="font-medium">{{ $q }}</span></p>
                @endif
            </div>

            {{-- แสดงข้อความสำเร็จ (เพิ่ม/แก้ไข/ลบ) / คำเตือนเมื่อไม่พบผลลัพธ์ --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                    {{ session('warning') }}
                </div>
            @endif

            {{-- ปุ่มเพิ่มข้อมูลผู้ป่วย (เฉพาะผู้มีสิทธิ์) --}}
            @can('create', \App\Models\Patient::class)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('patients.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ __('patients.add_new') }}
                    </a>
                </div>
            @endcan

            {{-- ตารางแสดงข้อมูลผู้ป่วย (เลื่อนแนวนอนได้) --}}
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-[1200px] table-auto border border-gray-200">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.hn') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.cid') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.name') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.dob') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.sex') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.phone') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">
                                {{ __('patients.blood_group') }}/{{ __('patients.rh_factor') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.insurance_scheme') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.consent_at') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.address') }}</th>
                            <th class="px-3 py-2 border whitespace-nowrap">{{ __('patients.note_general') }}</th>
                            <th class="px-3 py-2 border text-center whitespace-nowrap w-40">
                                {{ __('patients.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($patients as $patient)
                            <tr class="hover:bg-gray-50">
                                {{-- HN --}}
                                <td class="px-3 py-2 border align-top">{{ $patient->hn }}</td>

                                {{-- CID --}}
                                <td class="px-3 py-2 border align-top">{{ $patient->cid ?: '-' }}</td>

                                {{-- ชื่อ-นามสกุล --}}
                                <td class="px-3 py-2 border align-top whitespace-nowrap">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </td>

                                {{-- วันเกิด --}}
                                <td class="px-3 py-2 border align-top">
                                    {{ $patient->dob ? $patient->dob->format('d/m/Y') : '-' }}
                                </td>

                                {{-- เพศ (ใช้ label แปลจาก th.json) --}}
                                <td class="px-3 py-2 border align-top">
                                    {{ $patient->sex ? __('patients.' . $patient->sex) : '-' }}
                                </td>

                                {{-- เบอร์โทร --}}
                                <td class="px-3 py-2 border align-top whitespace-nowrap">
                                    {{ $patient->phone ?: '-' }}
                                </td>

                                {{-- หมู่เลือด / Rh --}}
                                <td class="px-3 py-2 border align-top whitespace-nowrap">
                                    {{ $patient->blood_group ?: '-' }}{{ $patient->rh_factor ? $patient->rh_factor : '' }}
                                </td>

                                {{-- สิทธิ์ประกัน --}}
                                <td class="px-3 py-2 border align-top">
                                    <span class="inline-block max-w-[220px] truncate"
                                        title="{{ $patient->insurance_scheme }}">
                                        {{ $patient->insurance_scheme ?: '-' }}
                                    </span>
                                </td>

                                {{-- วันที่ให้ความยินยอม --}}
                                <td class="px-3 py-2 border align-top whitespace-nowrap">
                                    {{ $patient->consent_at ? $patient->consent_at->format('d/m/Y H:i') : '-' }}
                                </td>

                                {{-- ที่อยู่ย่อ --}}
                                <td class="px-3 py-2 border align-top">
                                    <span class="inline-block max-w-[240px] truncate"
                                        title="{{ $patient->address_short }}">
                                        {{ $patient->address_short ?? '-' }}
                                    </span>
                                </td>

                                {{-- หมายเหตุทั่วไป --}}
                                <td class="px-3 py-2 border align-top">
                                    @if ($patient->note_general)
                                        <span class="inline-block max-w-[260px] truncate"
                                            title="{{ $patient->note_general }}">
                                            {{ $patient->note_general }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- ปุ่มจัดการ --}}
                                <td class="px-3 py-2 border text-center align-top whitespace-nowrap">
                                    @can('view', $patient)
                                        <a href="{{ route('patients.show', $patient) }}"
                                            class="text-gray-700 hover:underline">
                                            {{ __('patients.view') }}
                                        </a>
                                    @endcan

                                    @can('update', $patient)
                                        <a href="{{ route('patients.edit', $patient) }}"
                                            class="text-blue-600 hover:underline ml-2">
                                            {{ __('patients.edit') }}
                                        </a>
                                    @endcan

                                    @can('delete', $patient)
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('{{ __('patients.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-2">
                                                {{ __('patients.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-3 text-center text-gray-500">
                                    {{-- ถ้ามี q แต่ไม่มีผล → แจ้งไม่พบข้อมูล, ถ้าไม่มี q → ใช้ข้อความรายการว่างทั่วไป --}}
                                    @if (isset($q) && $q !== '')
                                        {{ __('No results found.') }}
                                    @else
                                        {{ __('patients.no_data') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- pagination --}}
            <div class="mt-4">
                {{ $patients->withQueryString()->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
