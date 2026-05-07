<x-layouts.app title="Dashboard">

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Karta: Środki --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Środki własne</span>
            <span class="text-2xl">💰</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalIncome, 0, ',', ' ') }} zł</p>
        <p class="text-xs text-gray-400 mt-1">łącznie zaewidencjonowane</p>
    </div>

    {{-- Karta: Wydatki --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Wydatki rzeczywiste</span>
            <span class="text-2xl">📉</span>
        </div>
        <p class="text-2xl font-bold text-red-600">{{ number_format($totalExpense, 0, ',', ' ') }} zł</p>
        @if($totalPlanned > 0)
        <p class="text-xs text-gray-400 mt-1">plan: {{ number_format($totalPlanned, 0, ',', ' ') }} zł</p>
        @else
        <p class="text-xs text-gray-400 mt-1">brak zaplanowanego budżetu</p>
        @endif
    </div>

    {{-- Karta: Bilans --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Bilans</span>
            <span class="text-2xl">⚖️</span>
        </div>
        @php $balance = $totalIncome - $totalExpense; @endphp
        <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($balance, 0, ',', ' ') }} zł
        </p>
        <p class="text-xs text-gray-400 mt-1">środki - wydatki</p>
    </div>

    {{-- Karta: Dokumenty --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Dokumenty</span>
            <span class="text-2xl">📁</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $documentsCount }}</p>
        <p class="text-xs text-gray-400 mt-1">oferty: {{ $offersCount }} · kosztorysy: {{ $estimatesCount }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Budżet wg kategorii --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Budżet wg kategorii</h2>
            <a href="{{ route('budget.index') }}" class="text-xs text-indigo-600 hover:underline">Więcej →</a>
        </div>
        @forelse($categories as $cat)
        <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full inline-block" style="background:{{ $cat->color }}"></span>
                    {{ $cat->name }}
                </span>
                <span class="text-gray-600">{{ number_format($cat->spent ?? 0, 0, ',', ' ') }} zł
                    @if($cat->budget_limit) / {{ number_format($cat->budget_limit, 0, ',', ' ') }} zł @endif
                </span>
            </div>
            @if($cat->budget_limit && $cat->budget_limit > 0)
            @php $pct = min(100, round(($cat->spent / $cat->budget_limit) * 100)); @endphp
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $pct > 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
            </div>
            @endif
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">
            Brak kategorii.<br>
            <a href="{{ route('budget-categories.create') }}" class="text-indigo-600 hover:underline">Dodaj pierwszą →</a>
        </p>
        @endforelse
    </div>

    {{-- Harmonogram --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Nadchodzące etapy</h2>
            <a href="{{ route('milestones.index') }}" class="text-xs text-indigo-600 hover:underline">Więcej →</a>
        </div>
        @forelse($upcomingMilestones as $m)
        <div class="flex items-start gap-3 mb-3">
            <span class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0" style="background:{{ $m->color }}"></span>
            <div>
                <p class="text-sm font-medium text-gray-800">{{ $m->title }}</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($m->start_date)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($m->end_date)->format('d.m.Y') }}</p>
            </div>
            <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ $m->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $m->status === 'in_progress' ? 'w toku' : 'planowany' }}
            </span>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">
            Brak etapów.<br>
            <a href="{{ route('milestones.create') }}" class="text-indigo-600 hover:underline">Dodaj etap →</a>
        </p>
        @endforelse
    </div>

    {{-- Ostatnie oferty + dokumenty --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Ostatnie oferty</h2>
                <a href="{{ route('offers.index') }}" class="text-xs text-indigo-600 hover:underline">Więcej →</a>
            </div>
            @forelse($recentOffers as $offer)
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $offer->title }}</p>
                    <p class="text-xs text-gray-400">{{ $offer->contractor_name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ number_format($offer->amount, 0, ',', ' ') }} zł</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $offer->status === 'accepted' ? 'bg-green-100 text-green-700' : ($offer->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ match($offer->status) { 'accepted' => 'przyjęta', 'rejected' => 'odrzucona', default => 'oczekuje' } }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-2">Brak ofert.<br><a href="{{ route('offers.create') }}" class="text-indigo-600 hover:underline">Dodaj →</a></p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Ostatnie dokumenty</h2>
                <a href="{{ route('documents.index') }}" class="text-xs text-indigo-600 hover:underline">Więcej →</a>
            </div>
            @forelse($recentDocs as $doc)
            <div class="flex items-center gap-2 mb-2">
                <span class="text-base">📄</span>
                <div>
                    <p class="text-sm font-medium text-gray-800 leading-tight">{{ $doc->title }}</p>
                    <p class="text-xs text-gray-400">{{ $doc->created_at->format('d.m.Y') }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-2">Brak dokumentów.<br><a href="{{ route('documents.create') }}" class="text-indigo-600 hover:underline">Dodaj →</a></p>
            @endforelse
        </div>
    </div>

</div>
</x-layouts.app>