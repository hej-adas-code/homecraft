<x-layouts.app :title="isset($offer) ? 'Edytuj ofertę' : 'Nowa oferta'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($offer) ? route('offers.update', $offer) : route('offers.store') }}" method="POST">
                @csrf
                @if(isset($offer)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $offer->title ?? '') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wykonawca</label>
                        <input type="text" name="contractor_name" value="{{ old('contractor_name', $offer->contractor_name ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kosztorys</label>
                        <select name="estimate_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Brak kosztorysu —</option>
                            @foreach($estimates as $estimate)
                                <option value="{{ $estimate->id }}" {{ old('estimate_id', $offer->estimate_id ?? '') == $estimate->id ? 'selected' : '' }}>{{ $estimate->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Wybierz status —</option>
                            @foreach(['new' => 'Nowa', 'pending' => 'Oczekująca', 'accepted' => 'Zaakceptowana', 'rejected' => 'Odrzucona'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $offer->status ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kwota (zł)</label>
                        <input type="number" name="amount" value="{{ old('amount', $offer->amount ?? '') }}" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ważna do</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', isset($offer) && $offer->valid_until ? $offer->valid_until->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $offer->description ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        {{ isset($offer) ? 'Zaktualizuj' : 'Dodaj ofertę' }}
                    </button>
                    <a href="{{ route('offers.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>