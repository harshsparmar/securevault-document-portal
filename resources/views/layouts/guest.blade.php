<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SecureVault') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark mode init (prevents flash) -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('darkMode', {
                    on: localStorage.getItem('darkMode') === 'true',
                    toggle() {
                        this.on = !this.on;
                        localStorage.setItem('darkMode', this.on);
                    }
                });
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="guest-shell">
            {{-- Left: Branding panel (blue gradient) --}}
            <div class="guest-brand">
                <div class="guest-brand__inner">
                    <x-application-logo class="w-14 h-14" />
                    <h1 class="guest-brand__title">SecureVault</h1>
                    <p class="guest-brand__tagline">Your documents, protected by enterprise-grade encryption & secure preview.</p>
                    <div class="guest-brand__features">
                        <div class="guest-brand__feature">
                            <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            <span>Private encrypted storage</span>
                        </div>
                        <div class="guest-brand__feature">
                            <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                            <span>View-only secure preview</span>
                        </div>
                        <div class="guest-brand__feature">
                            <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Role-based access control</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Auth form --}}
            <div class="guest-form-area">
                <div class="guest-form-card">
                    {{-- Mobile logo --}}
                    <div class="sm:hidden flex items-center gap-3 mb-6">
                        <x-application-logo class="w-10 h-10" />
                        <span class="text-lg font-bold text-gray-900">SecureVault</span>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
