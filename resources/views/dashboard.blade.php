<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            {{-- ปุ่มเฉพาะ Admin: อนุมัติผู้ใช้ --}}
            @role('admin')
                <div class="mt-6">
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ __('Approve User') }}
                    </a>
                </div>
            @endrole

            {{-- ปุ่มเข้าเมนูผู้ป่วย: Admin, Doctor, Nurse เท่านั้น --}}
            @role('admin|doctor|nurse')
                <div class="mt-6">
                    <a href="{{ route('patients.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        {{ __('patients.title') }}
                    </a>
                </div>
            @endrole
        </div>
    </div>
</x-app-layout>
