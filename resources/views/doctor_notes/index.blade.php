{{-- resources/views/doctor_notes/index.blade.php --}}
<x-app-layout>
    {{-- ส่วนหัวของหน้า --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('doctor_notes.index.title') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ส่วนหัวภายในหน้า --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">
                    🩺 {{ __('doctor_notes.index.title') }}
                </h1>

                {{-- ปุ่มเพิ่มบันทึกแพทย์ใหม่ (เฉพาะผู้ที่มีสิทธิ์) --}}
                @can('create', App\Models\DoctorNote::class)
                    <a href="{{ route('patients.doctor-notes.create', $patient) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        + {{ __('doctor_notes.actions.add') }}
                    </a>
                @endcan
            </div>

            {{-- แสดงข้อความแจ้งเตือนสำเร็จ (Flash message) --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ตรวจว่ามี note หรือไม่ --}}
            @if ($notes->isEmpty())
                <div class="text-gray-500 italic text-center py-10">
                    {{ __('doctor_notes.empty') }}
                </div>
            @else
                {{-- ตารางแสดงรายการบันทึกแพทย์ --}}
                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('doctor_notes.recorded_at') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('doctor_notes.doctor') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('doctor_notes.diagnosis') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('doctor_notes.status') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($notes as $note)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $note->recorded_at ? $note->recorded_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $note->doctor->name ?? __('patients.empty') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $note->diagnosis ?? __('patients.empty') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($note->status === 'planned') bg-yellow-100 text-yellow-800
                                            @elseif ($note->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif ($note->status === 'signed_off') bg-green-100 text-green-800
                                            @elseif ($note->status === 'cancelled') bg-gray-200 text-gray-600 @endif">
                                            {{ __('doctor_notes.status.' . $note->status) }}
                                        </span>
                                    </td>

                                    {{-- ปุ่มจัดการ --}}
                                    <td class="px-4 py-2 text-sm text-right space-x-2">
                                        {{-- ปุ่มดู --}}
                                        <a href="{{ route('patients.doctor-notes.show', [$patient, $note]) }}"
                                            class="text-blue-600 hover:text-blue-800">
                                            {{ __('doctor_notes.actions.view') }}
                                        </a>

                                        {{-- ปุ่มแก้ไข (เฉพาะผู้มีสิทธิ์) --}}
                                        @can('update', $note)
                                            <a href="{{ route('patients.doctor-notes.edit', [$patient, $note]) }}"
                                                class="text-yellow-600 hover:text-yellow-800">
                                                {{ __('doctor_notes.actions.edit') }}
                                            </a>
                                        @endcan

                                        {{-- ปุ่มลบ (เฉพาะผู้มีสิทธิ์) --}}
                                        @can('delete', $note)
                                            <form action="{{ route('patients.doctor-notes.destroy', [$patient, $note]) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('{{ __('doctor_notes.confirm_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">
                                                    {{ __('doctor_notes.actions.delete') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
