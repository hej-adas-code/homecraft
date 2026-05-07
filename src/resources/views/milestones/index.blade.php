<x-layouts.app title="Harmonogram">
    <x-slot name="actions">
        <a href="{{ route('milestones.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj kamień milowy</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($milestones->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center text-gray-400">
            <p class="text-lg font-medium">Brak kamieni milowych</p>
            <p class="text-sm mt-1">Dodaj pierwszy kamień milowy harmonogramu.</p>
        </div>
    @else
        @php
            $withDates = $milestones->filter(fn($m) => $m->start_date && $m->end_date);
            $minDate = $withDates->min('start_date');
            $maxDate = $withDates->max('end_date');
            $totalDays = ($minDate && $maxDate) ? max($minDate->diffInDays($maxDate), 1) : 1;

            $statusColors = [
                'planned'     => ['bar' => '#a5b4fc', 'badge' => 'bg-gray-100 text-gray-700'],
                'in_progress' => ['bar' => '#3b82f6', 'badge' => 'bg-blue-100 text-blue-700'],
                'done'        => ['bar' => '#22c55e', 'badge' => 'bg-green-100 text-green-700'],
                'delayed'     => ['bar' => '#ef4444', 'badge' => 'bg-red-100 text-red-700'],
            ];
            $statusLabels = [
                'planned'     => 'Planowany',
                'in_progress' => 'W toku',
                'done'        => 'Ukończony',
                'delayed'     => 'Opóźniony',
            ];
        @endphp

        {{-- Timeline --}}
        @if($withDates->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Oś czasu</h3>
            {{-- Nagłówek z datami --}}
            <div class="flex justify-between text-xs text-gray-400 mb-2 px-1">
                <span>{{ $minDate->format('d.m.Y') }}</span>
                <span>{{ $maxDate->format('d.m.Y') }}</span>
            </div>
            {{-- Tło osi --}}
            <div class="relative">
                <div class="absolute inset-0 bg-gray-100 rounded-full h-2 top-3"></div>
                <div class="space-y-3">
                    @foreach($withDates as $milestone)
                        @php
                            $startOffset = $minDate->diffInDays($milestone->start_date);
                            $duration    = $milestone->start_date->diffInDays($milestone->end_date) + 1;
                            $leftPct     = round($startOffset / $totalDays * 100, 2);
                            $widthPct    = max(round($duration / $totalDays * 100, 2), 2);
                            $barColor    = $milestone->color ?: ($statusColors[$milestone->status]['bar'] ?? '#a5b4fc');
                        @endphp
                        <div class="relative h-8">
                            <div class="absolute h-6 rounded-full flex items-center px-2 overflow-hidden"
                                 style="left: {{ $leftPct }}%; width: {{ $widthPct }}%; background-color: {{ $barColor }}; opacity: 0.85; top: 1px;"
                                 title="{{ $milestone->title }}: {{ $milestone->start_date->format('d.m.Y') }} – {{ $milestone->end_date->format('d.m.Y') }}">
                                <span class="text-white text-xs font-medium truncate">{{ $milestone->title }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Tabela --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Daty</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($milestones as $milestone)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($milestone->color)
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $milestone->color }}"></span>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $milestone->title }}</p>
                                    @if($milestone->description)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $milestone->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($milestone->start_date && $milestone->end_date)
                                {{ $milestone->start_date->format('d.m.Y') }} – {{ $milestone->end_date->format('d.m.Y') }}
                            @elseif($milestone->start_date)
                                od {{ $milestone->start_date->format('d.m.Y') }}
                            @elseif($milestone->end_date)
                                do {{ $milestone->end_date->format('d.m.Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = $statusColors[$milestone->status]['badge'] ?? 'bg-gray-100 text-gray-700';
                                $label = $statusLabels[$milestone->status] ?? ($milestone->status ?? '—');
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('milestones.edit', $milestone) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('milestones.destroy', $milestone) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć ten kamień milowy?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.app>
