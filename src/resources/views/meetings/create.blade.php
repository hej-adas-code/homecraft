<x-layouts.app :title="isset($meeting) ? 'Edytuj spotkanie' : 'Nowe spotkanie'">

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="{{ isset($meeting) ? route('meetings.update', $meeting) : route('meetings.store') }}" method="POST">
            @csrf
            @if(isset($meeting)) @method('PUT') @endif

            <div class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł spotkania <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}" required
                        placeholder="np. Omówienie projektu z architektem"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data i godzina <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="meeting_at"
                            value="{{ old('meeting_at', isset($meeting) ? $meeting->meeting_at->format('Y-m-d\TH:i') : '') }}" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('meeting_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(['planned' => 'Zaplanowane', 'done' => 'Odbyło się', 'cancelled' => 'Anulowane'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $meeting->status ?? 'planned') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wykonawca / firma</label>
                        <input type="text" name="contractor_name" value="{{ old('contractor_name', $meeting->contractor_name ?? '') }}"
                            placeholder="np. Kowalski Budowlanka"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontakt z bazy</label>
                        <select name="contact_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Wybierz kontakt —</option>
                            @foreach($contacts as $c)
                            <option value="{{ $c->id }}" {{ old('contact_id', $meeting->contact_id ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}{{ $c->company ? ' ('.$c->company.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokalizacja</label>
                    <input type="text" name="location" value="{{ old('location', $meeting->location ?? '') }}"
                        placeholder="np. Biuro architekta, ul. Kowalska 5"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agenda / o czym pamiętać</label>
                    <textarea name="agenda" rows="5"
                        placeholder="- Zapytać o termin realizacji&#10;- Omówić materiały&#10;- Przynieść projekt budowlany"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('agenda', $meeting->agenda ?? '') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Każda linia to osobny punkt agendy</p>
                </div>

                @if(isset($meeting))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notatki po spotkaniu</label>
                    <textarea name="notes" rows="4"
                        placeholder="Ustalenia, wnioski, następne kroki..."
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $meeting->notes ?? '') }}</textarea>
                </div>
                @endif

            </div>

            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    {{ isset($meeting) ? 'Zapisz zmiany' : 'Zaplanuj spotkanie' }}
                </button>
                <a href="{{ route('meetings.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Anuluj</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>