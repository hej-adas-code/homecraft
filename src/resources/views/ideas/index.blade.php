<x-layouts.app title="Pomysły">
    <x-slot name="actions">
        <a href="{{ route('idea-categories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Kategorie</a>
        <a href="{{ route('ideas.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj pomysł</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Filtrowanie po kategorii --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('ideas.index') }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ !request('category_id') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Wszystkie
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('ideas.index', ['category_id' => $cat->id]) }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ request('category_id') == $cat->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            @if($cat->icon) {{ $cat->icon }} @endif {{ $cat->name }}
        </a>
        @endforeach
    </div>
    @endif

    @if($ideas->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center text-gray-400">
            <p class="text-lg font-medium">Brak pomysłów</p>
            <p class="text-sm mt-1">Dodaj pierwszy pomysł.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ideas as $idea)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-sm transition-shadow flex flex-col">
                @if($idea->image_path)
                    <img src="{{ Storage::url($idea->image_path) }}" alt="{{ $idea->title }}" class="w-full h-40 object-cover">
                @elseif($idea->image_url ?? false)
                    <img src="{{ $idea->image_url }}" alt="{{ $idea->title }}" class="w-full h-40 object-cover">
                @endif
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $idea->title }}</h3>
                        @if($idea->category)
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                @if($idea->category->icon) {{ $idea->category->icon }} @endif
                                {{ $idea->category->name }}
                            </span>
                        @endif
                    </div>
                    @if($idea->description)
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2 flex-1">{{ $idea->description }}</p>
                    @endif
                    @if($idea->link)
                        <a href="{{ $idea->link }}" target="_blank" class="text-xs text-indigo-600 hover:underline truncate block mb-3">{{ $idea->link }}</a>
                    @endif
                    @if($idea->tags)
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach(explode(',', $idea->tags) as $tag)
                                @if(trim($tag))
                                <a href="{{ route('ideas.index', ['tag' => trim($tag)]) }}"
                                   class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-gray-200">{{ trim($tag) }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                        <a href="{{ route('ideas.edit', $idea) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                        <form action="{{ route('ideas.destroy', $idea) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć ten pomysł?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Usuń</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
