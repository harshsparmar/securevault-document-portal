<x-guest-layout>
    <div>
        <h1 class="auth-heading">Create an account</h1>
        <p class="auth-subheading">Join SecureVault to manage and view documents securely.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
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
            {{ __('Create account') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link font-semibold">Sign in</a>
        </p>
    </form>
</x-guest-layout>
