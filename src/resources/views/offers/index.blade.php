<x-layouts.app title="Oferty">
    <x-slot name="actions">
        <a href="{{ route('offers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj ofertę</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php
        $statusColors = [
            'new'      => 'bg-blue-100 text-blue-700',
            'pending'  => 'bg-yellow-100 text-yellow-700',
            'accepted' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
        $statusLabels = [
            'new'      => 'Nowa',
            'pending'  => 'Oczekuje',
            'accepted' => 'Zaakceptowana',
            'rejected' => 'Odrzucona',
        ];

        // Grupuj oferty po estimate_id (tylko te z estimate_id) żeby pokazać porównanie
        $grouped = $offers->filter(fn($o) => $o->estimate_id)->groupBy('estimate_id')->filter(fn($g) => $g->count() >= 2);
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        @if($offers->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg font-medium">Brak ofert</p>
                <p class="text-sm mt-1">Dodaj pierwszą ofertę.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tytuł</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Wykonawca</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kosztorys</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Kwota</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ważna do</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($offers as $offer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $offer->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $offer->contractor_name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $offer->estimate ? $offer->estimate->title : '—' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $color = $statusColors[$offer->status] ?? 'bg-gray-100 text-gray-700';
                                $label = $statusLabels[$offer->status] ?? ($offer->status ?? '—');
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 text-right font-medium">{{ $offer->amount ? number_format($offer->amount, 2, ',', ' ') . ' zł' : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $offer->valid_until ? $offer->valid_until->format('d.m.Y') : '—' }}</td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('offers.edit', $offer) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('offers.destroy', $offer) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć tę ofertę?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Porównanie ofert (jeśli ≥2 oferty dla tego samego kosztorysu) --}}
    @if($grouped->isNotEmpty())
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-lg">Porównanie ofert</h3>
        @foreach($grouped as $estimateId => $group)
        @php $estimateTitle = $group->first()->estimate ? $group->first()->estimate->title : ('Kosztorys #' . $estimateId); @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-700">Kosztorys: {{ $estimateTitle }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Oferta</th>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Wykonawca</th>
                            <th class="px-5 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Kwota</th>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ważna do</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $minAmount = $group->whereNotNull('amount')->min('amount'); @endphp
                        @foreach($group->sortBy('amount') as $offer)
                        <tr class="{{ $offer->amount && $offer->amount == $minAmount ? 'bg-green-50' : '' }}">
                            <td class="px-5 py-2.5 text-sm font-medium text-gray-900">
                                {{ $offer->title }}
                                @if($offer->amount && $offer->amount == $minAmount)
                                    <span class="ml-1 text-xs text-green-600 font-medium">(najniższa)</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-sm text-gray-600">{{ $offer->contractor_name ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-sm font-bold text-gray-900 text-right">{{ $offer->amount ? number_format($offer->amount, 2, ',', ' ') . ' zł' : '—' }}</td>
                            <td class="px-5 py-2.5">
                                @php $color = $statusColors[$offer->status] ?? 'bg-gray-100 text-gray-700'; @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $statusLabels[$offer->status] ?? ($offer->status ?? '—') }}</span>
                            </td>
                            <td class="px-5 py-2.5 text-sm text-gray-500">{{ $offer->valid_until ? $offer->valid_until->format('d.m.Y') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-layouts.app>
