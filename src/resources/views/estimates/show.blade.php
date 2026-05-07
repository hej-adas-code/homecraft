<x-layouts.app :title="$estimate->title">
    <x-slot name="actions">
        <a href="{{ route('estimates.edit', $estimate) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Edytuj</a>
        <a href="{{ route('estimates.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Wróć do listy</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-4xl space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    <p class="font-medium text-gray-900">{{ $estimate->status ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Kwota</p>
                    <p class="font-medium text-gray-900">{{ $estimate->total ? number_format($estimate->total, 2, ',', ' ') . ' zł' : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Ważny do</p>
                    <p class="font-medium text-gray-900">{{ $estimate->valid_until ? $estimate->valid_until->format('d.m.Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Liczba pozycji</p>
                    <p class="font-medium text-gray-900">{{ $estimate->items->count() }}</p>
                </div>
            </div>
            @if($estimate->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-600">{{ $estimate->description }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Pozycje kosztorysu</h3>
            </div>
            @if($estimate->items->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400 text-sm">Brak pozycji w tym kosztorysie.</div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nazwa</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ilość</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jednostka</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cena jedn.</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Łącznie</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($estimate->items as $item)
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $item->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700 text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $item->unit }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700 text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} zł</td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format($item->total, 2, ',', ' ') }} zł</td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('estimates.items.destroy', [$estimate, $item]) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć tę pozycję?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Usuń</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-700 text-right">Suma:</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($estimate->items->sum('total'), 2, ',', ' ') }} zł</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            {{-- Formularz dodawania pozycji inline --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Dodaj pozycję</p>
                <form action="{{ route('estimates.items.store', $estimate) }}" method="POST">
                    @csrf
                    <div class="flex flex-wrap gap-2 items-end">
                        <div class="flex-1 min-w-[160px]">
                            <label class="block text-xs text-gray-500 mb-1">Nazwa <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Nazwa pozycji">
                            @error('name') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">Ilość <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" required min="0" step="0.01"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('quantity') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">Jednostka</label>
                            <input type="text" name="unit" value="{{ old('unit', 'szt.') }}"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="szt.">
                        </div>
                        <div class="w-36">
                            <label class="block text-xs text-gray-500 mb-1">Cena jedn. (zł) <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_price" value="{{ old('unit_price') }}" required min="0" step="0.01"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="0.00">
                            @error('unit_price') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="pb-0.5">
                            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                Dodaj
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
