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

        <!-- Theme Enforcement: Corporate Light Mode -->
        <script>
            document.documentElement.classList.remove('dark');
            localStorage.removeItem('theme');
        </script>
        <script src="{{ asset('js/apexcharts.min.js') }}"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex overflow-x-hidden transition-colors duration-150"
          x-data="{ 
              sidebarOpen: false
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
                       wire:navigate
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
                       wire:navigate
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('inventory') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Inventory</span>
                        </div>
                        @if(($navPendingStockRequestsCount ?? 0) > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-xs" title="{{ $navPendingStockRequestsCount }} pending requisition(s)">
                                {{ $navPendingStockRequestsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Add Inventory Link -->
                    <a href="{{ route('inventory.add') }}"
                       wire:navigate
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('inventory.add*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add Inventory</span>
                        </div>
                    </a>

                    <!-- Sales Link -->
                    <a href="{{ route('sales') }}"
                       wire:navigate
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
                        $activeLayawaysCount = cache()->remember('active_layaways_count', 30, fn() => \App\Models\Sale::where('is_archived', false)->where('status', 'layaway')->count());
                    @endphp
                    <a href="{{ route('layaways') }}"
                       wire:navigate
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('layaways*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Lay-Aways</span>
                        </div>
                        @if($activeLayawaysCount > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-xs" title="{{ $activeLayawaysCount }} active layaway contract(s)">
                                {{ $activeLayawaysCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Customers Link -->
                    <a href="{{ route('customers') }}"
                       wire:navigate
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
                    <!-- Categories Link -->
                    <a href="{{ route('categories') }}"
                       wire:navigate
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('categories*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Categories</span>
                        </div>
                    </a>

                    <!-- Promotions Link -->
                    <a href="{{ route('promotions') }}"
                       wire:navigate
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
                       wire:navigate
                       class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('reports*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Reports & Ledger</span>
                        </div>
                    </a>
                </nav>

                @if(Auth::user()?->hasRole('admin'))
                    <!-- Navigation Hierarchy: Group 3 Administration -->
                    <div class="mt-4 mb-2">
                        <p class="text-slate-400 text-[10px] font-semibold tracking-wider uppercase px-3">
                            Administration
                        </p>
                    </div>

                    <nav class="space-y-1">
                        <!-- Backup & Recovery Link -->
                        <a href="{{ route('backup') }}"
                           wire:navigate
                           class="flex items-center justify-between px-3 py-2 text-xs tracking-wide transition-colors {{ request()->routeIs('backup*') ? 'bg-[#182a68] border-l-2 border-white text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-[#142259]/30 font-medium' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                                <span>Backup & Recovery</span>
                            </div>
                        </a>
                    </nav>
                @endif
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

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-7 h-7 rounded bg-[#1a2858] text-slate-200 font-bold text-[11px] flex items-center justify-center flex-shrink-0">
                            {{ $badgeInitials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-white text-xs font-semibold truncate leading-tight">{{ $currentUser?->name ?? $userTitle }}</div>
                            <div class="text-slate-400 text-[10px] font-mono tracking-wider uppercase truncate">{{ $roleLabel }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <form method="POST" action="{{ route('logout') }}" class="inline m-0">
                            @csrf
                            <button type="submit"
                                    class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-[#1a2858] rounded transition-colors cursor-pointer flex items-center justify-center"
                                    title="Sign Out"
                                    aria-label="Sign Out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
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
                    @if(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('manager'))
                        <!-- Stock Requisitions Notification Bell -->
                        <div class="relative" x-data="{ notifOpen: false }">
                            <button type="button"
                                    @click="notifOpen = !notifOpen"
                                    class="relative w-9 h-9 rounded bg-white dark:bg-[#121f4a] border border-slate-200 dark:border-slate-700/80 flex items-center justify-center text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:border-slate-600 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-slate-400"
                                    title="Stock Requisition Alerts"
                                    aria-label="Stock Requisition Alerts">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(($navPendingStockRequestsCount ?? 0) > 0)
                                    <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-amber-500 text-white text-[9px] font-bold shadow-xs animate-pulse">
                                        {{ $navPendingStockRequestsCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notification Dropdown Menu -->
                            <div x-show="notifOpen"
                                 @click.away="notifOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-80 sm:w-88 bg-white dark:bg-[#0c163b] rounded shadow-xl border border-slate-200 dark:border-[#1a2858] py-1.5 z-50 text-xs overflow-hidden"
                                 style="display: none;">
                                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-[#1a2858] flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        <span>Stock Requisitions</span>
                                    </div>
                                    @if(($navPendingStockRequestsCount ?? 0) > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900">
                                            {{ $navPendingStockRequestsCount }} pending
                                        </span>
                                    @endif
                                </div>

                                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse(($navPendingStockRequestsList ?? []) as $notifReq)
                                        <a href="{{ route('inventory', ['open_requests' => 1]) }}"
                                           class="block px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-[#121f4a] transition-colors group">
                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <span class="font-mono font-bold text-[#142259] dark:text-blue-400 text-xs">{{ $notifReq->request_number }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $notifReq->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="font-semibold text-slate-900 dark:text-white text-xs truncate group-hover:text-[#142259] dark:group-hover:text-blue-300">
                                                {{ $notifReq->product?->name ?? 'Product' }}
                                            </p>
                                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                <span>Qty: <strong class="text-slate-800 dark:text-slate-200">{{ $notifReq->quantity_requested }} units</strong></span>
                                                <span class="text-[10px] text-slate-400">By {{ $notifReq->requester?->name ?? 'Secretary' }}</span>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-xs text-slate-400">
                                            <svg class="w-6 h-6 mx-auto text-slate-300 dark:text-slate-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                            <p>No pending requisitions.</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">All stock requests are up to date.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="p-2 border-t border-slate-100 dark:border-[#1a2858] bg-slate-50/50 dark:bg-[#0f1b40]/50">
                                    <a href="{{ route('inventory', ['open_requests' => 1]) }}"
                                       class="w-full text-center py-1.5 px-3 rounded bg-slate-100 hover:bg-slate-200 dark:bg-[#142259] dark:hover:bg-[#1a2c73] text-[#142259] dark:text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5">
                                        <span>Open Procurement Hub</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 min-w-0 p-6 max-w-[1536px] w-full mx-auto">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
