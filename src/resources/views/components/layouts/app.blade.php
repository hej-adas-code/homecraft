<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HomeCraft' }} - HomeCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-slate-900 text-white flex flex-col min-h-screen flex-shrink-0">
        <div class="px-6 py-5 border-b border-slate-700">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">H</div>
                <span class="text-xl font-bold tracking-tight">HomeCraft</span>
            </a>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            @php
                $navItem = function(string $route, string $icon, string $label, string $currentRoute = '') use (&$navItem) {
                    $active = request()->routeIs($route . '*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white';
                    return "<a href=\"" . route($route) . "\" class=\"flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors $active\">
                        <span class=\"text-base\">$icon</span>
                        <span>$label</span>
                    </a>";
                };
            @endphp

            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pb-1">Główne</div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">🏠</span><span>Dashboard</span>
            </a>
            <a href="{{ route('timeline') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('timeline*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">📰</span><span>Oś czasu</span>
            </a>

            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pb-1 pt-4">Finanse</div>
            <a href="{{ route('budget.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('budget*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">💰</span><span>Budżet</span>
            </a>
            <a href="{{ route('estimates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('estimates*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">📋</span><span>Kosztorysy</span>
            </a>
            <a href="{{ route('offers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('offers*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">🤝</span><span>Oferty</span>
            </a>

            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pb-1 pt-4">Projekt</div>
            <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('documents*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">📁</span><span>Dokumenty</span>
            </a>
            <a href="{{ route('plots.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('plots*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">🗺️</span><span>Działka</span>
            </a>
            <a href="{{ route('milestones.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('milestones*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">📅</span><span>Harmonogram</span>
            </a>

            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pb-1 pt-4">Inne</div>
            <a href="{{ route('meetings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('meetings*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">🤝</span><span>Spotkania</span>
            </a>
            <a href="{{ route('ideas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('ideas*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">💡</span><span>Pomysły</span>
            </a>
            <a href="{{ route('contacts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('contacts*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span class="text-base">👥</span><span>Kontakty</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-slate-700">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-white text-xs">⚙️</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                    Wyloguj się
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-auto">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
            <div class="flex items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-8 mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-8 mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 px-8 py-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
