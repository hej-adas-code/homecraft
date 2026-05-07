<x-layouts.app :title="isset($milestone) ? 'Edytuj kamień milowy' : 'Nowy kamień milowy'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($milestone) ? route('milestones.update', $milestone) : route('milestones.store') }}" method="POST">
                @csrf
                @if(isset($milestone)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $milestone->title ?? '') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Wybierz status —</option>
                            @foreach(['planned' => 'Planowany', 'in_progress' => 'W toku', 'done' => 'Ukończony', 'delayed' => 'Opóźniony'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $milestone->status ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolor</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', $milestone->color ?? '#6366f1') }}"
                                class="h-10 w-16 border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data rozpoczęcia</label>
                        <input type="date" name="start_date" value="{{ old('start_date', isset($milestone) && $milestone->start_date ? $milestone->start_date->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data zakończenia</label>
                        <input type="date" name="end_date" value="{{ old('end_date', isset($milestone) && $milestone->end_date ? $milestone->end_date->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $milestone->description ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        {{ isset($milestone) ? 'Zaktualizuj' : 'Dodaj kamień milowy' }}
                    </button>
                    <a href="{{ route('milestones.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>