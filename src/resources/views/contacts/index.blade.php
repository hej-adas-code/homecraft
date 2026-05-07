<x-layouts.app title="Kontakty">
    <x-slot name="actions">
        <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">+ Dodaj kontakt</a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($contacts->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-lg font-medium">Brak kontaktów</p>
                <p class="text-sm mt-1">Dodaj pierwszy kontakt.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nazwa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Firma</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rola</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Telefon</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($contacts as $contact)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-700 font-semibold text-sm">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-900">{{ $contact->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $contact->company ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $contact->role ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="hover:text-indigo-600">{{ $contact->phone }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="hover:text-indigo-600 truncate max-w-xs block">{{ $contact->email }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('contacts.edit', $contact) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edytuj</a>
                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć ten kontakt?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.app>
