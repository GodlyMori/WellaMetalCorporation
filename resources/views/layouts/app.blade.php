<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Wella Metal Corporation') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-[#f4f7fc] text-slate-800 min-h-screen flex overflow-x-hidden"
          x-data="{ 
              sidebarOpen: false, 
              switchRoleModal: false,
              userMenuOpen: false 
          }">

        <!-- SIDEBAR (1:1 with Figma Design) -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-[#0d1747] text-white flex flex-col justify-between transition-transform duration-200 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Top Section -->
            <div class="p-6">
                <!-- Branding -->
                <div class="flex items-center gap-3.5 pb-6 border-b border-[#1c2a68]">
                    <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation" class="w-14 h-14 object-contain flex-shrink-0 drop-shadow-md">
                    <div>
                        <h1 class="text-white text-[13px] font-black tracking-wider leading-tight uppercase">
                            WELLA METAL
                        </h1>
                        <h1 class="text-white text-[13px] font-black tracking-wider leading-tight uppercase">
                            CORPORATION
                        </h1>
                        <p class="text-[#768bc4] text-[11px] font-medium leading-none mt-1">
                            Management Suite
                        </p>
                    </div>
                </div>

                <!-- Navigation Heading -->
                <div class="mt-8 mb-3">
                    <p class="text-[#5266a3] text-[11px] font-extrabold tracking-widest uppercase">
                        NAVIGATION
                    </p>
                </div>

                <!-- Nav Menu Items -->
                <nav class="space-y-1.5">
                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard*') || request()->routeIs('admin.dashboard*') ? 'bg-[#142366] text-white font-bold shadow-sm' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                            </svg>
                            <span class="text-[13px] tracking-wider uppercase">DASHBOARD</span>
                        </div>
                        @if(request()->routeIs('dashboard*') || request()->routeIs('admin.dashboard*'))
                            <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                        @endif
                    </a>

                    <!-- Inventory Link -->
                    <a href="{{ route('inventory') }}"
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('inventory*') ? 'bg-[#142366] text-white font-bold shadow-sm' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="text-[13px] tracking-wider uppercase">INVENTORY</span>
                        </div>
                        @if(request()->routeIs('inventory*'))
                            <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                        @endif
                    </a>

                    <!-- Sales Link -->
                    <a href="{{ route('sales') }}"
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('sales*') ? 'bg-[#142366] text-white font-bold shadow-sm' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2"/>
                            </svg>
                            <span class="text-[13px] tracking-wider uppercase">SALES</span>
                        </div>
                        @if(request()->routeIs('sales*'))
                            <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                        @endif
                    </a>

                    <!-- Promotions Link -->
                    <a href="{{ route('promotions') }}"
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('promotions*') ? 'bg-[#142366] text-white font-bold shadow-sm' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="text-[13px] tracking-wider uppercase">PROMOTIONS</span>
                        </div>
                        @if(request()->routeIs('promotions*'))
                            <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                        @endif
                    </a>

                    <!-- Reports Link -->
                    <a href="{{ route('reports') }}"
                       class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('reports*') ? 'bg-[#142366] text-white font-bold shadow-sm' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-[13px] tracking-wider uppercase">REPORTS</span>
                        </div>
                        @if(request()->routeIs('reports*'))
                            <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                        @endif
                    </a>

                    @if(Auth::user()?->hasRole('admin'))
                        <!-- Analytics & BI Link (Admin Only) -->
                        <a href="{{ route('analytics') }}"
                           class="flex items-center justify-between px-3.5 py-3 rounded-xl transition-all duration-150 {{ request()->routeIs('analytics*') ? 'bg-[#142366] text-white font-bold shadow-sm border border-amber-400/30' : 'text-[#8a9ecf] hover:text-white hover:bg-[#121f57] font-medium' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[13px] tracking-wider uppercase">ANALYTICS & BI</span>
                                    <span class="text-[9px] bg-amber-400/20 text-amber-300 font-extrabold px-1.5 py-0.5 rounded border border-amber-400/40">PRO</span>
                                </div>
                            </div>
                            @if(request()->routeIs('analytics*'))
                                <span class="w-2 h-2 rounded-full bg-[#10b981] shadow-[0_0_8px_#10b981]"></span>
                            @endif
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Bottom Section (User Role Card) -->
            <div class="p-5 border-t border-[#1c2a68]/70">
                @php
                    $currentUser = Auth::user();
                    $userRoleName = $currentUser ? ($currentUser->roles->first()?->name ?? 'secretary') : 'secretary';
                    $roleLabel = match($userRoleName) {
                        'admin' => 'ADMIN',
                        'manager' => 'MANAGER',
                        default => 'SECRETARY'
                    };
                    $userTitle = match($userRoleName) {
                        'admin' => 'System Admin',
                        'manager' => 'Operations Manager',
                        default => 'Sales Secretary'
                    };
                    $badgeInitials = match($userRoleName) {
                        'admin' => 'AD',
                        'manager' => 'MG',
                        default => 'SE'
                    };
                @endphp

                <div class="bg-[#13205c] border border-white/5 rounded-2xl p-3.5 shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#10b981] text-white font-black text-sm flex items-center justify-center flex-shrink-0 shadow-sm">
                            {{ $badgeInitials }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-white text-[13px] font-bold truncate">
                                {{ $userTitle }}
                            </h4>
                            <span class="inline-block mt-0.5 bg-[#064e3b] text-[#34d399] text-[9px] font-extrabold tracking-wider px-2 py-0.5 rounded uppercase">
                                {{ $roleLabel }}
                            </span>
                        </div>
                    </div>

                    <button type="button"
                            @click="switchRoleModal = true"
                            class="w-full mt-3 pt-2.5 border-t border-white/5 text-[#768bc4] hover:text-white text-[12px] font-semibold flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span>Switch Role</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- BACKDROP FOR MOBILE SIDEBAR -->
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 z-30 lg:hidden">
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 min-w-0 lg:pl-64 flex flex-col min-h-screen w-full">
            
            <!-- TOP HEADER (1:1 with Figma Design) -->
            <header class="sticky top-0 z-20 bg-[#f4f7fc]/90 backdrop-blur-md px-4 sm:px-6 lg:px-10 py-5 flex items-center justify-between border-b border-slate-200/60 w-full">
                <!-- Page Title & Date -->
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger -->
                    <button type="button"
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 text-slate-600 hover:text-slate-900 bg-white rounded-xl shadow-sm border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div>
                        <h2 class="text-[#142259] text-2xl lg:text-[28px] font-black uppercase tracking-tight leading-none">
                            @yield('title', 'DASHBOARD')
                        </h2>
                        <p class="text-[#8a9bbd] text-xs font-semibold mt-1">
                            {{ now()->format('l, F j, Y') }}
                        </p>
                    </div>
                </div>

                <!-- Right Top Controls -->
                <div class="flex items-center gap-3">
                    <!-- Search Icon Button -->
                    <button type="button"
                            class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[#556987] hover:text-[#142259] hover:border-slate-300 shadow-sm transition-all cursor-pointer"
                            title="Global Search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    <!-- Alerts Pill Badge -->
                    @php
                        $alertCount = \App\Models\Product::where('status', 'active')->where('quantity_in_stock', '<=', 4)->count();
                    @endphp
                    <a href="{{ route('inventory') }}#alerts"
                       class="bg-red-50/90 hover:bg-red-100/90 text-[#dc2626] border border-red-200/80 rounded-full px-3.5 py-2 text-xs font-black flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                        <!-- Hazard Triangle Icon -->
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18h.01"/>
                        </svg>
                        <span>{{ $alertCount }} alerts</span>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="w-10 h-10 rounded-full bg-[#0d1747] text-white font-black text-xs flex items-center justify-center border-2 border-white shadow-md hover:ring-2 hover:ring-blue-400 transition-all cursor-pointer">
                            AM
                        </button>

                        <div x-show="userMenuOpen"
                             @click.away="userMenuOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-800">{{ Auth::user()?->name ?? 'User' }}</p>
                                <p class="text-slate-400 text-[11px] truncate">{{ Auth::user()?->email ?? '' }}</p>
                            </div>
                            <button @click="switchRoleModal = true; userMenuOpen = false;"
                                    class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Switch Role
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-10 max-w-[1600px] w-full mx-auto">
                {{ $slot }}
            </main>
        </div>

        <!-- SWITCH ROLE MODAL (Allows instant switching between Admin, Manager, and Secretary) -->
        <div x-show="switchRoleModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div @click.away="switchRoleModal = false"
                 class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-[#142259] font-black text-lg">Switch Active Role</h3>
                    <button @click="switchRoleModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="space-y-3 mt-5">
                    <!-- Admin -->
                    <form method="POST" action="/switch-role/admin">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 transition cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-xs">AD</span>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">System Admin</p>
                                    <p class="text-slate-400 text-xs">Full access (Manage Users, Archive, Inventory, Sales)</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-blue-600">Select →</span>
                        </button>
                    </form>

                    <!-- Manager -->
                    <form method="POST" action="/switch-role/manager">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 transition cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">MG</span>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">Operations Manager</p>
                                    <p class="text-slate-400 text-xs">Manage inventory, sales, stock transfers & reports</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-indigo-600">Select →</span>
                        </button>
                    </form>

                    <!-- Secretary -->
                    <form method="POST" action="/switch-role/secretary">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-[#10b981] text-white font-bold flex items-center justify-center text-xs">SE</span>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">Sales Secretary</p>
                                    <p class="text-slate-400 text-xs">Manage inventory, record sales transactions</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">Select →</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
