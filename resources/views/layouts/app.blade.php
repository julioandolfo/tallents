<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tallents RH</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @include('layouts.partials.theme-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full dark:bg-slate-900" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Overlay mobile -->
        <div x-show="sidebarOpen"
             x-cloak
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             @click="sidebarOpen = false"></div>

        <!-- SIDEBAR DESKTOP -->
        <div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 app-sidebar z-50">
            <div class="flex items-center h-16 px-4 app-sidebar-accent shrink-0">
                <svg class="h-7 w-7 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-gray-900 dark:text-white font-bold text-xl">Tallents <span class="text-indigo-600 dark:text-indigo-400">RH</span></span>
            </div>
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </nav>
            <div class="px-4 py-3 app-sidebar-accent shrink-0">
                <p class="text-xs text-gray-500">Tallents RH v1.0</p>
            </div>
        </div>

        <!-- SIDEBAR MOBILE -->
        <div class="flex flex-col w-64 app-sidebar fixed inset-y-0 z-50 transition-transform duration-300 lg:hidden"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
             x-cloak>
            <div class="flex items-center justify-between h-16 px-4 app-sidebar-accent shrink-0">
                <span class="text-gray-900 dark:text-white font-bold text-xl">Tallents <span class="text-indigo-600 dark:text-indigo-400">RH</span></span>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </nav>
            <div class="px-4 py-3 app-sidebar-accent shrink-0">
                <p class="text-xs text-gray-500">Tallents RH v1.0</p>
            </div>
        </div>

        <!-- CONTEÚDO PRINCIPAL -->
        <div class="flex flex-col flex-1 overflow-hidden lg:pl-64">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200 z-10 shrink-0">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="hidden lg:block">
                        <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <!-- User menu -->
                    <div class="flex items-center gap-3 ml-auto" x-data="{ userMenuOpen: false }">
                        @include('layouts.partials.theme-switcher')
                        <span class="text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 focus:outline-none">
                                @if(isset(auth()->user()->foto) && auth()->user()->foto)
                                    <img src="{{ Storage::url(auth()->user()->foto) }}"
                                         class="h-8 w-8 rounded-full object-cover ring-2 ring-indigo-500">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="userMenuOpen"
                                 x-cloak
                                 @click.away="userMenuOpen = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                                <a href="{{ route('perfil.index') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Meu Perfil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 p-4"
                         x-data x-init="setTimeout(() => $el.remove(), 5000)">
                        <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 flex items-center gap-3 rounded-lg bg-red-50 border border-red-200 p-4"
                         x-data x-init="setTimeout(() => $el.remove(), 5000)">
                        <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-red-700 mb-1">Por favor corrija os erros abaixo:</p>
                                <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Conteúdo da página -->
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
