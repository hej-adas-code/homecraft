<x-layouts.app :title="$meeting->title">
    <x-slot name="actions">
        <a href="{{ route('meetings.edit', $meeting) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Edytuj</a>
        <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" onsubmit="return confirm('Usunąć spotkanie?')" class="inline">
            @csrf @method('DELETE')
            <button class="px-4 py-2 text-red-600 border border-red-200 rounded-lg text-sm hover:bg-red-50">Usuń</button>
        </form>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-3xl space-y-5">

        {{-- Meta --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Data i godzina</p>
                    <p class="font-semibold text-gray-900">{{ $meeting->meeting_at->format('d.m.Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $meeting->meeting_at->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Wykonawca</p>
                    <p class="font-semibold text-gray-900">{{ $meeting->contractor_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Lokalizacja</p>
                    <p class="text-sm text-gray-700">{{ $meeting->location ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status</p>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $meeting->status === 'done' ? 'bg-green-100 text-green-700' : ($meeting->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700') }}">
                        {{ match($meeting->status) { 'done' => 'Odbyło się', 'cancelled' => 'Anulowane', default => 'Zaplanowane' } }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Agenda --}}
        @if($meeting->agenda)
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">📋 Agenda / o czym pamiętać</h3>
            <ul class="space-y-2">
                @foreach(explode("\n", trim($meeting->agenda)) as $line)
                    @if(trim($line))
                    <li class="flex items-start gap-2.5 text-sm text-gray-700">
                        <span class="mt-0.5 w-5 h-5 rounded-full border-2 border-indigo-200 flex-shrink-0"></span>
                        {{ trim($line) }}
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Notes --}}
        @if($meeting->notes)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <h3 class="font-semibold text-amber-800 mb-3 flex items-center gap-2">📝 Notatki po spotkaniu</h3>
            <p class="text-sm text-amber-900 whitespace-pre-line">{{ $meeting->notes }}</p>
        </div>
        @else
        <div class="bg-white border border-dashed border-gray-200 rounded-xl p-5 text-center">
            <p class="text-sm text-gray-400 mb-2">Brak notatek po spotkaniu</p>
            <a href="{{ route('meetings.edit', $meeting) }}" class="text-indigo-600 text-sm hover:underline">Dodaj notatki →</a>
        </div>
        @endif

    </div>
</x-layouts.app>