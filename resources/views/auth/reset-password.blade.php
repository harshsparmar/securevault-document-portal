<x-guest-layout>
    <div>
        <h1 class="auth-heading">Reset password</h1>
        <p class="auth-subheading">Enter your new password below.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address (readonly) -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1.5 w-full bg-gray-50 cursor-not-allowed" type="email" name="email" :value="old('email', $request->email)" required readonly tabindex="-1" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('New password')" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autofocus autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center py-3">
            {{ __('Reset Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>
