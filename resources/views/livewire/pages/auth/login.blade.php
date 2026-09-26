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

<div class="relative min-h-screen w-full flex flex-col items-center justify-center px-4 py-8 overflow-hidden select-none bg-[#070e27]"
     style="background-image: 
        radial-gradient(circle at 50% 50%, #0d1b4a 0%, #070e27 80%),
        linear-gradient(to right, rgba(56, 100, 200, 0.08) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(56, 100, 200, 0.08) 1px, transparent 1px);
        background-size: 100% 100%, 48px 48px, 48px 48px;">

    <!-- Background Industrial Watermark Icons (1-to-1 match with design) -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Washer / Small Gear (Top Left) -->
        <svg class="absolute top-[8%] left-[30%] w-9 h-9 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"/>
        </svg>

        <!-- Gear / Cogwheel (Top Right) -->
        <svg class="absolute top-[14%] right-[14%] w-12 h-12 text-[#2b3d75] opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="3"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
        </svg>

        <!-- Warning Triangle (Mid Left) -->
        <svg class="absolute top-[51%] left-[3%] w-7 h-7 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18h.01" />
        </svg>

        <!-- Hard Hat / Helmet (Lower Left) -->
        <svg class="absolute top-[63%] left-[24%] w-10 h-10 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4a8 8 0 00-8 8v1h16v-1a8 8 0 00-8-8zM3 14h18a1 1 0 011 1v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-1a1 1 0 011-1z" />
        </svg>

        <!-- Lightning Bolt (Bottom Left) -->
        <svg class="absolute bottom-[8%] left-[10%] w-6 h-9 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
        </svg>

        <!-- Lightning Bolt (Center Bottom) -->
        <svg class="absolute bottom-[4%] left-[44%] w-6 h-9 text-[#2b3d75] opacity-20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
        </svg>

        <!-- Lightning Bolt (Mid Right) -->
        <svg class="absolute top-[46%] right-[8%] w-6 h-9 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
        </svg>

        <!-- Pin / Tool Marker (Right Lower-Mid) -->
        <svg class="absolute top-[57%] right-[23%] w-7 h-10 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="currentColor">
            <circle cx="12" cy="7" r="5" />
            <path d="M12 12c-1.5 0-3 2-3 4s1.5 6 3 6 3-4 3-6-1.5-4-3-4z" />
        </svg>

        <!-- Warning Triangle (Lower Right) -->
        <svg class="absolute bottom-[22%] right-[13%] w-7 h-7 text-[#2b3d75] opacity-25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18h.01" />
        </svg>

        <!-- Large Industrial Cogwheel (Bottom Right) -->
        <svg class="absolute bottom-[9%] right-[32%] w-16 h-16 text-[#2b3d75] opacity-30" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.42.147-.822.336-1.202.563l-1.026-.522a1.875 1.875 0 00-2.28.423l-1.06 1.06a1.875 1.875 0 00-.424 2.28l.523 1.026c-.227.38-.416.782-.563 1.202l-1.072.178A1.875 1.875 0 001.5 12.922v1.5c0 .917.663 1.699 1.567 1.85l1.072.178c.147.42.336.822.563 1.202l-.523 1.026a1.875 1.875 0 00.424 2.28l1.06 1.06a1.875 1.875 0 002.28.424l1.026-.523c.38.227.782.416 1.202.563l.178 1.072a1.875 1.875 0 001.85 1.567h1.5c.917 0 1.699-.663 1.85-1.567l.178-1.072c.42-.147.822-.336 1.202-.563l1.026.523a1.875 1.875 0 002.28-.424l1.06-1.06a1.875 1.875 0 00.424-2.28l-.523-1.026c.227-.38.416-.782.563-1.202l1.072-.178a1.875 1.875 0 001.567-1.85v-1.5a1.875 1.875 0 00-1.567-1.85l-1.072-.178c-.147-.42-.336-.822-.563-1.202l.523-1.026a1.875 1.875 0 00-.424-2.28l-1.06-1.06a1.875 1.875 0 00-2.28-.424l-1.026.523a7.487 7.487 0 00-1.202-.563l-.178-1.072A1.875 1.875 0 0012.922 2.25h-1.5zm.922 6.75a3 3 0 100 6 3 3 0 000-6z" clip-rule="evenodd"/>
        </svg>
    </div>

    <!-- Company Logo & Branding -->
    <div class="relative z-10 flex flex-col items-center mb-6 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation Logo" class="w-24 h-24 sm:w-28 sm:h-28 object-contain drop-shadow-[0_12px_24px_rgba(0,0,0,0.6)]">
        <h2 class="text-white text-lg sm:text-xl font-black tracking-widest uppercase mt-3">
            WELLA METAL CORPORATION
        </h2>
        <p class="text-[#768bc4] text-xs font-bold tracking-wider uppercase mt-0.5">
            Management Suite
        </p>
    </div>

    <!-- Login Card Container -->
    <div class="relative z-10 w-full max-w-[430px] rounded-[28px] bg-[#131f4a]/90 border border-[#263773]/80 backdrop-blur-xl p-8 sm:p-10 shadow-2xl shadow-[#040816]/90"
         x-data="{ showPassword: false }">

        <!-- Header -->
        <div class="mb-7">
            <h1 class="text-white text-[32px] sm:text-[34px] font-black tracking-tight leading-tight">
                Sign In
            </h1>
            <p class="text-[#8497c2] text-[14px] sm:text-[15px] mt-1 font-normal">
                Enter your username and password
            </p>
        </div>

        <!-- Form -->
        <form wire:submit="login" class="space-y-5">
            <!-- Username Input -->
            <div>
                <label for="username" class="block text-[12px] font-bold tracking-wider text-[#798cb9] uppercase mb-2">
                    USERNAME
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6e80ad]">
                        <!-- User Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full bg-[#1c2a5e]/90 text-white placeholder-[#5a6c98] text-[15px] rounded-xl pl-12 pr-4 py-3.5 border border-[#2e407e] focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 transition-all duration-150"
                    />
                </div>
                @error('form.username')
                    <p class="text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-[12px] font-bold tracking-wider text-[#798cb9] uppercase mb-2">
                    PASSWORD
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6e80ad]">
                        <!-- Lock Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full bg-[#1c2a5e]/90 text-white placeholder-[#5a6c98] text-[15px] rounded-xl pl-12 pr-12 py-3.5 border border-[#2e407e] focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 transition-all duration-150"
                    />
                    <!-- Eye Toggle Button -->
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#6e80ad] hover:text-white transition-colors cursor-pointer"
                        aria-label="Toggle password visibility"
                    >
                        <template x-if="!showPassword">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showPassword">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </template>
                    </button>
                </div>
                @error('form.password')
                    <p class="text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full bg-[#dc2626] hover:bg-[#b91c1c] active:bg-[#991b1b] text-white font-extrabold text-[15px] tracking-wider py-4 rounded-xl shadow-lg shadow-red-600/40 flex items-center justify-center gap-2 uppercase transition-all duration-200 transform active:scale-[0.99] cursor-pointer"
                >
                    <span>SIGN IN</span>
                    <!-- Arrow Right -->
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Footer Text (Centered below card) -->
    <div class="relative z-10 mt-9 text-center">
        <p class="text-[12px] font-semibold tracking-[0.25em] text-[#4d5f8c] uppercase">
            WELLA METAL CORPORATION · 2026
        </p>
    </div>
</div>
