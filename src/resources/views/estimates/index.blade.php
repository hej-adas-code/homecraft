<x-layouts.app title="Kosztorysy">
    <x-slot name="actions">
        <a href="{{ route('estimates.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj kosztorys</a>
    </x-slot>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($estimates->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg font-medium">Brak kosztorysów</p>
                <p class="text-sm mt-1">Dodaj pierwszy kosztorys.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tytuł</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Kwota</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ważny do</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pozycje</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($estimates as $estimate)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('estimates.show', $estimate) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $estimate->title }}</a>
                            @if($estimate->description)
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $estimate->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = ['draft' => 'bg-gray-100 text-gray-700', 'sent' => 'bg-blue-100 text-blue-700', 'accepted' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                                $statusLabels = ['draft' => 'Szkic', 'sent' => 'Wysłany', 'accepted' => 'Zaakceptowany', 'rejected' => 'Odrzucony'];
                                $color = $statusColors[$estimate->status] ?? 'bg-gray-100 text-gray-700';
                                $label = $statusLabels[$estimate->status] ?? ($estimate->status ?? '—');
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 text-right font-medium">{{ $estimate->total ? number_format($estimate->total, 2, ',', ' ') . ' zł' : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $estimate->valid_until ? $estimate->valid_until->format('d.m.Y') : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $estimate->items_count }}</td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('estimates.edit', $estimate) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('estimates.destroy', $estimate) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć ten kosztorys?')">
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
</x-layouts.app>