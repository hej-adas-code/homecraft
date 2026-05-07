<x-layouts.app :title="isset($document) ? 'Edytuj dokument' : 'Nowy dokument'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($document) ? route('documents.update', $document) : route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($document)) @method('PUT') @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $document->title ?? '') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Wybierz kategorię —</option>
                            @foreach([
                                'pozwolenia budowlane' => 'Pozwolenia budowlane',
                                'umowy'                => 'Umowy',
                                'projekty/rysunki'     => 'Projekty / rysunki',
                                'faktury'              => 'Faktury',
                                'korespondencja'       => 'Korespondencja',
                                'inne'                 => 'Inne',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('category', $document->category ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plik</label>
                        @if(isset($document) && $document->file_path)
                            <div class="mb-2 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Obecny plik: <strong>{{ $document->file_name }}</strong>
                                @if($document->file_size) ({{ number_format($document->file_size / 1024, 1) }} KB) @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mb-2">Wybierz nowy plik, aby zastąpić obecny.</p>
                        @endif
                        <input type="file" name="file" accept="*/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $document->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        {{ isset($document) ? 'Zaktualizuj' : 'Dodaj dokument' }}
                    </button>
                    <a href="{{ route('documents.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
