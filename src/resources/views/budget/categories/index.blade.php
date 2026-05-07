<x-layouts.app title="Kategorie budżetu">
    <x-slot name="actions">
        <a href="{{ route('budget-categories.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj kategorię</a>
    </x-slot>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($categories->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg font-medium">Brak kategorii</p>
                <p class="text-sm mt-1">Dodaj pierwszą kategorię budżetu.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kolor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Limit budżetu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pozycje</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($category->color)
                                    <span class="w-4 h-4 rounded-full inline-block" style="background-color: {{ $category->color }}"></span>
                                @endif
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $category->color ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700 text-sm">{{ $category->budget_limit ? number_format($category->budget_limit, 2, ',', ' ') . ' zł' : '—' }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $category->items_count }}</td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('budget-categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('budget-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć tę kategorię?')">
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