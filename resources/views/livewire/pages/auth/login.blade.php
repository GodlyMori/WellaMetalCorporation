<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);

            return;
        }

        if ($user->hasRole('secretary')) {
            $this->redirect(route('secretary.workspace', absolute: false), navigate: true);

            return;
        }

        $this->redirect(route('dashboard', absolute: false), navigate: true); // fallback
    }
}; ?>

<div class="relative min-h-screen w-full flex flex-col items-center justify-center px-4 py-12 select-none overflow-hidden bg-gradient-to-br from-[#060c24] via-[#091438] to-[#040818]">

    <!-- Architectural Background Grid Pattern -->
    <div class="absolute inset-0 pointer-events-none opacity-40"
         style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.2px, transparent 1.2px); background-size: 28px 28px;">
    </div>

    <!-- Atmospheric Ambient Lighting Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-blue-600/20 blur-[130px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-indigo-600/20 blur-[130px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[520px] h-[520px] rounded-full bg-blue-500/10 blur-[150px] pointer-events-none"></div>

    <!-- Corner Technical Crosshairs & Accents -->
    <div class="absolute top-6 left-6 hidden sm:flex items-center gap-2 text-slate-500/40 text-[10px] font-mono tracking-widest uppercase">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
        <span>SYS.WMC // LAT 14.5995° N, 120.9842° E</span>
    </div>
    <div class="absolute top-6 right-6 hidden sm:flex items-center gap-2 text-slate-500/40 text-[10px] font-mono tracking-widest uppercase">
        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>GATEWAY SECURE</span>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 w-full max-w-[430px] flex flex-col items-center">

        <!-- Company Logo & Enterprise Header -->
        <div class="flex flex-col items-center mb-6 text-center">
            <div class="relative p-2.5 rounded-2xl bg-gradient-to-b from-white/10 to-transparent border border-white/15 shadow-xl shadow-blue-950/50 backdrop-blur-md">
                <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation Logo" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-[0_4px_12px_rgba(37,99,235,0.3)]">
            </div>
            
            <h2 class="text-white text-base sm:text-lg font-bold tracking-wide mt-4 drop-shadow-sm">
                Wella Metal Corporation
            </h2>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 mt-1.5 rounded-full bg-white/[0.07] border border-white/10 backdrop-blur-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <p class="text-slate-300 text-[10px] font-semibold tracking-wider uppercase">
                    Enterprise Retail Operations &amp; Yard Portal
                </p>
            </div>
        </div>

        <!-- Glassmorphic Login Card Container -->
        <div class="w-full rounded-xl bg-[#12204a]/85 border border-white/15 p-7 sm:p-9 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7)] backdrop-blur-xl ring-1 ring-white/10"
             x-data="{ showPassword: false }">

            <!-- Card Header -->
            <div class="mb-6">
                <h1 class="text-white text-xl sm:text-2xl font-bold tracking-tight">
                    Sign In
                </h1>
                <p class="text-slate-300 text-xs mt-1">
                    Enter authorized credentials to access branch workspace
                </p>
            </div>

            <!-- Form -->
            <form wire:submit="login" class="space-y-4">
                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-[11px] font-semibold text-slate-300 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input
                            id="username"
                            wire:model="form.username"
                            type="text"
                            name="username"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full bg-[#1b2b5e]/90 text-white caret-white placeholder-slate-400 text-xs sm:text-sm rounded-lg pl-10 pr-3.5 py-2.5 border border-[#2c3f7a] focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus-visible:outline-none transition-colors"
                        />
                    </div>
                    @error('form.username')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-[11px] font-semibold text-slate-300 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            wire:model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full bg-[#1b2b5e]/90 text-white caret-white placeholder-slate-400 text-xs sm:text-sm rounded-lg pl-10 pr-10 py-2.5 border border-[#2c3f7a] focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus-visible:outline-none transition-colors"
                        />
                        <!-- Eye Toggle Button with aria-label -->
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition-colors cursor-pointer"
                            aria-label="Toggle password visibility"
                        >
                            <template x-if="!showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                            <template x-if="showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('form.password')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-semibold text-xs tracking-wider py-3 rounded-lg flex items-center justify-center gap-2 uppercase shadow-lg shadow-blue-600/30 hover:shadow-blue-500/40 transition-all duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:outline-none"
                    >
                        <span>Sign In</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Corporate Footer -->
        <div class="mt-8 text-center">
            <p class="text-[11px] font-medium tracking-widest text-slate-400/80 uppercase">
                Wella Metal Corporation &copy; {{ date('Y') }} &middot; All Rights Reserved
            </p>
        </div>

    </div>
</div>
