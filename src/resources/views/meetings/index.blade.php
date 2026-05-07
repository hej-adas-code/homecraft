<x-layouts.app title="Spotkania z wykonawcami">
    <x-slot name="actions">
        <a href="{{ route('meetings.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">+ Zaplanuj spotkanie</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Nadchodzące --}}
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Nadchodzące</h2>
        @forelse($upcoming as $m)
        <a href="{{ route('meetings.show', $m) }}" class="block bg-white border border-gray-200 rounded-xl p-5 mb-3 hover:border-indigo-300 hover:shadow-sm transition-all">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 text-center bg-indigo-50 border border-indigo-100 rounded-xl px-3 py-2 min-w-[56px]">
                        <p class="text-xs text-indigo-500 font-medium">{{ $m->meeting_at->format('M') }}</p>
                        <p class="text-2xl font-bold text-indigo-700 leading-none">{{ $m->meeting_at->format('d') }}</p>
                        <p class="text-xs text-indigo-400">{{ $m->meeting_at->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $m->title }}</p>
                        @if($m->contractor_name)
                            <p class="text-sm text-gray-500 mt-0.5">👤 {{ $m->contractor_name }}</p>
                        @endif
                        @if($m->location)
                            <p class="text-sm text-gray-400 mt-0.5">📍 {{ $m->location }}</p>
                        @endif
                        @if($m->agenda)
                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit($m->agenda, 120) }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $m->status === 'done' ? 'bg-green-100 text-green-700' : ($m->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700') }}">
                    {{ match($m->status) { 'done' => 'Odbyło się', 'cancelled' => 'Anulowane', default => 'Zaplanowane' } }}
                </span>
            </div>
        </a>
        @empty
        <div class="bg-white border border-dashed border-gray-200 rounded-xl p-8 text-center text-gray-400">
            <p class="text-base font-medium mb-1">Brak zaplanowanych spotkań</p>
            <a href="{{ route('meetings.create') }}" class="text-indigo-600 text-sm hover:underline">Zaplanuj pierwsze →</a>
        </div>
        @endforelse
    </div>

    {{-- Poprzednie --}}
    @if($past->count())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Poprzednie</h2>
        @foreach($past as $m)
        <a href="{{ route('meetings.show', $m) }}" class="flex items-center gap-4 bg-white border border-gray-100 rounded-xl p-4 mb-2 hover:border-gray-300 transition-all opacity-70 hover:opacity-100">
            <div class="text-center min-w-[48px]">
                <p class="text-xs text-gray-400">{{ $m->meeting_at->format('d.m') }}</p>
                <p class="text-sm font-semibold text-gray-500">{{ $m->meeting_at->format('H:i') }}</p>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-700 text-sm">{{ $m->title }}</p>
                @if($m->contractor_name)<p class="text-xs text-gray-400">{{ $m->contractor_name }}</p>@endif
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $m->status === 'done' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                {{ $m->status === 'done' ? 'Odbyło się' : 'Minęło' }}
            </span>
        </a>
        @endforeach
    </div>
    @endif
</x-layouts.app>