<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-8">
        <div class="max-w-xl w-full bg-white p-8 rounded-2xl shadow">
            <h1 class="text-3xl font-bold text-gray-800 mb-3">
                {{ __('Stroke e-Form Demo') }}
            </h1>
            <p class="text-gray-600 mb-6">
                {{ __('A demo system for stroke patient e-forms, designed for healthcare professionals') }}
            </p>

            <div class="flex gap-3">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white">
                    {{ __('Log in') }}
                </a>

                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300">
                    {{ __('Register') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
