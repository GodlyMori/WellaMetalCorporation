<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

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

<div class="min-h-screen w-full flex flex-col items-center justify-center px-4 py-12 select-none bg-[#0c163b]">
    <!-- Company Logo & Authority Header -->
    <div class="flex flex-col items-center mb-6 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation Logo" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
        <h2 class="text-white text-sm font-semibold tracking-wide mt-3">
            Wella Metal Corporation
        </h2>
        <p class="text-slate-400 text-xs font-medium tracking-wide uppercase mt-0.5">
            Management Platform
        </p>
    </div>

    <!-- Login Card Container -->
    <div class="w-full max-w-[420px] rounded-md bg-[#121f4a] border border-[#223368] p-7 sm:p-9 shadow-lg"
         x-data="{ showPassword: false }">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-white text-xl sm:text-2xl font-bold tracking-tight">
                Sign In
            </h1>
            <p class="text-slate-300 text-xs mt-1">
                Enter your system credentials to access the workspace
            </p>
        </div>

        <!-- Form -->
        <form wire:submit="login" class="space-y-4">
            <!-- Username Input -->
            <div>
                <label for="username" class="block text-[11px] font-semibold text-slate-400 mb-1.5">
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
                        placeholder="admin / manager / secretary"
                        class="w-full bg-[#1b2b5e] text-white placeholder-slate-400 text-xs sm:text-sm rounded pl-10 pr-3.5 py-2.5 border border-[#2c3f7a] focus:border-[#142259] focus:outline-none focus:ring-1 focus:ring-[#142259] focus-visible:outline-none transition-colors"
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
                <label for="password" class="block text-[11px] font-semibold text-slate-400 mb-1.5">
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
                        placeholder="••••••••"
                        class="w-full bg-[#1b2b5e] text-white placeholder-slate-400 text-xs sm:text-sm rounded pl-10 pr-10 py-2.5 border border-[#2c3f7a] focus:border-[#142259] focus:outline-none focus:ring-1 focus:ring-[#142259] focus-visible:outline-none transition-colors"
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
                    class="w-full bg-[#142259] hover:bg-[#0e1840] active:bg-[#0a112e] text-white font-semibold text-xs tracking-wide py-3 rounded flex items-center justify-center gap-2 uppercase transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#142259]/50 focus-visible:outline-none"
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
        <p class="text-[11px] font-medium tracking-widest text-slate-500 uppercase">
            Wella Metal Corporation &copy; {{ date('Y') }} &middot; All Rights Reserved
        </p>
    </div>
</div>
