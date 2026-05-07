<x-layouts.app title="Działka">
    <x-slot name="actions">
        <a href="{{ route('plots.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">+ Dodaj działkę</a>
    </x-slot>

    @forelse($plots as $plot)
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 flex items-center justify-between">
        <div>
            <p class="font-semibold text-gray-900">Działka nr {{ $plot->plot_number }}</p>
            <p class="text-sm text-gray-500 mt-0.5">
                @if($plot->area) {{ number_format($plot->area, 0, ',', ' ') }} m² · @endif
                @if($plot->address) {{ $plot->address }} · @endif
                Dodana {{ $plot->created_at->diffForHumans() }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('plots.show', $plot) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Otwórz edytor</a>
            <form method="POST" action="{{ route('plots.destroy', $plot) }}" onsubmit="return confirm('Usunąć działkę?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm">Usuń</button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-20 text-gray-400">
        <div class="text-5xl mb-4">🗺️</div>
        <p class="text-lg font-medium mb-2">Brak działek</p>
        <p class="text-sm mb-6">Dodaj działkę podając jej numer ewidencyjny — pobierzemy kształt z geoportal.gov.pl</p>
        <a href="{{ route('plots.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium">+ Dodaj działkę</a>
    </div>
    @endforelse
</x-layouts.app>