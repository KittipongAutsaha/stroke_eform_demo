<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approve User') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Flash Message --}}
        @if (session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 px-4 py-3 rounded text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="flex items-center justify-between mb-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="only_pending" value="1" @checked(request('only_pending'))
                        class="rounded border-gray-300">
                    <span>{{ __('Pending Approval') }}</span>
                </label>
                <button class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">
                    {{ __('Search') }}
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Email Address') }}</th>
                        <th class="px-4 py-3">{{ __('Role') }}</th>
                        <th class="px-4 py-3">{{ __('Requested Role') }}</th>
                        <th class="px-4 py-3">{{ __('Pending Approval') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $u)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $u->id }}</td>
                            <td class="px-4 py-3">{{ $u->name }}</td>
                            <td class="px-4 py-3">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                {{ $u->roles->pluck('name')->join(', ') ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $u->requested_role ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($u->approved_at)
                                    <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">✔</span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">✕</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (is_null($u->approved_at))
                                    <form method="POST" action="{{ route('admin.users.approve', $u) }}"
                                        onsubmit="return confirm('{{ __('Approve') }}: {{ $u->name }} ?')"
                                        class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')

                                        @php
                                            $currentRole = $u->requested_role ?? $u->roles->pluck('name')->first();
                                        @endphp
                                        <select name="role" class="border rounded px-2 py-1 text-sm">
                                            <option value="" {{ $currentRole ? '' : 'selected' }}>--
                                                {{ __('Select Role') }} --</option>
                                            <option value="doctor" {{ $currentRole === 'doctor' ? 'selected' : '' }}>
                                                {{ __('Doctor') }}</option>
                                            <option value="nurse" {{ $currentRole === 'nurse' ? 'selected' : '' }}>
                                                {{ __('Nurse') }}</option>
                                            <option value="staff" {{ $currentRole === 'staff' ? 'selected' : '' }}>
                                                {{ __('Staff') }}</option>
                                        </select>

                                        <button
                                            class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                                            {{ __('Approve') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">{{ __('User is already approved.') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                {{ __('No results found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>
</x-app-layout>
