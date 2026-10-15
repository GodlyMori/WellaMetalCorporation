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
     * Helper to quickly populate credentials for role testing and demonstrations.
     */
    public function setDemoUser(string $role): void
    {
        $this->form->username = $role;
        $this->form->password = 'password123';
        $this->resetErrorBag();
    }

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

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-slate-100 selection:bg-[#142259] selection:text-white">

    <!-- ============================================================== -->
    <!-- LEFT PANEL: ENTERPRISE BRANDING & CAPSTONE CAPABILITY SHOWCASE  -->
    <!-- ============================================================== -->
    <div class="hidden lg:flex lg:w-7/12 xl:w-3/5 bg-gradient-to-br from-[#0a1638] via-[#10204d] to-[#07112c] text-white p-12 xl:p-16 flex-col justify-between relative overflow-hidden">
        
        <!-- Background Architectural Grid & Ambient Orbs -->
        <div class="absolute inset-0 pointer-events-none opacity-20"
             style="background-image: linear-gradient(to right, rgba(255,255,255,0.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 36px 36px;">
        </div>
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-blue-500/20 blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-indigo-500/20 blur-[140px] pointer-events-none"></div>

        <!-- Decorative Structural SVG Truss / Engineering Lines -->
        <svg class="absolute right-0 bottom-0 w-2/3 h-2/3 text-white/[0.03] pointer-events-none" viewBox="0 0 400 400" fill="currentColor">
            <line x1="0" y1="400" x2="400" y2="0" stroke="currentColor" stroke-width="2" stroke-dasharray="6,6" />
            <line x1="100" y1="400" x2="400" y2="100" stroke="currentColor" stroke-width="1.5" />
            <line x1="200" y1="400" x2="400" y2="200" stroke="currentColor" stroke-width="1.5" />
            <line x1="0" y1="300" x2="300" y2="0" stroke="currentColor" stroke-width="1.5" />
            <circle cx="200" cy="200" r="160" stroke="currentColor" stroke-width="1" fill="none" />
            <circle cx="200" cy="200" r="120" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4,4" fill="none" />
        </svg>

        <!-- Top Header / Brand Title -->
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-white/10 p-2 border border-white/20 backdrop-blur-md shadow-lg flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-base font-bold text-white tracking-wide">WELLA METAL CORPORATION</h2>
                    <p class="text-xs text-blue-200/70 font-mono tracking-wider uppercase">Fabrication Yard &amp; Retail Operations Portal</p>
                </div>
            </div>
            <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-mono text-emerald-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>SYSTEM ONLINE</span>
            </div>
        </div>

        <!-- Middle Hero & Feature Highlights -->
        <div class="relative z-10 my-auto py-8 space-y-8 max-w-2xl">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-xs font-semibold text-blue-200">
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Enterprise Retail &amp; Yard Management Platform</span>
                </div>
                <h1 class="text-3xl xl:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Optimized Inventory, Point-of-Sale &amp; Operational Continuity
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Designed to eliminate manual inventory tallying, streamline retail lay-away transactions, automate periodic reporting, and safeguard core business data with robust archiving.
                </p>
            </div>

            <!-- 4 Architecture Highlights (Addressing Panelist Recommendations & Objectives) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- Highlight 1: Yard Inventory -->
                <div class="p-4 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur-xs hover:bg-white/[0.09] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-300 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Yard Inventory &amp; Batch Intake</h3>
                    <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                        Physical yard stock tracking, batch intake workbench, category management, and low-stock threshold alerts.
                    </p>
                </div>

                <!-- Highlight 2: Sales & Lay-Away -->
                <div class="p-4 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur-xs hover:bg-white/[0.09] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Sales POS &amp; Lay-Away Ledger</h3>
                    <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                        Seamless POS processing, installment payment amortizations, customer accounts, and official digital receipts.
                    </p>
                </div>

                <!-- Highlight 3: Periodic Business Reports (Obj 3) -->
                <div class="p-4 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur-xs hover:bg-white/[0.09] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Automated Periodic Reports</h3>
                    <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                        Generates daily, weekly, and monthly sales, turnover, and branch profitability PDF summaries with full traceability.
                    </p>
                </div>

                <!-- Highlight 4: Data Preservation (Obj 5) -->
                <div class="p-4 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur-xs hover:bg-white/[0.09] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-300 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Preservation &amp; Archiving</h3>
                    <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                        Automated database backup snapshots, point-in-time recovery, and transaction archiving for operational continuity.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Technical Coordinates -->
        <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400 font-mono">
            <span>TERMINAL ID: WMC-MAIN-01</span>
            <span>SAN JOSE DEL MONTE, BULACAN</span>
            <span>TLS 1.3 / AES-256</span>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- RIGHT PANEL: CLEAN AUTHENTICATION PORTAL (LIGHT & ACCESSIBLE)   -->
    <!-- ============================================================== -->
    <div class="w-full lg:w-5/12 xl:w-2/5 min-h-screen flex flex-col justify-between p-6 sm:p-10 lg:p-12 bg-slate-50 relative">
        
        <!-- Subtle Background Dot Pattern for Depth -->
        <div class="absolute inset-0 pointer-events-none opacity-40"
             style="background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 20px 20px;">
        </div>

        <!-- Mobile Header (Visible only on < lg) -->
        <div class="lg:hidden flex items-center justify-between pb-6 border-b border-slate-200 relative z-10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation" class="w-9 h-9 object-contain">
                <div>
                    <h2 class="text-xs font-bold text-slate-900 tracking-wide">WELLA METAL CORP.</h2>
                    <p class="text-[10px] text-slate-500 font-mono">Yard Operations Portal</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Active</span>
            </span>
        </div>

        <!-- Center Login Card -->
        <div class="my-auto w-full max-w-md mx-auto py-8 relative z-10" x-data="{ showPassword: false }">
            
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl shadow-slate-200/60 p-8 sm:p-9 space-y-6">
                
                <!-- Card Header -->
                <div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center mb-4 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Sign In</h1>
                    <p class="text-xs text-slate-500 mt-1">Enter your credentials to access the branch workspace</p>
                </div>

                <!-- Quick-Select Demo Roles (Perfect for Thesis Panelists / Testing) -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Quick Demo Sign-In
                        </span>
                        <span class="text-slate-400 font-mono text-[10px]">Click to fill</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 text-xs font-semibold">
                        <button type="button"
                                wire:click="setDemoUser('admin')"
                                class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-700 text-slate-700 text-[11px] shadow-2xs transition-all text-center cursor-pointer">
                            Admin
                        </button>
                        <button type="button"
                                wire:click="setDemoUser('manager')"
                                class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-700 text-slate-700 text-[11px] shadow-2xs transition-all text-center cursor-pointer">
                            Manager
                        </button>
                        <button type="button"
                                wire:click="setDemoUser('secretary')"
                                class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-700 text-slate-700 text-[11px] shadow-2xs transition-all text-center cursor-pointer">
                            Secretary
                        </button>
                    </div>
                </div>

                <!-- Login Form -->
                <form wire:submit="login" class="space-y-4">
                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Username or Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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
                                placeholder="e.g. admin or secretary"
                                class="w-full bg-white text-slate-900 placeholder-slate-400 text-xs sm:text-sm rounded-lg pl-10 pr-3.5 py-2.5 border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:outline-none transition-colors shadow-2xs"
                            />
                        </div>
                        @error('form.username')
                            <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700">
                                Password
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                wire:model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••••"
                                class="w-full bg-white text-slate-900 placeholder-slate-400 text-xs sm:text-sm rounded-lg pl-10 pr-10 py-2.5 border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:outline-none transition-colors shadow-2xs"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                aria-label="Toggle password visibility"
                            >
                                <template x-if="!showPassword">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                        @error('form.password')
                            <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                            <input type="checkbox" wire:model="form.remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <span>Keep me signed in</span>
                        </label>
                        <span class="text-slate-400 font-mono text-[11px]">v2.4 Core</span>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="login"
                            class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs tracking-wider py-3 rounded-lg flex items-center justify-center gap-2 uppercase shadow-md shadow-blue-600/20 hover:shadow-lg transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                                <span>Sign In to Terminal</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="login" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Verifying credentials...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Security Note -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Authorized access strictly monitored and archived</span>
                </div>

            </div>

        </div>

        <!-- Right Panel Footer -->
        <div class="text-center py-2 text-[11px] text-slate-400 relative z-10">
            Wella Metal Corporation &copy; {{ date('Y') }} &middot; All Rights Reserved
        </div>

    </div>

</div>
