<x-layouts.app title="Budżet">
    <x-slot name="actions">
        <a href="{{ route('budget-categories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Kategorie</a>
        <a href="{{ route('budget.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj pozycję</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Karty podsumowania --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Zaplanowane łącznie (wydatki)</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPlanned, 2, ',', ' ') }} zł</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Rzeczywiste wydatki</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($totalExpense, 2, ',', ' ') }} zł</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Saldo (przychody − wydatki)</p>
            @php $saldo = $totalIncome - $totalExpense; @endphp
            <p class="text-2xl font-bold {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($saldo, 2, ',', ' ') }} zł</p>
        </div>
    </div>

    {{-- Przełącznik tabs --}}
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        <a href="{{ route('budget.index') }}"
           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ !request('type') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Wszystkie
        </a>
        <a href="{{ route('budget.index', ['type' => 'expense']) }}"
           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('type') === 'expense' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Wydatki
        </a>
        <a href="{{ route('budget.index', ['type' => 'income']) }}"
           class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('type') === 'income' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Przychody
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($items->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg font-medium">Brak pozycji budżetu</p>
                <p class="text-sm mt-1">Dodaj pierwszą pozycję budżetu.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategoria</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Typ</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Planowane</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Rzeczywiste</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($item->category)
                                <span class="inline-flex items-center gap-1.5">
                                    @if($item->category->color)
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $item->category->color }}"></span>
                                    @endif
                                    {{ $item->category->name }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($item->type === 'income')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Przychód</span>
                            @elseif($item->type === 'expense')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Wydatek</span>
                            @else
                                <span class="text-gray-400">{{ $item->type ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 text-right">{{ number_format($item->planned_amount, 2, ',', ' ') }} zł</td>
                        <td class="px-6 py-4 text-sm text-right">
                            @if($item->actual_amount !== null)
                                <span class="{{ $item->actual_amount > $item->planned_amount ? 'text-red-600' : 'text-green-600' }} font-medium">
                                    {{ number_format($item->actual_amount, 2, ',', ' ') }} zł
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->date ? $item->date->format('d.m.Y') : '—' }}</td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('budget.edit', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('budget.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć tę pozycję?')">
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
