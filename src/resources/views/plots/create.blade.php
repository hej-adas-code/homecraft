<x-layouts.app title="Dodaj działkę">

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-1">Wyszukaj działkę</h2>
        <p class="text-sm text-gray-500 mb-5">Podaj numer ewidencyjny działki (np. <code class="bg-gray-100 px-1 rounded">141201_1.0001.123</code> lub <code class="bg-gray-100 px-1 rounded">141201_1.0001.123/4</code>). Geometria zostanie pobrana z geoportal.gov.pl (ULDK).</p>

        <div class="flex gap-3 mb-4">
            <input type="text" id="searchInput" placeholder="np. 141201_1.0001.123" class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button onclick="searchPlot()" id="searchBtn" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">Sprawdź</button>
        </div>

        <div id="searchResult" class="hidden"></div>
    </div>

    <form method="POST" action="{{ route('plots.store') }}" id="saveForm" class="hidden">
        @csrf
        <input type="hidden" name="plot_number" id="plotNumber">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Zapisz działkę</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Opis / adres (opcjonalnie)</label>
                <input type="text" name="address" placeholder="np. ul. Leśna 5, Kobyłka" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            @error('plot_number') <p class="text-red-600 text-sm mb-3">{{ $message }}</p> @enderror
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Zapisz i otwórz edytor →</button>
        </div>
    </form>
</div>

<script>
async function searchPlot() {
    const input = document.getElementById('searchInput');
    const btn = document.getElementById('searchBtn');
    const result = document.getElementById('searchResult');
    const saveForm = document.getElementById('saveForm');
    const plotNumber = input.value.trim();

    if (!plotNumber) return;

    btn.disabled = true;
    btn.textContent = 'Szukam...';
    result.className = 'hidden';

    try {
        const resp = await fetch(`{{ route('plots.search') }}?plot_number=${encodeURIComponent(plotNumber)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await resp.json();

        if (data.success) {
            const area = data.area ? `${Math.round(data.area).toLocaleString('pl')} m²` : 'brak danych';
            result.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-green-800 font-medium">✓ Znaleziono działkę</p>
                    <p class="text-green-700 text-sm mt-1">Powierzchnia: ${area} · Format geometrii: ${data.geometry.substring(0,20)}...</p>
                </div>`;
            result.className = 'mb-4';
            document.getElementById('plotNumber').value = plotNumber;
            saveForm.className = '';
        } else {
            result.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800 text-sm">✗ ${data.error}</div>`;
            result.className = 'mb-4';
            saveForm.className = 'hidden';
        }
    } catch (e) {
        result.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800 text-sm">Błąd połączenia z serwerem</div>`;
        result.className = 'mb-4';
    }

    btn.disabled = false;
    btn.textContent = 'Sprawdź';
}

document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') searchPlot();
});
</script>
</x-layouts.app>