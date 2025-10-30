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

            {{-- แสดงข้อความเมื่อทำงานสำเร็จ เช่น เพิ่ม/ลบ/แก้ไข --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ปุ่มเพิ่มข้อมูลผู้ป่วย --}}
            @can('create', \App\Models\Patient::class)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('patients.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ __('patients.add_new') }}
                    </a>
                </div>
            @endcan

            {{-- ตารางแสดงข้อมูลผู้ป่วยทั้งหมด --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">{{ __('patients.hn') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.cid') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.name') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.dob') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.sex') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.address') }}</th>
                            <th class="px-4 py-2 border">{{ __('patients.note_general') }}</th>
                            <th class="px-4 py-2 border text-center w-40">{{ __('patients.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- วนลูปแสดงรายชื่อผู้ป่วย --}}
                        @forelse($patients as $patient)
                            <tr class="hover:bg-gray-50">
                                {{-- HN --}}
                                <td class="px-4 py-2 border">{{ $patient->hn }}</td>

                                {{-- CID --}}
                                <td class="px-4 py-2 border">{{ $patient->cid ?: '-' }}</td>

                                {{-- ชื่อ --}}
                                <td class="px-4 py-2 border">{{ $patient->first_name }} {{ $patient->last_name }}</td>

                                {{-- วันเกิด --}}
                                <td class="px-4 py-2 border">
                                    {{ $patient->dob ? $patient->dob->format('d/m/Y') : '-' }}
                                </td>

                                {{-- เพศ --}}
                                <td class="px-4 py-2 border">{{ $patient->sex ?? '-' }}</td>

                                {{-- ที่อยู่ --}}
                                <td class="px-4 py-2 border">{{ $patient->address_short ?? '-' }}</td>

                                {{-- หมายเหตุ --}}
                                <td class="px-4 py-2 border">
                                    @if ($patient->note_general)
                                        <span title="{{ $patient->note_general }}">
                                            {{ \Illuminate\Support\Str::limit($patient->note_general, 40) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- ปุ่มจัดการ (ดู / แก้ไข / ลบ) --}}
                                <td class="px-4 py-2 border text-center">
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
                                        {{-- ฟอร์มลบ --}}
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
                            {{-- กรณีไม่มีข้อมูล --}}
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-500">
                                    {{ __('patients.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ส่วนแสดงปุ่มเปลี่ยนหน้า pagination --}}
            <div class="mt-4">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
