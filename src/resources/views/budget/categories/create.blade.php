<x-layouts.app :title="isset($category) ? 'Edytuj kategorię' : 'Nowa kategoria budżetu'">
    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($category) ? route('budget-categories.update', $category) : route('budget-categories.store') }}" method="POST">
                @csrf
                @if(isset($category)) @method('PUT') @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolor</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', $category->color ?? '#6366f1') }}"
                                class="h-10 w-16 border border-gray-300 rounded-lg cursor-pointer">
                            <span class="text-xs text-gray-400">Wybierz kolor dla kategorii</span>
                        </div>
                        @error('color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Limit budżetu (zł)</label>
                        <input type="number" name="budget_limit" value="{{ old('budget_limit', $category->budget_limit ?? '') }}" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('budget_limit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        {{ isset($category) ? 'Zaktualizuj' : 'Dodaj kategorię' }}
                    </button>
                    <a href="{{ route('budget-categories.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>