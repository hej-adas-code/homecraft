<x-layouts.app title="Działka {{ $plot->plot_number }}">
<x-slot name="actions">
    <a href="{{ route('plots.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Moje działki</a>
</x-slot>

{{-- Leaflet CSS + JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>

<div class="grid gap-6" style="grid-template-columns: 280px 1fr;">

    {{-- Panel boczny --}}
    <div class="space-y-4">

        {{-- Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide mb-2">Działka</p>
            <p class="font-bold text-gray-900 text-sm">Nr: {{ $plot->plot_number }}</p>
            @if($plot->area)
            <p class="text-sm text-gray-600">Powierzchnia: <strong>{{ number_format($plot->area, 0, ',', ' ') }} m²</strong></p>
            @endif
            @if($plot->address)
            <p class="text-sm text-gray-500 mt-1">{{ $plot->address }}</p>
            @endif
            <p id="houseAreaInfo" class="text-sm text-indigo-600 mt-2 hidden"></p>
        </div>

        {{-- Wymiary domu --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide mb-3">Dom</p>
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="text-xs text-gray-500">Szerokość (m)</label>
                    <input type="number" id="houseW" value="{{ $plot->house_width ?? 12 }}" min="3" max="50" step="0.5"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        onchange="updateHouseRect()">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Głębokość (m)</label>
                    <input type="number" id="houseH" value="{{ $plot->house_height ?? 9 }}" min="3" max="50" step="0.5"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        onchange="updateHouseRect()">
                </div>
            </div>
            <div class="mb-3">
                <label class="text-xs text-gray-500">Obrót (°)</label>
                <input type="range" id="houseRot" min="-180" max="180" step="1" value="{{ $plot->house_rotation ?? 0 }}"
                    class="w-full" oninput="document.getElementById('houseRotVal').textContent=this.value+'°'; updateHouseRect();">
                <div class="flex justify-between text-xs text-gray-400 mt-0.5">
                    <span>-180°</span><span id="houseRotVal">{{ $plot->house_rotation ?? 0 }}°</span><span>+180°</span>
                </div>
            </div>
            <button onclick="centerHouse()" class="w-full py-2 text-sm text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">
                Wycentruj dom
            </button>
        </div>

        {{-- Linie zabudowy --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide mb-3">Linie zabudowy (m)</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach([['front','Przód (pd.)'],['back','Tył (pn.)'],['left','Lewo (wsch.)'],['right','Prawo (zach.)']] as [$key,$label])
                <div>
                    <label class="text-xs text-gray-500">{{ $label }}</label>
                    <input type="number" id="sb_{{ $key }}" value="{{ $plot->{'setback_'.$key} }}" min="0" max="30" step="0.5"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        onchange="updateSetbackLayer()">
                </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('plots.update', $plot) }}" class="mt-3">
                @csrf @method('PATCH')
                @foreach(['front','back','left','right'] as $k)
                <input type="hidden" name="setback_{{ $k }}" id="sb_{{ $k }}_hidden">
                @endforeach
                <button type="submit" onclick="syncSetbacks()" class="w-full py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700">Zapisz ustawienia</button>
            </form>
        </div>

        {{-- PV info --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-xs text-amber-700 uppercase font-semibold tracking-wide mb-2">☀️ Optymalizacja PV</p>
            <p id="pvInfo" class="text-sm text-amber-800">Wczytaj działkę, aby zobaczyć zalecenia.</p>
        </div>

        {{-- Legenda --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide mb-2">Legenda</p>
            <div class="space-y-1.5 text-xs text-gray-600">
                <div class="flex items-center gap-2"><span class="w-4 h-3 rounded" style="background:#4ade80;opacity:0.5"></span> Działka</div>
                <div class="flex items-center gap-2"><span class="w-4 h-1 border-t-2 border-dashed border-orange-400 inline-block"></span> Linia zabudowy</div>
                <div class="flex items-center gap-2"><span class="w-4 h-3 rounded bg-blue-400 opacity-70"></span> Dom (przeciągnij marker)</div>
                <div class="flex items-center gap-2"><span class="w-4 h-1 border-t-2 border-yellow-400 inline-block"></span> Kierunek słońca (S)</div>
            </div>
        </div>

    </div>

    {{-- Mapa --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs text-gray-400">Przeciągnij marker domu · Scroll = zoom</p>
            <div class="flex gap-2">
                <button onclick="fitPlot()" class="px-3 py-1 text-xs border rounded hover:bg-gray-50">Dopasuj widok</button>
                <button onclick="saveHousePosition()" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">💾 Zapisz pozycję</button>
            </div>
        </div>
        <div id="plotMap" style="height: 600px; z-index: 0;" class="rounded-lg"></div>
    </div>

</div>

<script>
// ================================================================
// Data from server
// ================================================================
const PLOT_WKT  = @json($plot->geometry_wkt);
const PLOT_DATA = {
    house_x:        {{ $plot->house_x ?? 'null' }},
    house_y:        {{ $plot->house_y ?? 'null' }},
    house_width:    {{ $plot->house_width ?? 12 }},
    house_height:   {{ $plot->house_height ?? 9 }},
    house_rotation: {{ $plot->house_rotation ?? 0 }},
};
const SAVE_URL = "{{ route('plots.update-house', $plot) }}";
const CSRF     = "{{ csrf_token() }}";

// ================================================================
// proj4 — PUWG92 → WGS84
// ================================================================
proj4.defs('EPSG:2180', '+proj=tmerc +lat_0=0 +lon_0=19 +k=0.9993 +x_0=500000 +y_0=-5300000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs');

function puwg92toLatLng(x, y) {
    const [lon, lat] = proj4('EPSG:2180', 'EPSG:4326', [x, y]);
    return [lat, lon]; // Leaflet: [lat, lng]
}

function latLngToPuwg92(lat, lng) {
    return proj4('EPSG:4326', 'EPSG:2180', [lng, lat]); // returns [x, y]
}

function parseWKTtoLatLng(wkt) {
    if (!wkt) return [];
    const str = wkt.replace(/\s+/g, ' ').trim().toUpperCase();
    let coordStr = null;
    if (str.startsWith('MULTIPOLYGON')) {
        const m = wkt.match(/\(\(\(([^)]+)\)\)/);
        if (m) coordStr = m[1];
    } else {
        const m = wkt.match(/\(\(([^)]+)\)/);
        if (m) coordStr = m[1];
        else {
            const m2 = wkt.match(/\(([^)]+)\)/);
            if (m2) coordStr = m2[1];
        }
    }
    if (!coordStr) return [];
    return coordStr.trim().split(',').map(p => {
        const parts = p.trim().split(/\s+/);
        const x = parseFloat(parts[0]), y = parseFloat(parts[1]);
        if (isNaN(x) || isNaN(y)) return null;
        return puwg92toLatLng(x, y);
    }).filter(Boolean);
}

// Compute approximate centroid (avg) of polygon lat/lng
function centroidLatLng(latlngs) {
    let lat = 0, lng = 0;
    latlngs.forEach(p => { lat += p[0]; lng += p[1]; });
    return [lat / latlngs.length, lng / latlngs.length];
}

// Compute inward offset polygon (shrink) — in WGS84 degrees
// We use a simple per-edge offset approach in metric space
function offsetPolygonWGS84(latlngs, distMeters) {
    // Convert to approx meters for offset calc, then back
    if (latlngs.length < 3 || distMeters <= 0) return latlngs;

    const refLat = latlngs[0][0];
    const mPerLat = 111320;
    const mPerLng = 111320 * Math.cos(refLat * Math.PI / 180);

    // to local metres
    const pts = latlngs.map(p => ({ x: p[1] * mPerLng, y: p[0] * mPerLat }));
    const n = pts.length;

    // signed area
    let area = 0;
    for (let i = 0; i < n; i++) {
        const j = (i+1) % n;
        area += (pts[j].x - pts[i].x) * (pts[j].y + pts[i].y);
    }
    const sign = area > 0 ? -1 : 1;

    const result = [];
    for (let i = 0; i < n; i++) {
        const a = pts[(i-1+n) % n];
        const b = pts[i];
        const c = pts[(i+1) % n];

        const ab = { x: b.x-a.x, y: b.y-a.y };
        const ab_len = Math.hypot(ab.x, ab.y);
        if (ab_len < 1e-9) { result.push(b); continue; }
        const n_ab = { x: sign*ab.y/ab_len, y: -sign*ab.x/ab_len };

        const bc = { x: c.x-b.x, y: c.y-b.y };
        const bc_len = Math.hypot(bc.x, bc.y);
        if (bc_len < 1e-9) { result.push(b); continue; }
        const n_bc = { x: sign*bc.y/bc_len, y: -sign*bc.x/bc_len };

        const bis = { x: n_ab.x + n_bc.x, y: n_ab.y + n_bc.y };
        const bisLen = Math.hypot(bis.x, bis.y);
        if (bisLen < 1e-6) {
            result.push({ x: b.x + n_ab.x*distMeters, y: b.y + n_ab.y*distMeters });
            continue;
        }
        const dot = n_ab.x * bis.x/bisLen + n_ab.y * bis.y/bisLen;
        const sc  = Math.abs(dot) > 0.15 ? distMeters/dot : distMeters*4;
        result.push({ x: b.x + bis.x/bisLen * sc, y: b.y + bis.y/bisLen * sc });
    }

    return result.map(p => [p.y / mPerLat, p.x / mPerLng]);
}

// Rotate a set of corners around a center
function rotatePoints(corners, centerLat, centerLng, angleDeg) {
    const rad = angleDeg * Math.PI / 180;
    const cos = Math.cos(rad), sin = Math.sin(rad);
    const mPerLat = 111320;
    const mPerLng = 111320 * Math.cos(centerLat * Math.PI / 180);

    return corners.map(([lat, lng]) => {
        const dy = (lat - centerLat) * mPerLat;
        const dx = (lng - centerLng) * mPerLng;
        const rx = dx * cos - dy * sin;
        const ry = dx * sin + dy * cos;
        return [centerLat + ry / mPerLat, centerLng + rx / mPerLng];
    });
}

// Build rectangle corners for house (W x H meters) centered at [lat,lng]
function buildHouseCorners(lat, lng, wMeters, hMeters, rotDeg) {
    const mPerLat = 111320;
    const mPerLng = 111320 * Math.cos(lat * Math.PI / 180);
    const hw = wMeters / 2, hh = hMeters / 2;
    const corners = [
        [lat + hh/mPerLat, lng - hw/mPerLng],
        [lat + hh/mPerLat, lng + hw/mPerLng],
        [lat - hh/mPerLat, lng + hw/mPerLng],
        [lat - hh/mPerLat, lng - hw/mPerLng],
    ];
    if (rotDeg !== 0) {
        return rotatePoints(corners, lat, lng, rotDeg);
    }
    return corners;
}

// ================================================================
// Map setup
// ================================================================
let map, plotPolygon, setbackPolygon, houseLayer, houseMarker, pvArrow;
let houseLat = null, houseLng = null;
let plotLatLngs = [];

window.addEventListener('DOMContentLoaded', () => {
    // Init map
    map = L.map('plotMap', { zoomControl: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 22
    }).addTo(map);

    if (!PLOT_WKT) {
        map.setView([52, 19], 7);
        return;
    }

    // Parse WKT
    plotLatLngs = parseWKTtoLatLng(PLOT_WKT);
    if (plotLatLngs.length < 3) {
        map.setView([52, 19], 7);
        return;
    }

    // Działka polygon
    plotPolygon = L.polygon(plotLatLngs, {
        color: '#16a34a',
        weight: 2,
        fillColor: '#4ade80',
        fillOpacity: 0.2,
    }).addTo(map);

    // Auto-fit
    map.fitBounds(plotPolygon.getBounds(), { padding: [40, 40] });

    // Centroid
    const center = centroidLatLng(plotLatLngs);

    // PV info
    updatePvInfo(center[0]);

    // Linie zabudowy
    updateSetbackLayer();

    // House position
    if (PLOT_DATA.house_x && PLOT_DATA.house_y) {
        const pos = puwg92toLatLng(PLOT_DATA.house_x, PLOT_DATA.house_y);
        houseLat = pos[0];
        houseLng = pos[1];
    } else {
        houseLat = center[0];
        houseLng = center[1];
    }

    // Dom
    buildHouseLayer();

    // PV arrow (south direction from centroid)
    buildPvArrow(center[0], center[1]);

    // House info
    updateHouseInfo();
});

function updatePvInfo(lat) {
    const optTilt = Math.round(lat - 5);
    document.getElementById('pvInfo').innerHTML =
        `<strong>Szerokość geogr.:</strong> ~${lat.toFixed(1)}°N<br>` +
        `<strong>Optymalny azymut:</strong> 180° (południe)<br>` +
        `<strong>Optymalny kąt paneli:</strong> ${optTilt}–${optTilt+8}°<br>` +
        `<span class="text-xs">Strzałka żółta = kierunek S</span>`;
}

function buildHouseLayer() {
    const w   = parseFloat(document.getElementById('houseW').value) || 12;
    const h   = parseFloat(document.getElementById('houseH').value) || 9;
    const rot = parseFloat(document.getElementById('houseRot').value) || 0;

    // Remove old layers
    if (houseLayer) map.removeLayer(houseLayer);
    if (houseMarker) map.removeLayer(houseMarker);

    const corners = buildHouseCorners(houseLat, houseLng, w, h, rot);

    houseLayer = L.polygon(corners, {
        color: '#1d4ed8',
        weight: 2,
        fillColor: '#60a5fa',
        fillOpacity: 0.55,
    }).addTo(map);

    // Draggable marker at center
    const dragIcon = L.divIcon({
        html: `<div style="width:22px;height:22px;background:#1d4ed8;border:2px solid white;border-radius:50%;cursor:move;display:flex;align-items:center;justify-content:center;font-size:10px;color:white;box-shadow:0 2px 4px rgba(0,0,0,.3);">⊕</div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11],
        className: '',
    });

    houseMarker = L.marker([houseLat, houseLng], { draggable: true, icon: dragIcon }).addTo(map);

    houseMarker.on('drag', function(e) {
        const pos = e.target.getLatLng();
        houseLat = pos.lat;
        houseLng = pos.lng;
        updateHouseRect();
    });

    updateHouseInfo();
}

function updateHouseRect() {
    const w   = parseFloat(document.getElementById('houseW').value) || 12;
    const h   = parseFloat(document.getElementById('houseH').value) || 9;
    const rot = parseFloat(document.getElementById('houseRot').value) || 0;

    if (!houseLayer || !houseLat) return;

    const corners = buildHouseCorners(houseLat, houseLng, w, h, rot);
    houseLayer.setLatLngs(corners);

    if (houseMarker) houseMarker.setLatLng([houseLat, houseLng]);

    updateHouseInfo();
}

function updateSetbackLayer() {
    if (!plotLatLngs.length) return;

    const sb = Math.min(
        parseFloat(document.getElementById('sb_front').value) || 0,
        parseFloat(document.getElementById('sb_back').value) || 0,
        parseFloat(document.getElementById('sb_left').value) || 0,
        parseFloat(document.getElementById('sb_right').value) || 0,
    );

    if (setbackPolygon) map.removeLayer(setbackPolygon);

    if (sb <= 0) return;

    const offsetPts = offsetPolygonWGS84(plotLatLngs, sb);
    if (offsetPts.length < 3) return;

    setbackPolygon = L.polygon(offsetPts, {
        color: '#f97316',
        weight: 2,
        dashArray: '8 5',
        fillColor: '#6366f1',
        fillOpacity: 0.04,
    }).addTo(map);
}

function buildPvArrow(lat, lng) {
    if (pvArrow) map.removeLayer(pvArrow);

    const mPerLat = 111320;
    const arrowLenM = 30;

    // South = decrease lat
    const endLat = lat - arrowLenM / mPerLat;

    pvArrow = L.polyline([[lat, lng], [endLat, lng]], {
        color: '#fbbf24',
        weight: 3,
        dashArray: null,
    }).addTo(map);

    // Arrow head marker
    const arrowIcon = L.divIcon({
        html: `<div style="font-size:16px;line-height:1;">▼</div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8],
        className: '',
    });
    L.marker([endLat, lng], { icon: arrowIcon, interactive: false }).addTo(map);

    // Sun label
    const sunIcon = L.divIcon({
        html: `<div style="font-size:14px;line-height:1;" title="Południe (optymalne PV)">☀️</div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 16],
        className: '',
    });
    L.marker([lat, lng], { icon: sunIcon, interactive: false }).addTo(map);
}

function updateHouseInfo() {
    const w = parseFloat(document.getElementById('houseW').value) || 12;
    const h = parseFloat(document.getElementById('houseH').value) || 9;
    const houseArea = w * h;

    // Approximate plot area in m² from lat/lng polygon
    let plotArea = 0;
    if (plotLatLngs.length > 2) {
        const mPerLat = 111320;
        const refLat = plotLatLngs[0][0];
        const mPerLng = 111320 * Math.cos(refLat * Math.PI / 180);
        const pts = plotLatLngs.map(p => ({ x: p[1] * mPerLng, y: p[0] * mPerLat }));
        for (let i = 0; i < pts.length; i++) {
            const j = (i+1) % pts.length;
            plotArea += pts[i].x * pts[j].y - pts[j].x * pts[i].y;
        }
        plotArea = Math.abs(plotArea / 2);
    }

    const ratio = plotArea > 0 ? ((houseArea / plotArea) * 100).toFixed(1) : 0;
    const el = document.getElementById('houseAreaInfo');
    el.textContent = `Rzut domu: ${houseArea} m² · Wskaźnik zabudowy: ${ratio}%`;
    el.className = 'text-sm mt-2 ' + (parseFloat(ratio) > 30 ? 'text-red-600' : 'text-indigo-600');
    el.classList.remove('hidden');
}

function centerHouse() {
    if (!plotLatLngs.length) return;
    const c = centroidLatLng(plotLatLngs);
    houseLat = c[0];
    houseLng = c[1];
    buildHouseLayer();
}

function fitPlot() {
    if (plotPolygon) map.fitBounds(plotPolygon.getBounds(), { padding: [40, 40] });
}

function syncSetbacks() {
    ['front','back','left','right'].forEach(k => {
        document.getElementById(`sb_${k}_hidden`).value = document.getElementById(`sb_${k}`).value;
    });
}

async function saveHousePosition() {
    if (!houseLat) return;

    const btn = document.querySelector('[onclick="saveHousePosition()"]');
    btn.textContent = 'Zapisuję...';
    btn.disabled = true;

    // Convert WGS84 back to PUWG92
    const puwg = latLngToPuwg92(houseLat, houseLng);

    try {
        const resp = await fetch(SAVE_URL, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                house_x:        puwg[0],
                house_y:        puwg[1],
                house_width:    parseFloat(document.getElementById('houseW').value),
                house_height:   parseFloat(document.getElementById('houseH').value),
                house_rotation: parseFloat(document.getElementById('houseRot').value),
            })
        });
        const data = await resp.json();
        btn.textContent = data.ok ? '✓ Zapisano' : '✗ Błąd';
    } catch {
        btn.textContent = '✗ Błąd';
    }
    setTimeout(() => { btn.textContent = '💾 Zapisz pozycję'; btn.disabled = false; }, 2000);
}
</script>
</x-layouts.app>
