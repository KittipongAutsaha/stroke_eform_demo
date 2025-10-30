<x-app-layout>
    {{-- หัวข้อหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('patients.detail') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @can('view', $patient)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">

                    {{-- HN --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.hn') }}:</span>
                        <span class="text-gray-900">{{ $patient->hn }}</span>
                    </div>

                    {{-- CID --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.cid') }}:</span>
                        <span class="text-gray-900">{{ $patient->cid ?: '-' }}</span>
                    </div>

                    {{-- ชื่อ-นามสกุล --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.name') }}:</span>
                        <span class="text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</span>
                    </div>

                    {{-- วันเกิด --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.dob') }}:</span>
                        <span class="text-gray-900">{{ $patient->dob ? $patient->dob->format('d/m/Y') : '-' }}</span>
                    </div>

                    {{-- เพศ --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.sex') }}:</span>
                        <span class="text-gray-900">{{ ucfirst($patient->sex ?? '-') }}</span>
                    </div>

                    {{-- ที่อยู่ --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.address') }}:</span>
                        <span class="text-gray-900">{{ $patient->address_short ?: '-' }}</span>
                    </div>

                    {{-- หมายเหตุ --}}
                    <div>
                        <span class="font-medium text-gray-700">{{ __('patients.note_general') }}:</span>
                        <span class="text-gray-900">{{ $patient->note_general ?: '-' }}</span>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="flex justify-end space-x-2 mt-6">
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            {{ __('Back') }}
                        </a>

                        @can('update', $patient)
                            <a href="{{ route('patients.edit', $patient->id) }}"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                {{ __('patients.edit') }}
                            </a>
                        @endcan

                        @can('delete', $patient)
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('{{ __('patients.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    {{ __('patients.delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
