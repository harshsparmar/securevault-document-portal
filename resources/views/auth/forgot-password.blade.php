<x-guest-layout>
    <div>
        <h1 class="auth-heading">Reset password</h1>
        <p class="auth-subheading">Enter your email and we'll send you a reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center py-3">
            {{ __('Send reset link') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="auth-link font-semibold">Back to login</a>
        </p>
    </form>
</x-guest-layout>
