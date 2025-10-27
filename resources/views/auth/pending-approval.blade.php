<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">
                {{ __('Your account is not approved yet') }}
            </h2>
            <p class="text-gray-600 text-center mb-6">
                {{ __('Please wait for an administrator to approve your account before using the system') }}
            </p>

            {{-- Primary: Logout (controller will redirect to login) --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-primary-button class="w-full justify-center">
                    {{ __('Log Out') }}
                </x-primary-button>
            </form>

            {{-- Secondary link: Back to homepage (public) --}}
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-800">
                    {{ __('Back to homepage') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
