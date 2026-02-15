<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-slate-900" style="font-family: 'Playfair Display', serif;">
                    Welcome back
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Sign in to review approvals, monitor activity, and serve customers.
                </p>
            </div>

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-slate-700" />
                <x-input id="email"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="text-slate-700" />
                <x-input id="password"
                    class="block mt-1 w-full bg-white/80 border-slate-200 focus:border-emerald-600 focus:ring-emerald-600"
                    type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-emerald-700 hover:text-emerald-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-block px-5 py-1.5 border border-emerald-200 text-emerald-700 hover:border-emerald-300 hover:text-emerald-900 rounded-md text-sm leading-normal">
                        Register
                    </a>
                @endif

                <x-button class="ms-4 bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
