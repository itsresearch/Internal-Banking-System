<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-slate-900" style="font-family: 'Playfair Display', serif;">
                    Create your access
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Register to join the secure banking workspace.
                </p>
            </div>

            <div>
                <x-label for="name" value="{{ __('Name') }}" class="text-slate-700" />
                <x-input id="name"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" class="text-slate-700" />
                <x-input id="email"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="text-slate-700" />
                <x-input id="password"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-slate-700" />
                <x-input id="password_confirmation"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-emerald-700 hover:text-emerald-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4 bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
