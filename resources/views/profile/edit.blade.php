<x-app-layout>
    <style>
        /* Dark-theme overrides for shared Breeze form components on this page */
        .settings-card label { color: #b8b7ae !important; }
        .settings-card input[type="text"],
        .settings-card input[type="email"],
        .settings-card input[type="password"] {
            background-color: rgba(255,255,255,.04);
            border-color: rgba(255,255,255,.12);
            color: #f4f4f3;
        }
        .settings-card input[type="text"]:focus,
        .settings-card input[type="email"]:focus,
        .settings-card input[type="password"]:focus {
            border-color: rgba(249,115,22,.5);
            box-shadow: 0 0 0 2px rgba(249,115,22,.15);
        }
        .settings-card input[type="file"] { color: #68685f; }
        .settings-card input[type="file"]::file-selector-button {
            background: rgba(255,255,255,.08);
            color: #e4e4e2;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 0.5rem;
            padding: 0.5rem 0.9rem;
            margin-right: 0.75rem;
            cursor: pointer;
        }
        .settings-card input[type="file"]::file-selector-button:hover { background: rgba(255,255,255,.14); }
        .settings-card ul { color: #fca5a5 !important; }
    </style>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="settings-card rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 backdrop-blur-xl sm:p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        @if(auth()->user()->password !== null)
            <div class="settings-card rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 backdrop-blur-xl sm:p-6">
                @include('profile.partials.update-password-form')
            </div>
        @endif

        <div class="settings-card rounded-2xl border border-red-400/[0.22] bg-red-400/[0.04] p-5 backdrop-blur-xl sm:p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
