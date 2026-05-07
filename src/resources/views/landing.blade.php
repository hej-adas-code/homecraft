<!DOCTYPE html>
<html lang="pl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeCraft — Panuj nad swoją budową</title>
    <meta name="description" content="Kosztorysy, budżet, dokumenty, oferty i interaktywna mapa działki — wszystko w jednym miejscu dla budujących własny dom.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom CSS variables and overrides */
        :root {
            --indigo-glow: rgba(99, 102, 241, 0.15);
        }

        /* Smooth scroll offset for sticky nav */
        html { scroll-padding-top: 72px; }

        /* Grain overlay for hero */
        .hero-grain::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            opacity: 0.4;
        }

        /* Animated gradient orbs */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }
        @keyframes float-medium {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            33% { transform: translateY(-20px) translateX(15px); }
            66% { transform: translateY(10px) translateX(-10px); }
        }
        .orb-1 { animation: float-slow 12s ease-in-out infinite; }
        .orb-2 { animation: float-medium 9s ease-in-out infinite; }
        .orb-3 { animation: float-slow 15s ease-in-out infinite reverse; }

        /* Fade-in animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-1 { animation: fadeInUp 0.7s ease-out 0.1s both; }
        .fade-in-2 { animation: fadeInUp 0.7s ease-out 0.25s both; }
        .fade-in-3 { animation: fadeInUp 0.7s ease-out 0.4s both; }
        .fade-in-4 { animation: fadeInUp 0.7s ease-out 0.55s both; }
        .fade-in-5 { animation: fadeInUp 0.7s ease-out 0.7s both; }
        .fade-in-6 { animation: fadeInUp 0.7s ease-out 0.85s both; }

        /* FAQ accordion pure CSS */
        .faq-item input[type="checkbox"] { display: none; }
        .faq-item .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding 0.35s ease;
        }
        .faq-item input[type="checkbox"]:checked ~ .faq-answer {
            max-height: 200px;
        }
        .faq-item .faq-icon {
            transition: transform 0.3s ease;
        }
        .faq-item input[type="checkbox"]:checked ~ label .faq-icon {
            transform: rotate(45deg);
        }
        /* Simpler: the icon is inside the label */
        .faq-toggle:has(input:checked) .faq-icon { transform: rotate(45deg); }

        /* Card hover */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Mockup screen shimmer */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .shimmer-bar {
            animation: shimmer 2.5s ease-in-out infinite;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
        }

        /* Compass animation */
        @keyframes compass-rotate {
            0%, 100% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
        }
        .compass-needle { animation: compass-rotate 3s ease-in-out infinite; }

        /* Sun ray pulse */
        @keyframes sun-pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        .sun-ray { animation: sun-pulse 2s ease-in-out infinite; }

        /* Gradient text utility (for Tailwind JIT) */
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glassmorphism nav */
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Problem card connector line */
        .connector-line {
            background: linear-gradient(90deg, #e0e7ff, #a5b4fc, #e0e7ff);
        }

        /* Stats counter glow */
        .stat-number {
            text-shadow: 0 0 30px rgba(99, 102, 241, 0.5);
        }

        /* Map mockup styles */
        .map-parcel {
            clip-path: polygon(8% 15%, 92% 8%, 95% 88%, 5% 92%);
        }
        .house-shadow {
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3));
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.7; }
            70% { transform: scale(1.1); opacity: 0; }
            100% { transform: scale(0.95); opacity: 0; }
        }
        .pulse-dot::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.4);
            animation: pulse-ring 2s ease-out infinite;
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-900 overflow-x-hidden">

{{-- ===================== NAVIGATION ===================== --}}
<header class="glass-nav sticky top-0 z-50 border-b border-gray-100/80">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-0 flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-lg flex items-center justify-center text-white font-black text-base shadow-md shadow-indigo-200 group-hover:shadow-indigo-300 transition-shadow">H</div>
            <span class="text-lg font-black text-gray-900 tracking-tight">HomeCraft</span>
        </a>

        {{-- Desktop nav links --}}
        <nav class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Funkcje</a>
            <a href="#how-it-works" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Jak to działa</a>
            <a href="#faq" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">FAQ</a>
        </nav>

        {{-- CTA buttons --}}
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                    Przejdź do aplikacji →
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors px-3 py-2">
                    Logowanie
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                    Zacznij za darmo
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- ===================== HERO ===================== --}}
<section class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden hero-grain"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #0f172a 100%);">

    {{-- Animated background orbs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="orb-1 absolute top-1/4 left-1/6 w-96 h-96 rounded-full opacity-20"
             style="background: radial-gradient(circle, rgba(99,102,241,0.6) 0%, transparent 70%); filter: blur(40px);"></div>
        <div class="orb-2 absolute bottom-1/4 right-1/6 w-80 h-80 rounded-full opacity-15"
             style="background: radial-gradient(circle, rgba(34,211,238,0.5) 0%, transparent 70%); filter: blur(50px);"></div>
        <div class="orb-3 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-10"
             style="background: radial-gradient(circle, rgba(139,92,246,0.4) 0%, transparent 70%); filter: blur(80px);"></div>
        {{-- Grid lines --}}
        <div class="absolute inset-0 opacity-5"
             style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 60px 60px;"></div>
    </div>

    <div class="relative z-10 w-full max-w-5xl mx-auto px-5 sm:px-8 text-center py-20 pt-16">

        {{-- Badge --}}
        <div class="fade-in-1 inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide mb-8"
             style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #a5b4fc;">
            🏗️ Narzędzie dla budujących własny dom
        </div>

        {{-- H1 --}}
        <h1 class="fade-in-2 text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-[1.05] tracking-tight mb-6">
            Panuj nad swoją budową.<br>
            <span class="gradient-text">Od działki do odbioru.</span>
        </h1>

        {{-- Subtitle --}}
        <p class="fade-in-3 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            Kosztorysy, budżet, dokumenty, oferty i interaktywna mapa działki —<br class="hidden sm:block">
            wszystko w jednym miejscu.
        </p>

        {{-- CTA Buttons --}}
        <div class="fade-in-4 flex flex-col sm:flex-row gap-4 justify-center items-center mb-10">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl text-base hover:bg-indigo-500 transition-all duration-300 shadow-xl shadow-indigo-900/50 hover:shadow-indigo-900/70 hover:-translate-y-0.5">
                    Przejdź do aplikacji →
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl text-base hover:bg-indigo-500 transition-all duration-300 shadow-xl shadow-indigo-900/50 hover:shadow-indigo-900/70 hover:-translate-y-0.5">
                    Zacznij za darmo →
                </a>
                <a href="#features"
                   class="w-full sm:w-auto px-8 py-4 text-white font-semibold rounded-xl text-base transition-all duration-300 hover:-translate-y-0.5"
                   style="border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05);">
                    Zobacz funkcje
                </a>
            @endauth
        </div>

        {{-- Trust signals --}}
        <div class="fade-in-5 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-slate-500 mb-14">
            <span class="flex items-center gap-1.5"><span class="text-base">🔒</span> Bezpłatny dostęp</span>
            <span class="w-px h-4 bg-slate-700 hidden sm:block"></span>
            <span class="flex items-center gap-1.5"><span class="text-base">📱</span> Działa na telefonie</span>
            <span class="w-px h-4 bg-slate-700 hidden sm:block"></span>
            <span class="flex items-center gap-1.5"><span class="text-base">🇵🇱</span> Polskie API geoportalu</span>
        </div>

        {{-- App mockup --}}
        <div class="fade-in-6 relative mx-auto max-w-3xl">
            {{-- Glow behind mockup --}}
            <div class="absolute -inset-4 rounded-3xl opacity-30 blur-2xl"
                 style="background: linear-gradient(135deg, rgba(99,102,241,0.6), rgba(34,211,238,0.4));"></div>

            {{-- Browser chrome --}}
            <div class="relative rounded-2xl overflow-hidden shadow-2xl"
                 style="border: 1px solid rgba(255,255,255,0.1); background: #1e1b4b;">
                {{-- Browser top bar --}}
                <div class="flex items-center gap-2 px-4 py-3"
                     style="background: rgba(15,15,35,0.8); border-bottom: 1px solid rgba(255,255,255,0.06);">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/70"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                    </div>
                    <div class="flex-1 mx-4">
                        <div class="h-5 rounded-md px-3 flex items-center"
                             style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                            <span class="text-xs text-slate-500">app.homecraft.pl/dashboard</span>
                        </div>
                    </div>
                </div>

                {{-- App content mockup --}}
                <div class="p-4 sm:p-5" style="background: #0f172a;">
                    {{-- Top stats row --}}
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="rounded-xl p-3" style="background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.2);">
                            <div class="text-xs text-slate-500 mb-1">Budżet całkowity</div>
                            <div class="text-lg font-black text-indigo-300">480 000 zł</div>
                            <div class="text-xs text-emerald-400 mt-0.5">↑ 12% poniżej planu</div>
                        </div>
                        <div class="rounded-xl p-3" style="background: rgba(34,211,238,0.08); border: 1px solid rgba(34,211,238,0.15);">
                            <div class="text-xs text-slate-500 mb-1">Wydano</div>
                            <div class="text-lg font-black text-cyan-300">187 240 zł</div>
                            <div class="w-full bg-slate-700 rounded-full h-1 mt-2">
                                <div class="h-1 rounded-full bg-gradient-to-r from-cyan-400 to-indigo-400" style="width: 39%"></div>
                            </div>
                        </div>
                        <div class="rounded-xl p-3" style="background: rgba(168,85,247,0.08); border: 1px solid rgba(168,85,247,0.15);">
                            <div class="text-xs text-slate-500 mb-1">Dokumenty</div>
                            <div class="text-lg font-black text-purple-300">24</div>
                            <div class="text-xs text-slate-500 mt-0.5">pliki · 5 kategorii</div>
                        </div>
                    </div>

                    {{-- Two column lower area --}}
                    <div class="grid grid-cols-5 gap-3">
                        {{-- Chart mockup --}}
                        <div class="col-span-3 rounded-xl p-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
                            <div class="text-xs font-semibold text-slate-400 mb-3">Wydatki wg kategorii</div>
                            <div class="space-y-2">
                                <div>
                                    <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Fundamenty</span><span class="text-slate-300">58 000 zł</span></div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full" style="width: 72%; background: linear-gradient(90deg, #6366f1, #818cf8);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Dach</span><span class="text-slate-300">42 500 zł</span></div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full" style="width: 52%; background: linear-gradient(90deg, #22d3ee, #67e8f9);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Instalacje</span><span class="text-slate-300">31 200 zł</span></div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full" style="width: 38%; background: linear-gradient(90deg, #a855f7, #c084fc);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Okna i drzwi</span><span class="text-slate-300">22 800 zł</span></div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full" style="width: 28%; background: linear-gradient(90deg, #10b981, #34d399);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent activity --}}
                        <div class="col-span-2 rounded-xl p-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
                            <div class="text-xs font-semibold text-slate-400 mb-3">Ostatnie oferty</div>
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background: rgba(99,102,241,0.2);">🤝</div>
                                    <div>
                                        <div class="text-xs text-slate-300 font-medium">Firma Jan-Bud</div>
                                        <div class="text-xs text-slate-600">tynki · 18 200 zł</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background: rgba(34,211,238,0.15);">📋</div>
                                    <div>
                                        <div class="text-xs text-slate-300 font-medium">Kosztorys PV</div>
                                        <div class="text-xs text-slate-600">fotowoltaika · 28kWp</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background: rgba(168,85,247,0.15);">📁</div>
                                    <div>
                                        <div class="text-xs text-slate-300 font-medium">Projekt budowlany</div>
                                        <div class="text-xs text-slate-600">PDF · 12.4 MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-slate-600 text-xs tracking-widest uppercase">
        <span>Przewiń</span>
        <div class="w-px h-8 bg-gradient-to-b from-slate-600 to-transparent"></div>
    </div>
</section>

{{-- ===================== PROBLEM / SOLUTION ===================== --}}
<section id="how-it-works" class="py-24 px-5 sm:px-8 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm tracking-widest uppercase mb-3">Problem i rozwiązanie</p>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Koniec z chaosem budowy</h2>
            <p class="text-gray-500 mt-4 max-w-xl mx-auto">Budowa domu to setki decyzji, dokumentów i wydatków. HomeCraft zamienia chaos w porządek.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="p-6 pb-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold mb-4">
                        ✕ Problem
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Arkusze kalkulacyjne</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">Excel z dziesiątkami zakładek, formuły się psują, wersje pliku wszędzie.</p>
                </div>
                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mx-6"></div>
                <div class="p-6 pt-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold mb-4">
                        ✓ Rozwiązanie
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Budżet z kategoriami</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Przejrzysty budżet podzielony na etapy, automatyczne podsumowania i wykresy postępu.</p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500"></div>
            </div>

            {{-- Card 2 --}}
            <div class="relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="p-6 pb-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold mb-4">
                        ✕ Problem
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Papiery wszędzie</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">Faktury w szufladzie, umowy w mailach, projekty na dysku — nie wiadomo co gdzie.</p>
                </div>
                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mx-6"></div>
                <div class="p-6 pt-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold mb-4">
                        ✓ Rozwiązanie
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Dokumenty w chmurze</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Wszystkie pliki posegregowane w kategorii, dostępne z każdego urządzenia, bezpiecznie.</p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500"></div>
            </div>

            {{-- Card 3 --}}
            <div class="relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="p-6 pb-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold mb-4">
                        ✕ Problem
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg mb-2">Gdzie mój dom na działce?</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">Odległości od granicy, orientacja słońca, linie zabudowy — wszystko na pamięć lub rysunki.</p>
                </div>
                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mx-6"></div>
                <div class="p-6 pt-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold mb-4">
                        ✓ Rozwiązanie
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Interaktywna mapa z ULDK</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Pobierz granice działki z GUGiK, nałóż dom i obserwuj jak słońce pada na dach.</p>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-cyan-500"></div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== FEATURES GRID ===================== --}}
<section id="features" class="py-24 px-5 sm:px-8 bg-slate-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm tracking-widest uppercase mb-3">Funkcje</p>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Wszystko czego potrzebujesz</h2>
            <p class="text-gray-500 mt-4 max-w-xl mx-auto">Jeden zestaw narzędzi zamiast tuzina aplikacji. Zaprojektowany specjalnie dla budujących dom w Polsce.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {{-- Feature 1 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(99,102,241,0.05));">💰</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Budżet i finanse</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Planuj wydatki etapami, śledź postęp i kontroluj ile jeszcze zostało do końca budowy.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(34,211,238,0.1), rgba(34,211,238,0.05));">📋</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Kosztorysy</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Twórz szczegółowe kosztorysy materiałów i robocizny dla każdego etapu budowy.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(168,85,247,0.1), rgba(168,85,247,0.05));">🤝</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Oferty</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Zbieraj oferty od wykonawców, porównuj je i wybieraj najlepszą opcję dla swojej budowy.</p>
            </div>

            {{-- Feature 4 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(16,185,129,0.05));">📁</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Dokumenty</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Przechowuj projekty, pozwolenia, faktury i umowy posegregowane w jednym bezpiecznym miejscu.</p>
            </div>

            {{-- Feature 5 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(245,158,11,0.05));">🗺️</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Mapa działki</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Interaktywna mapa z danymi z GUGiK — narysuj dom i sprawdź kąt nasłonecznienia dachu.</p>
            </div>

            {{-- Feature 6 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(251,191,36,0.1), rgba(251,191,36,0.05));">☀️</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Optymalizacja PV</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Wyznacz optymalny kąt i orientację instalacji fotowoltaicznej na podstawie położenia działki.</p>
            </div>

            {{-- Feature 7 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(236,72,153,0.1), rgba(236,72,153,0.05));">💡</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Tablica inspiracji</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Zapisuj inspiracje wizualne, próbniki kolorów i pomysły na wykończenie swojego domu.</p>
            </div>

            {{-- Feature 8 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.05));">📅</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Harmonogram</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Planuj etapy budowy na osi czasu, wyznaczaj terminy i pilnuj postępów prac.</p>
            </div>

            {{-- Feature 9 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4"
                     style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(34,211,238,0.05));">👥</div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Kontakty</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Baza wykonawców, dostawców i specjalistów powiązana z ofertami i kosztorysami.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== MAP HIGHLIGHT ===================== --}}
<section class="py-24 px-5 sm:px-8 bg-white overflow-hidden">
    <div class="max-w-5xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Text side --}}
            <div>
                <p class="text-indigo-600 font-semibold text-sm tracking-widest uppercase mb-3">Mapa działki</p>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight mb-6">
                    Narysuj swój dom<br>na prawdziwej mapie
                </h2>
                <p class="text-gray-500 leading-relaxed mb-8">
                    Dzięki integracji z oficjalnym API ULDK (GUGiK) możesz pobrać dokładne granice swojej działki i nanieść na nie projekt domu. Żadnych przybliżeń — prawdziwe dane geodezyjne.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Pobierz granice z GUGiK po numerze działki</p>
                            <p class="text-gray-500 text-sm mt-0.5">Wystarczy podać identyfikator działki z rejestru gruntów</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Narysuj budynek i sprawdź odległości od granic</p>
                            <p class="text-gray-500 text-sm mt-0.5">Automatyczne obliczanie odległości zgodnie z warunkami zabudowy</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Analizuj nasłonecznienie i kąt pod fotowoltaikę</p>
                            <p class="text-gray-500 text-sm mt-0.5">Wizualizacja trajektorii słońca dla Twojej lokalizacji</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Eksportuj mapę do PDF dla architekta</p>
                            <p class="text-gray-500 text-sm mt-0.5">Gotowy rysunek sytuacyjny z opisem i legendą</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Map mockup --}}
            <div class="relative">
                {{-- Glow --}}
                <div class="absolute -inset-8 rounded-3xl opacity-20 blur-3xl"
                     style="background: radial-gradient(circle, rgba(99,102,241,0.6), rgba(34,211,238,0.3));"></div>

                <div class="relative rounded-2xl overflow-hidden shadow-2xl"
                     style="border: 1px solid rgba(99,102,241,0.2); background: #0d1117; aspect-ratio: 4/3;">

                    {{-- Map background grid --}}
                    <div class="absolute inset-0"
                         style="background-image: linear-gradient(rgba(99,102,241,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,0.08) 1px, transparent 1px); background-size: 30px 30px; background-color: #0d1117;"></div>

                    {{-- Top bar --}}
                    <div class="absolute top-0 left-0 right-0 px-4 py-2.5 flex items-center justify-between z-10"
                         style="background: rgba(13,17,23,0.9); border-bottom: 1px solid rgba(99,102,241,0.15);">
                        <span class="text-xs font-semibold text-indigo-400">🗺️ Mapa działki — ULDK</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            <span class="text-xs text-emerald-400">Zsynchronizowano</span>
                        </div>
                    </div>

                    {{-- Parcel outline --}}
                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid meet">
                        {{-- Terrain parcels --}}
                        <polygon points="30,60 180,35 195,255 25,265" fill="rgba(34,197,94,0.06)" stroke="rgba(34,197,94,0.5)" stroke-width="1.5" stroke-dasharray="6,3"/>
                        <polygon points="195,255 180,35 370,45 375,260" fill="rgba(99,102,241,0.04)" stroke="rgba(99,102,241,0.2)" stroke-width="1"/>

                        {{-- Main parcel highlight --}}
                        <polygon points="35,65 175,40 190,250 30,260" fill="rgba(34,197,94,0.12)" stroke="rgba(34,197,94,0.8)" stroke-width="2"/>

                        {{-- House footprint --}}
                        <rect x="65" y="100" width="90" height="110" rx="3" fill="rgba(99,102,241,0.3)" stroke="#818cf8" stroke-width="2"/>
                        {{-- House label --}}
                        <text x="110" y="162" text-anchor="middle" fill="#a5b4fc" font-size="9" font-weight="600">DOM</text>
                        <text x="110" y="174" text-anchor="middle" fill="#6366f1" font-size="7">120 m²</text>

                        {{-- Dimension lines --}}
                        <line x1="35" y1="268" x2="190" y2="258" stroke="rgba(255,255,255,0.2)" stroke-width="1" stroke-dasharray="3,2"/>
                        <text x="110" y="278" text-anchor="middle" fill="rgba(255,255,255,0.3)" font-size="7">~42 m</text>
                        <line x1="192" y1="252" x2="192" y2="40" stroke="rgba(255,255,255,0.2)" stroke-width="1" stroke-dasharray="3,2"/>
                        <text x="202" y="148" fill="rgba(255,255,255,0.3)" font-size="7">~58 m</text>

                        {{-- Distance from boundary markers --}}
                        <line x1="35" y1="155" x2="65" y2="155" stroke="#f59e0b" stroke-width="1" stroke-dasharray="2,2"/>
                        <text x="50" y="150" text-anchor="middle" fill="#f59e0b" font-size="7">4m</text>

                        {{-- North arrow --}}
                        <g transform="translate(350,70)">
                            <circle cx="0" cy="0" r="18" fill="rgba(0,0,0,0.5)" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
                            <text x="0" y="-6" text-anchor="middle" fill="white" font-size="7" font-weight="600">N</text>
                            <polygon points="0,-14 3,-2 -3,-2" fill="#f43f5e" class="compass-needle"/>
                            <polygon points="0,14 3,2 -3,2" fill="rgba(255,255,255,0.3)"/>
                        </g>

                        {{-- Sun direction arrow --}}
                        <g transform="translate(330,230)" class="sun-ray">
                            <circle cx="0" cy="0" r="10" fill="rgba(251,191,36,0.15)" stroke="rgba(251,191,36,0.4)" stroke-width="1"/>
                            <text x="0" y="4" text-anchor="middle" fill="#fbbf24" font-size="10">☀</text>
                            <line x1="0" y1="-14" x2="-45" y2="-60" stroke="rgba(251,191,36,0.4)" stroke-width="1.5" stroke-dasharray="3,2"
                                  marker-end="url(#arrowhead)"/>
                        </g>

                        {{-- Parcel number tag --}}
                        <rect x="38" y="42" width="60" height="16" rx="3" fill="rgba(34,197,94,0.2)" stroke="rgba(34,197,94,0.4)" stroke-width="1"/>
                        <text x="68" y="53" text-anchor="middle" fill="rgba(34,197,94,0.9)" font-size="8" font-weight="600">Dz. 123/4</text>
                    </svg>

                    {{-- Scale bar --}}
                    <div class="absolute bottom-3 left-4 flex items-center gap-1.5">
                        <div class="flex">
                            <div class="w-8 h-1.5 bg-white/60"></div>
                            <div class="w-8 h-1.5 bg-transparent border border-white/60"></div>
                        </div>
                        <span class="text-xs text-white/40">10 m</span>
                    </div>

                    {{-- Zoom controls --}}
                    <div class="absolute bottom-3 right-4 flex flex-col gap-1">
                        <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold text-white/60 cursor-pointer"
                             style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">+</div>
                        <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold text-white/60 cursor-pointer"
                             style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">−</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ===================== STATS BAR ===================== --}}
<section class="py-16 px-5 sm:px-8" style="background: linear-gradient(135deg, #0f172a, #1e1b4b);">
    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-black gradient-text stat-number mb-2">9</div>
                <div class="text-slate-300 font-semibold text-base mb-1">modułów w jednym miejscu</div>
                <div class="text-slate-600 text-sm">Wszystko czego potrzebujesz</div>
            </div>
            <div class="sm:border-x sm:border-slate-700/50">
                <div class="text-4xl font-black gradient-text stat-number mb-2">1</div>
                <div class="text-slate-300 font-semibold text-base mb-1">narzędzie zamiast wielu</div>
                <div class="text-slate-600 text-sm">Koniec z chaosem aplikacji</div>
            </div>
            <div>
                <div class="text-4xl font-black gradient-text stat-number mb-2">🇵🇱</div>
                <div class="text-slate-300 font-semibold text-base mb-1">Polska baza danych działek</div>
                <div class="text-slate-600 text-sm">Oficjalne dane GUGiK/ULDK</div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== CTA SECTION ===================== --}}
<section class="py-24 px-5 sm:px-8 relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-64 opacity-20"
             style="background: radial-gradient(ellipse, rgba(99,102,241,0.6), transparent 70%); filter: blur(40px);"></div>
    </div>
    <div class="relative z-10 max-w-3xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold mb-8"
             style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #a5b4fc;">
            🚀 Bezpłatny dostęp — bez karty kredytowej
        </div>
        <h2 class="text-4xl sm:text-5xl font-black text-white tracking-tight mb-6">
            Gotowy żeby zapanować<br>nad swoją budową?
        </h2>
        <p class="text-slate-400 text-lg mb-10 leading-relaxed">
            Dołącz do budujących, którzy mają porządek zamiast chaosu. Rejestracja zajmuje minutę.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="px-10 py-4 bg-indigo-600 text-white font-bold rounded-xl text-lg hover:bg-indigo-500 transition-all duration-300 shadow-xl shadow-indigo-900/50 hover:shadow-indigo-900/70 hover:-translate-y-0.5">
                    Przejdź do aplikacji →
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="px-10 py-4 bg-indigo-600 text-white font-bold rounded-xl text-lg hover:bg-indigo-500 transition-all duration-300 shadow-xl shadow-indigo-900/50 hover:shadow-indigo-900/70 hover:-translate-y-0.5">
                    Zacznij za darmo →
                </a>
                <a href="{{ route('login') }}"
                   class="px-10 py-4 text-white font-semibold rounded-xl text-lg transition-all duration-300 hover:-translate-y-0.5"
                   style="border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05);">
                    Mam już konto
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- ===================== FAQ ===================== --}}
<section id="faq" class="py-24 px-5 sm:px-8 bg-white">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-indigo-600 font-semibold text-sm tracking-widest uppercase mb-3">FAQ</p>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Masz pytania?</h2>
        </div>

        <div class="space-y-3">

            {{-- FAQ 1 --}}
            <div class="faq-item rounded-xl border border-gray-200 overflow-hidden">
                <input type="checkbox" id="faq1">
                <label for="faq1" class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-gray-900 text-base pr-4">Czy aplikacja jest płatna?</span>
                    <span class="faq-icon flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg leading-none select-none">+</span>
                </label>
                <div class="faq-answer">
                    <div class="px-6 pb-5 text-gray-500 leading-relaxed">
                        Nie, dostęp jest całkowicie bezpłatny. Zarejestruj się i korzystaj ze wszystkich funkcji bez żadnych opłat ani limitów.
                    </div>
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="faq-item rounded-xl border border-gray-200 overflow-hidden">
                <input type="checkbox" id="faq2">
                <label for="faq2" class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-gray-900 text-base pr-4">Skąd pobierane są dane działki?</span>
                    <span class="faq-icon flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg leading-none select-none">+</span>
                </label>
                <div class="faq-answer">
                    <div class="px-6 pb-5 text-gray-500 leading-relaxed">
                        Z oficjalnego API ULDK udostępnianego przez GUGiK (Główny Urząd Geodezji i Kartografii). Są to dokładne, aktualne dane z Ewidencji Gruntów i Budynków.
                    </div>
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div class="faq-item rounded-xl border border-gray-200 overflow-hidden">
                <input type="checkbox" id="faq3">
                <label for="faq3" class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-gray-900 text-base pr-4">Czy moje dane są bezpieczne?</span>
                    <span class="faq-icon flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg leading-none select-none">+</span>
                </label>
                <div class="faq-answer">
                    <div class="px-6 pb-5 text-gray-500 leading-relaxed">
                        Tak. Dane są przechowywane na polskich serwerach, szyfrowane podczas przesyłania (HTTPS) i nie są udostępniane podmiotom trzecim. Spełniamy wymagania RODO.
                    </div>
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="faq-item rounded-xl border border-gray-200 overflow-hidden">
                <input type="checkbox" id="faq4">
                <label for="faq4" class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-gray-900 text-base pr-4">Na jakich urządzeniach działa?</span>
                    <span class="faq-icon flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg leading-none select-none">+</span>
                </label>
                <div class="faq-answer">
                    <div class="px-6 pb-5 text-gray-500 leading-relaxed">
                        Na każdym. HomeCraft działa w przeglądarce — komputer, laptop, tablet i smartfon. Interfejs jest w pełni responsywny i dostosowany do ekranów dotykowych.
                    </div>
                </div>
            </div>

            {{-- FAQ 5 --}}
            <div class="faq-item rounded-xl border border-gray-200 overflow-hidden">
                <input type="checkbox" id="faq5">
                <label for="faq5" class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-gray-900 text-base pr-4">Czy potrzebuję wiedzy technicznej?</span>
                    <span class="faq-icon flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg leading-none select-none">+</span>
                </label>
                <div class="faq-answer">
                    <div class="px-6 pb-5 text-gray-500 leading-relaxed">
                        Nie. HomeCraft zaprojektowano z myślą o osobach budujących dom po raz pierwszy. Interfejs jest intuicyjny — wystarczy znać numer swojej działki, by zacząć.
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ===================== FOOTER ===================== --}}
<footer class="border-t border-gray-100 bg-white py-10 px-5 sm:px-8">
    <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-lg flex items-center justify-center text-white font-black text-sm shadow-sm">H</div>
            <span class="text-base font-black text-gray-900 tracking-tight">HomeCraft</span>
        </div>
        <p class="text-sm text-gray-400 text-center">© {{ date('Y') }} HomeCraft. Wszystkie prawa zastrzeżone.</p>
        <div class="flex items-center gap-6">
            <a href="#" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">Polityka prywatności</a>
            <a href="#" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">Regulamin</a>
        </div>
    </div>
</footer>

</body>
</html>