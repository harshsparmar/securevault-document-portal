<x-app-layout>
    <x-slot name="header">
        <div class="page-header">
            <div class="flex items-center gap-3">
                <div class="profile-section__icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="page-title">{{ __('Profile') }}</h2>
                    <p class="page-subtitle">Manage your account settings and security preferences.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="doc-page">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Information --}}
            <div class="card">
                <div class="card__body">
                    <div class="profile-section">
                        <div class="profile-section__header">
                            <div class="profile-section__icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="profile-section__title">Profile Information</h3>
                                <p class="profile-section__desc">Update your name and email address.</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-xl mt-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card">
                <div class="card__body">
                    <div class="profile-section">
                        <div class="profile-section__header">
                            <div class="profile-section__icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h3 class="profile-section__title">Security</h3>
                                <p class="profile-section__desc">Keep your account secure with a strong password.</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-xl mt-4">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card ring-1 ring-red-100">
                <div class="card__body">
                    <div class="profile-section">
                        <div class="profile-section__header">
                            <div class="profile-section__icon--danger">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="profile-section__title text-red-900">Danger Zone</h3>
                                <p class="profile-section__desc">Permanently delete your account and all data.</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-xl mt-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
