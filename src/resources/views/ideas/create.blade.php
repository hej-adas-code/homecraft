<x-layouts.app :title="isset($idea) ? 'Edytuj pomysł' : 'Nowy pomysł'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($idea) ? route('ideas.update', $idea) : route('ideas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($idea)) @method('PUT') @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $idea->title ?? '') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Brak kategorii —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $idea->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    @if($cat->icon) {{ $cat->icon }} @endif {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                        <textarea name="description" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $idea->description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link (URL)</label>
                        <input type="url" name="link" value="{{ old('link', $idea->link ?? '') }}"
                            placeholder="https://..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('link') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagi (oddzielone przecinkami)</label>
                        <input type="text" name="tags" value="{{ old('tags', $idea->tags ?? '') }}"
                            placeholder="np. kuchnia, salon, kolory"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    {{-- Zdjęcie --}}
                    <div class="space-y-3 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700">Obraz (opcjonalnie)</p>

                        @if(isset($idea) && $idea->image_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($idea->image_path) }}" alt="" class="w-32 h-20 object-cover rounded-lg">
                                <p class="text-xs text-gray-400 mt-1">Obecne zdjęcie — zostanie zastąpione jeśli wybierzesz nowe.</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Prześlij plik</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">lub podaj URL obrazka</label>
                            <input type="url" name="image_url" value="{{ old('image_url', $idea->image_url ?? '') }}"
                                placeholder="https://przyklad.com/obraz.jpg"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        {{ isset($idea) ? 'Zaktualizuj' : 'Dodaj pomysł' }}
                    </button>
                    <a href="{{ route('ideas.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
