<x-layouts.app title="Oś czasu">
<x-slot name="actions">
    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-800">← Dashboard</a>
</x-slot>

{{-- Toggle formularza --}}
<div class="mb-6">
    <button id="toggleForm"
        onclick="document.getElementById('addEntryForm').classList.toggle('hidden')"
        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        + Dodaj wpis
    </button>
</div>

{{-- Formularz dodawania --}}
<div id="addEntryForm" class="hidden mb-8 bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Nowy wpis na osi czasu</h3>
    <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500 font-medium">Typ</label>
                <select name="type" class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="note">Notatka</option>
                    <option value="photo">Zdjęcie</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium">Data</label>
                <input type="datetime-local" name="entry_date"
                    value="{{ now()->format('Y-m-d\TH:i') }}"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="text-xs text-gray-500 font-medium">Tytuł *</label>
            <input type="text" name="entry_title" required placeholder="Krótki tytuł wpisu"
                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="text-xs text-gray-500 font-medium">Treść</label>
            <textarea name="entry_body" rows="3" placeholder="Opcjonalny opis..."
                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
        </div>
        <div>
            <label class="text-xs text-gray-500 font-medium">Zdjęcie (opcjonalne)</label>
            <input type="file" name="image" accept="image/*"
                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Dodaj</button>
            <button type="button" onclick="document.getElementById('addEntryForm').classList.add('hidden')"
                class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">Anuluj</button>
        </div>
    </form>
</div>

{{-- Oś czasu --}}
@php
$typeConfig = [
    'milestone'   => ['color' => 'indigo',  'label' => 'Etap',         'icon' => '📅'],
    'budget_item' => ['color' => 'green',   'label' => 'Budżet',       'icon' => '💰'],
    'offer'       => ['color' => 'yellow',  'label' => 'Oferta',       'icon' => '🤝'],
    'document'    => ['color' => 'blue',    'label' => 'Dokument',     'icon' => '📁'],
    'idea'        => ['color' => 'purple',  'label' => 'Pomysł',       'icon' => '💡'],
    'note'        => ['color' => 'gray',    'label' => 'Notatka',      'icon' => '📝'],
    'photo'       => ['color' => 'pink',    'label' => 'Zdjęcie',      'icon' => '📷'],
];
$badgeClasses = [
    'indigo' => 'bg-indigo-100 text-indigo-700',
    'green'  => 'bg-green-100 text-green-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'blue'   => 'bg-blue-100 text-blue-700',
    'purple' => 'bg-purple-100 text-purple-700',
    'gray'   => 'bg-gray-100 text-gray-600',
    'pink'   => 'bg-pink-100 text-pink-700',
];
$dotClasses = [
    'indigo' => 'bg-indigo-500',
    'green'  => 'bg-green-500',
    'yellow' => 'bg-yellow-400',
    'blue'   => 'bg-blue-500',
    'purple' => 'bg-purple-500',
    'gray'   => 'bg-gray-400',
    'pink'   => 'bg-pink-500',
];
@endphp

@if($entries->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <p class="text-4xl mb-3">📰</p>
        <p class="text-sm">Brak wpisów na osi czasu.</p>
        <p class="text-xs mt-1">Wpisy pojawiają się automatycznie gdy dodajesz dokumenty, oferty, etapy itp.</p>
    </div>
@else
<div class="relative">
    {{-- Pionowa linia --}}
    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

    <div class="space-y-6">
        @foreach($entries as $entry)
        @php
            $cfg   = $typeConfig[$entry->type] ?? $typeConfig['note'];
            $color = $cfg['color'];
        @endphp
        <div class="relative flex gap-6 items-start">
            {{-- Kółko na osi --}}
            <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full {{ $dotClasses[$color] }} flex items-center justify-center text-white text-base shadow-sm">
                {{ $cfg['icon'] }}
            </div>

            {{-- Karta wpisu --}}
            <div class="flex-1 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badgeClasses[$color] }}">
                            {{ $cfg['label'] }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $entry->entry_date->format('d.m.Y H:i') }}
                        </span>
                    </div>
                    @if(in_array($entry->type, ['note', 'photo']))
                    <form action="{{ route('timeline.destroy', $entry) }}" method="POST" class="flex-shrink-0"
                        onsubmit="return confirm('Usunąć wpis?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-400 text-xs transition-colors">✕</button>
                    </form>
                    @endif
                </div>

                <h3 class="mt-2 text-sm font-semibold text-gray-800">{{ $entry->entry_title }}</h3>

                @if($entry->entry_body)
                <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ $entry->entry_body }}</p>
                @endif

                @if($entry->image_path)
                <img src="{{ Storage::url($entry->image_path) }}" alt="{{ $entry->entry_title }}"
                    class="mt-3 rounded-lg max-h-48 object-cover border border-gray-100">
                @endif

                @if($entry->entryable_id && $entry->entryable_type)
                @php
                    $linkRoute = null;
                    $linkLabel = 'Zobacz →';
                    try {
                        $modelClass = $entry->entryable_type;
                        $basename   = class_basename($modelClass);
                        $routeMap = [
                            'BudgetItem' => ['budget.show',     $entry->entryable_id],
                            'Offer'      => ['offers.show',     $entry->entryable_id],
                            'Document'   => ['documents.show',  $entry->entryable_id],
                            'Milestone'  => ['milestones.show', $entry->entryable_id],
                            'Idea'       => ['ideas.show',      $entry->entryable_id],
                        ];
                        if (isset($routeMap[$basename])) {
                            $linkRoute = route($routeMap[$basename][0], $routeMap[$basename][1]);
                        }
                    } catch (\Exception $e) {}
                @endphp
                @if($linkRoute)
                <a href="{{ $linkRoute }}" class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800 hover:underline">
                    {{ $linkLabel }}
                </a>
                @endif
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
</x-layouts.app>
