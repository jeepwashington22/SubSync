<section>
    <header>
        <h2 class="font-display text-lg font-medium text-[#f4f4f3]">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-[#68685f]">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data"
        x-data="{
            preview: @if ($user->profile_photo_path !== null) '{{ $user->profilePhotoUrl() }}' @else null @endif,
            onFileChange(event) {
                const file = event.target.files[0];
                if (! file) return;
                if (file.size > 10 * 1024 * 1024) {
                    event.target.value = '';
                    alert('The photo is too large. Please choose an image under 10MB.');
                    return;
                }
                this.preview = URL.createObjectURL(file);
            },
        }"
    >
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="flex items-center gap-4">
            <img
                x-show="preview"
                :src="preview"
                alt="{{ __('Profile photo') }}"
                class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-200"
            />
            <div
                x-show="! preview"
                x-cloak
                class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-amber-600 font-bold text-xl text-[#1a0d02]"
            >
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div>
                <x-input-label for="profile_photo" :value="__('Profile Photo')" class="text-[#68685f]" />
                <input
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                    x-on:change="onFileChange($event)"
                />
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                <p class="mt-1 text-xs text-gray-500">{{ __('JPG, PNG or WebP, up to 10MB.') }}</p>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-[#f4f4f3]/80">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-orange-400 hover:text-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 focus:ring-offset-[#08090a]">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-[#1a0d02] shadow-lg shadow-orange-950/40 transition hover:bg-orange-400">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-[#68685f]"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
