<div class="min-h-screen relative overflow-hidden bg-slate-950 text-slate-100">
    <div class="absolute inset-0">
        <div class="absolute -top-40 -right-32 h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-24 h-80 w-80 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.08),_transparent_55%)]"></div>
        <div
            class="absolute inset-0 opacity-30 [background-image:linear-gradient(135deg,rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:28px_28px]">
        </div>
    </div>

    <div class="relative z-10 flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-5xl">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_1fr] items-center">
                <div class="hidden lg:flex flex-col gap-6">
                    <div class="w-fit">
                        {{ $logo }}
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-emerald-200">Secure Banking Access</p>
                        <h1 class="mt-4 text-4xl font-semibold leading-tight"
                            style="font-family: 'Playfair Display', serif;">
                            {{ config('app.name', 'Bank Management Suite') }}
                        </h1>
                        <p class="mt-3 text-base text-emerald-100/80">
                            Manage approvals, monitor transactions, and serve customers with confidence.
                        </p>
                    </div>
                    <div class="grid gap-4 text-sm text-emerald-100/70">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-emerald-400/30 bg-emerald-500/10">24/7</span>
                            <span>Real-time activity monitoring and audit trails.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-emerald-400/30 bg-emerald-500/10">SSL</span>
                            <span>Encrypted sessions for staff and managers.</span>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <div class="rounded-2xl border border-white/10 bg-white/95 text-slate-900 shadow-2xl">
                        <div class="p-8 sm:p-10">
                            <div class="mb-6 flex items-center gap-3 lg:hidden">
                                <div class="w-fit">
                                    {{ $logo }}
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-700">Secure Banking</p>
                                    <p class="text-base font-semibold" style="font-family: 'Playfair Display', serif;">
                                        {{ config('app.name', 'Bank Management Suite') }}
                                    </p>
                                </div>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                    <p class="mt-6 text-center text-xs text-emerald-100/70">
                        Need access? Contact your branch manager or systems administrator.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
