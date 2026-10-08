<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Wella Metal Corporation') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts: Plus Jakarta Sans for unified corporate typography -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <script src="{{ asset('js/apexcharts.min.js') }}"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#070d1e] text-slate-800 dark:text-slate-100 min-h-screen flex overflow-x-hidden transition-colors duration-150"
          x-data="{ 
              sidebarOpen: false, 
              switchRoleModal: false,
              userMenuOpen: false,
              darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
          }">

        <!-- SIDEBAR -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-[#0c163b] text-white flex flex-col justify-between transition-transform duration-200 lg:translate-x-0 border-r border-[#1a2858]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Top Section -->
            <div class="p-5 flex-1 flex flex-col overflow-y-auto">
                <!-- Branding -->
                <div class="flex items-center gap-3 pb-5 border-b border-[#1c2a68]">
                    <img src="{{ asset('images/logo.png') }}" alt="Wella Metal Corporation" class="w-11 h-11 object-contain flex-shrink-0">
                    <div>
                        <h1 class="text-white text-xs font-bold tracking-wider leading-tight uppercase">
                            Wella Metal
                        </h1>
                        <h1 class="text-white text-xs font-bold tracking-wider leading-tight uppercase">
                            Corporation
                        </h1>
                        <p class="text-slate-400 text-[11px] font-medium leading-none mt-1">
                            Management Platform
                        </p>
                    </div>
                </div>

                <!-- Navigation Hierarchy: Group 1 Operations -->
                <div class="mt-4 mb-2">
                    <p class="text-slate-400 text-[10px] font-semibold tracking-wider uppercase px-3">
                        Operations
                    </p>
                </div>

                <!-- Nav Menu Items -->
                <nav class="space-y-1">
                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('dashboard*') || request()->routeIs('admin.dashboard*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                                <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                            </svg>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <!-- Inventory Link -->
                    <a href="{{ route('inventory') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('inventory*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Inventory</span>
                        </div>
                    </a>

                    <!-- Sales Link -->
                    <a href="{{ route('sales') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('sales*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2"/>
                            </svg>
                            <span>Sales & POS</span>
                        </div>
                    </a>

                    <!-- Lay-Aways Link -->
                    @php
                        $activeLayawaysCount = \App\Models\Sale::where('is_archived', false)->where('status', 'layaway')->count();
                    @endphp
                    <a href="{{ route('layaways') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('layaways*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Lay-Aways</span>
                        </div>
                        @if($activeLayawaysCount > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                {{ $activeLayawaysCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Customers Link -->
                    <a href="{{ route('customers') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('customers*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Customers</span>
                        </div>
                    </a>
                </nav>

                <!-- Navigation Hierarchy: Group 2 Management & Reporting -->
                <div class="mt-4 mb-2">
                    <p class="text-slate-400 text-[10px] font-semibold tracking-wider uppercase px-3">
                        Management
                    </p>
                </div>

                <nav class="space-y-1">
                    <!-- Promotions Link -->
                    <a href="{{ route('promotions') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('promotions*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Promotions</span>
                        </div>
                    </a>

                    <!-- Reports Link -->
                    <a href="{{ route('reports') }}"
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('reports*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Reports & Ledger</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- Bottom Section (User Role) -->
            <div class="px-4 py-4 border-t border-[#1c2a68]">
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

                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded bg-[#1a2858] text-slate-200 font-bold text-[11px] flex items-center justify-center flex-shrink-0">
                            {{ $badgeInitials }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-white text-xs font-semibold truncate leading-tight">{{ $userTitle }}</div>
                            <div class="text-slate-400 text-[11px] font-mono tracking-wider uppercase">{{ $roleLabel }}</div>
                        </div>
                    </div>
                    @if(!$currentUser?->hasRole('secretary'))
                        <button type="button" @click="switchRoleModal = true" class="text-slate-400 hover:text-white transition-colors cursor-pointer flex-shrink-0" title="Switch workspace role">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </button>
                    @endif
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
             class="fixed inset-0 bg-slate-950/60 z-30 lg:hidden">
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 min-w-0 lg:pl-64 flex flex-col min-h-screen w-full">
            
            <!-- TOP HEADER -->
            <header class="sticky top-0 z-20 bg-white/95 dark:bg-[#0c163b]/95 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between border-b border-slate-200 dark:border-[#1a2858] w-full shadow-sm transition-colors duration-150">
                <!-- Page Title -->
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button type="button"
                            @click="sidebarOpen = !sidebarOpen"
                            aria-label="Open navigation sidebar"
                            class="lg:hidden p-1.5 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-white dark:bg-[#121f4a] rounded border border-slate-200 dark:border-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white leading-tight">
                            @yield('title', 'Dashboard')
                        </h2>
                    </div>
                </div>

                <!-- Right Top Controls -->
                <div class="flex items-center gap-2.5">
                    <!-- Light / Dark Mode Toggle Button -->
                    <button type="button"
                            @click="
                                darkMode = !darkMode;
                                if (darkMode) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.theme = 'dark';
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.theme = 'light';
                                }
                                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: darkMode } }));
                            "
                            class="w-9 h-9 rounded bg-white dark:bg-[#121f4a] border border-slate-200 dark:border-slate-700/80 flex items-center justify-center text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-600 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-slate-400"
                            title="Toggle Light / Dark mode"
                            aria-label="Toggle Light / Dark mode">
                        <!-- Sun icon for dark mode -->
                        <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon for light mode -->
                        <svg class="w-4 h-4 block dark:hidden text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>


                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="w-9 h-9 rounded bg-[#142259] text-white font-bold text-xs flex items-center justify-center border border-slate-700 dark:border-[#1a2858] hover:bg-[#0e1840] transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-slate-400"
                                aria-label="User account menu">
                            {{ strtoupper(substr(Auth::user()?->name ?? 'WM', 0, 2)) }}
                        </button>

                        <div x-show="userMenuOpen"
                             @click.away="userMenuOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#0c163b] rounded shadow-md border border-slate-200 dark:border-[#1a2858] py-1.5 z-50 text-xs">
                            <div class="px-3.5 py-2 border-b border-slate-100 dark:border-[#1a2858]">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ Auth::user()?->name ?? 'User' }}</p>
                                <p class="text-slate-500 dark:text-slate-400 text-[11px] truncate">{{ Auth::user()?->email ?? '' }}</p>
                            </div>
                            @if(!Auth::user()?->hasRole('secretary'))
                                <button @click="switchRoleModal = true; userMenuOpen = false;"
                                        class="w-full text-left px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-[#132057] text-slate-700 dark:text-slate-300 font-medium flex items-center gap-2 cursor-pointer transition-colors">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    Switch Role
                                </button>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-3.5 py-2 text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 font-medium flex items-center gap-2 cursor-pointer transition-colors">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 min-w-0 p-6 max-w-[1536px] w-full mx-auto">
                {{ $slot }}
            </main>
        </div>

        @if(!Auth::user()?->hasRole('secretary'))
        <!-- SWITCH ROLE MODAL -->
        <div x-show="switchRoleModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div @click.away="switchRoleModal = false"
                 class="bg-white dark:bg-[#0c163b] rounded-md max-w-md w-full shadow-lg border border-slate-200 dark:border-[#1a2858] relative animate-in fade-in zoom-in-95 duration-150">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Switch workspace role</h3>
                    <button @click="switchRoleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer" aria-label="Close dialog">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 space-y-2.5">
                    <!-- Admin -->
                    <form method="POST" action="/switch-role/admin">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3 rounded border border-slate-200 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-[#132057] transition-colors cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded bg-[#142259] text-white font-bold flex items-center justify-center text-xs">AD</span>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white text-xs">System Admin</p>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px]">Full access (Users, Archive, Inventory, Sales, Reports)</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Select</span>
                        </button>
                    </form>

                    <!-- Manager -->
                    <form method="POST" action="/switch-role/manager">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3 rounded border border-slate-200 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-[#132057] transition-colors cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded bg-[#142259] text-white font-bold flex items-center justify-center text-xs">MG</span>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white text-xs">Operations Manager</p>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px]">Inventory, sales, stock transfers & reports</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Select</span>
                        </button>
                    </form>

                    <!-- Secretary -->
                    <form method="POST" action="/switch-role/secretary">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3 rounded border border-slate-200 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-[#132057] transition-colors cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded bg-emerald-700 text-white font-bold flex items-center justify-center text-xs">SE</span>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white text-xs">Sales Secretary</p>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px]">Manage inventory, record sales transactions</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Select</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @livewireScripts
        @stack('scripts')
    </body>
</html>
