<x-guest-layout>
    <div>
        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-subheading">Sign in to access your secure documents.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
            <label for="remember_me" class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center py-3">
            {{ __('Sign in') }}
        </x-primary-button>

        <!-- Register link -->
        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-500 mt-4">
                Don't have an account?
                <a href="{{ route('register') }}" class="auth-link font-semibold">Create one</a>
            </p>
        @endif
    </form>
</x-guest-layout>
