<!DOCTYPE html>
<html lang="pl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeCraft — Twój asystent budowy domu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-900">

{{-- Nawigacja --}}
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">H</div>
            <span class="text-xl font-bold text-gray-900 tracking-tight">HomeCraft</span>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Przejdź do aplikacji →
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Logowanie</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Zarejestruj się bezpłatnie
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- Hero --}}
<section class="pt-24 pb-20 px-6 text-center bg-gradient-to-b from-indigo-50 to-white">
    <div class="max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
            ✨ Bezpłatny dostęp — zarejestruj się już dziś
        </div>
        <h1 class="text-5xl font-extrabold text-gray-900 leading-tight mb-6">
            Buduj swój dom<br>
            <span class="text-indigo-600">z głową i porządkiem</span>
        </h1>
        <p class="text-xl text-gray-500 mb-10 leading-relaxed">
            HomeCraft to aplikacja dla budujących własny dom. Kosztorysy, budżet, oferty, dokumenty,
            analiza działki z optymalnym kątem dla PV — wszystko w jednym miejscu.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-600 text-white font-semibold rounded-xl text-base hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                Zacznij za darmo →
            </a>
            <a href="#funkcje" class="px-8 py-4 bg-white text-gray-700 font-semibold rounded-xl text-base hover:bg-gray-50 transition-colors border border-gray-200">
                Zobacz możliwości
            </a>
        </div>
    </div>
</section>

{{-- Funkcje --}}
<section id="funkcje" class="py-20 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Wszystko czego potrzebujesz przy budowie</h2>
            <p class="text-gray-500 text-lg">Jeden system zamiast kilkudziesięciu arkuszy kalkulacyjnych</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $features = [
                ['💰', 'Budżet i finanse', 'Planuj wydatki według kategorii. Śledź rzeczywiste koszty vs. plan. Nigdy nie stracisz z oczu swoich środków.'],
                ['📋', 'Kosztorysy', 'Twórz kosztorysy z podziałem na pozycje. Generuj zestawienia dla wykonawców i banku.'],
                ['🤝', 'Oferty od wykonawców', 'Zbieraj i porównuj oferty w jednym miejscu. Przyjmuj, odrzucaj, archiwizuj.'],
                ['📁', 'Dokumenty budowy', 'Pozwolenia, umowy, projekty, faktury — wszystko poukładane i dostępne w każdej chwili.'],
                ['🗺️', 'Analiza działki', 'Wpisz numer działki, pobierz jej kształt z rządowego geoportalu i zaplanuj ustawienie domu. Z uwzględnieniem linii zabudowy i kąta słońca dla paneli PV.'],
                ['💡', 'Inspiracje i pomysły', 'Zbieraj pomysły do domu według kategorii: architektura, wnętrza, ogród, technologie. Moodboard w jednym miejscu.'],
                ['📅', 'Harmonogram', 'Planuj etapy budowy. Śledź postęp. Wiedz, co jest zaplanowane, a co w trakcie.'],
                ['👥', 'Kontakty', 'Baza architektów, wykonawców, dostawców i urzędów — zawsze pod ręką.'],
                ['⚡', 'Więcej wkrótce', 'Dziennik budowy, import faktur, kalkulator kredytu, alerty budżetowe — już w przygotowaniu.'],
            ];
            @endphp

            @foreach($features as [$icon, $title, $desc])
            <div class="p-6 rounded-2xl border border-gray-100 hover:border-indigo-100 hover:shadow-md transition-all bg-white group">
                <div class="text-3xl mb-3">{{ $icon }}</div>
                <h3 class="font-semibold text-gray-900 mb-2 text-base">{{ $title }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Działka highlight --}}
<section class="py-20 px-6 bg-indigo-50">
    <div class="max-w-5xl mx-auto flex flex-col lg:flex-row items-center gap-12">
        <div class="flex-1">
            <div class="text-indigo-600 font-semibold text-sm mb-3 uppercase tracking-wide">Moduł działki</div>
            <h2 class="text-3xl font-bold text-gray-900 mb-5 leading-tight">
                Narysuj swój dom<br>na mapie działki
            </h2>
            <ul class="space-y-3 text-gray-600">
                @foreach([
                    'Pobierz kształt działki z geoportal.gov.pl po numerze ewidencyjnym',
                    'Ustaw linie zabudowy — odległości od każdej granicy',
                    'Wstaw wymiary domu i przesuń go w obrębie działki',
                    'Sprawdź optymalny azymut dla instalacji fotowoltaicznej'
                ] as $item)
                <li class="flex items-start gap-2.5">
                    <span class="text-indigo-500 mt-0.5">✓</span>
                    <span>{{ $item }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="flex-1 bg-white rounded-2xl border border-indigo-100 shadow-xl p-8 text-center">
            <div class="text-8xl mb-4">🗺️</div>
            <p class="text-gray-500 text-sm">Interaktywna mapa działki<br>z edytorem pozycji domu</p>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 px-6 bg-gray-900 text-white text-center">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-bold mb-4">Gotowy żeby ogarnąć swoją budowę?</h2>
        <p class="text-gray-400 mb-8 text-lg">Rejestracja jest bezpłatna. Żadnych kart, żadnych zobowiązań.</p>
        <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-indigo-500 text-white font-semibold rounded-xl text-base hover:bg-indigo-400 transition-colors">
            Zarejestruj się bezpłatnie →
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="py-8 px-6 border-t border-gray-100 text-center text-sm text-gray-400">
    <div class="flex items-center justify-center gap-2 mb-2">
        <div class="w-5 h-5 bg-indigo-600 rounded flex items-center justify-center text-white font-bold text-xs">H</div>
        <span class="font-semibold text-gray-600">HomeCraft</span>
    </div>
    <p>© {{ date('Y') }} HomeCraft. Wszystkie prawa zastrzeżone.</p>
</footer>

</body>
</html>