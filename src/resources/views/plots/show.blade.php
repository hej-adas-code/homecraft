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

    // Wyciągnij współrzędne pierwszego zewnętrznego ringu
    // Działa dla: POLYGON((coords)), POLYGON((outer),(hole)),
    //             MULTIPOLYGON(((coords),...),((coords2),...))
    const coordStr = extractFirstRing(wkt);
    if (!coordStr) return [];

    return coordStr.trim().split(',').map(p => {
        const parts = p.trim().split(/\s+/);
        const x = parseFloat(parts[0]), y = parseFloat(parts[1]);
        if (isNaN(x) || isNaN(y)) return null;
        return puwg92toLatLng(x, y);
    }).filter(Boolean);
}

function extractFirstRing(wkt) {
    // Znajdź pierwszy "((..." — początek zewnętrznego ringu
    const idx = wkt.indexOf('((');
    if (idx === -1) {
        // Spróbuj pojedynczego "(..."
        const m = wkt.match(/\(([^()]+)\)/);
        return m ? m[1] : null;
    }
    // Idź po znakach i zbierz wszystko do pierwszego zamknięcia ")"
    // po głębokości 2 (otwieramy "((" więc głębokość = 2)
    let depth = 0;
    let start = -1;
    for (let i = idx; i < wkt.length; i++) {
        if (wkt[i] === '(') {
            depth++;
            if (depth === 2) start = i + 1; // zacznij zbierać po "(("
        } else if (wkt[i] === ')') {
            depth--;
            if (depth === 1 && start !== -1) {
                // Zamknęliśmy pierwszy ring
                return wkt.substring(start, i);
            }
        }
    }
    return null;
}

// Compute approximate centroid (avg) of polygon lat/lng
function centroidLatLng(latlngs) {
    let lat = 0, lng = 0;
    latlngs.forEach(p => { lat += p[0]; lng += p[1]; });
    return [lat / latlngs.length, lng / latlngs.length];
}

// Azymut (0=N, 90=E …) → id pola setback
function azimuthToFieldId(az) {
    const a = ((az % 360) + 360) % 360;
    if (a >= 315 || a < 45)  return 'sb_back';   // N
    if (a >= 45  && a < 135) return 'sb_left';   // E
    if (a >= 135 && a < 225) return 'sb_front';  // S
    return 'sb_right';                            // W
}

// Setback value dla danego azymutu zewnętrznej normalnej
function setbackForAzimuth(az) {
    const id = azimuthToFieldId(az);
    return parseFloat(document.getElementById(id).value) || 0;
}

// ── Uproszczenie polygonu — usuwa krótkie krawędzie (skosy, ścięcia naroży) ───
function simplifyPolygon(pts) {
    const n = pts.length;
    if (n < 3) return pts;

    // Długości wszystkich krawędzi
    const lens = pts.map((p, i) => {
        const q = pts[(i+1) % n];
        return Math.hypot(q.x - p.x, q.y - p.y);
    });

    // Próg: max z (3m  lub  max_setback × 2)
    const maxSb = Math.max(
        parseFloat(document.getElementById('sb_front').value) || 0,
        parseFloat(document.getElementById('sb_back').value)  || 0,
        parseFloat(document.getElementById('sb_left').value)  || 0,
        parseFloat(document.getElementById('sb_right').value) || 0,
    );
    const threshold = Math.max(3, maxSb * 2);

    // Usuń wierzchołki, z których wychodzi krótka krawędź
    let result = pts.filter((_, i) => lens[i] >= threshold);
    return result.length >= 3 ? result : pts; // fallback gdy za agresywne
}

// ── Przecięcie dwóch nieskończonych prostych ─────────────────────────────────
function lineIntersect(p1, d1, p2, d2) {
    const cross = d1.x * d2.y - d1.y * d2.x;
    if (Math.abs(cross) < 1e-10) {
        // Równoległe — zwróć punkt pośredni
        return { x: (p1.x + p2.x) / 2, y: (p1.y + p2.y) / 2 };
    }
    const dx = p2.x - p1.x, dy = p2.y - p1.y;
    const t  = (dx * d2.y - dy * d2.x) / cross;
    return { x: p1.x + t * d1.x, y: p1.y + t * d1.y };
}

// ── Sutherland-Hodgman: przytnij polygon subject do clip polygon ─────────────
// Clip polygon musi być wypukły (convex). Oba jako tablice {x,y}.
function shClip(subject, clip) {
    let output = [...subject];
    const n = clip.length;
    for (let i = 0; i < n && output.length > 0; i++) {
        const A = clip[i], B = clip[(i+1) % n];
        const input = output;
        output = [];
        for (let j = 0; j < input.length; j++) {
            const P = input[j];
            const Q = input[(j+1) % input.length];
            const insP = shInside(P, A, B);
            const insQ = shInside(Q, A, B);
            if (insP) {
                output.push(P);
                if (!insQ) { const x = shCross(P, Q, A, B); if (x) output.push(x); }
            } else if (insQ) {
                const x = shCross(P, Q, A, B); if (x) output.push(x);
            }
        }
    }
    return output;
}
function shInside(P, A, B) {
    return (B.x - A.x) * (P.y - A.y) - (B.y - A.y) * (P.x - A.x) >= -1e-9;
}
function shCross(P, Q, A, B) {
    const d1 = { x: Q.x-P.x, y: Q.y-P.y };
    const d2 = { x: B.x-A.x, y: B.y-A.y };
    const cr  = d1.x*d2.y - d1.y*d2.x;
    if (Math.abs(cr) < 1e-12) return null;
    const t = ((A.x-P.x)*d2.y - (A.y-P.y)*d2.x) / cr;
    return { x: P.x + t*d1.x, y: P.y + t*d1.y };
}
// ── Strefa zabudowy: per-edge offset na uproszczonym polygonie ────────────────
function buildZonePolygon(latlngs) {
    if (latlngs.length < 3) return null;

    const refLat  = latlngs[0][0];
    const mPerLat = 111320;
    const mPerLng = 111320 * Math.cos(refLat * Math.PI / 180);

    // Konwersja do metrów lokalnych
    let pts = latlngs.map(p => ({ x: p[1] * mPerLng, y: p[0] * mPerLat }));

    // 1. Uprość — usuń skosy (krótkie krawędzie)
    const origPts = pts.map(p => ({ x: p.x, y: p.y })); // oryginał do clippingu
    pts = simplifyPolygon(pts);
    const n = pts.length;
    if (n < 3) return null;

    // 2. Orientacja (shoelace)
    let area = 0;
    for (let i = 0; i < n; i++) {
        const j = (i+1) % n;
        area += pts[i].x * pts[j].y - pts[j].x * pts[i].y;
    }
    const isCCW = area > 0;

    // 3. Dla każdej krawędzi: przesunięta prosta (p + kierunek)
    const offEdges = pts.map((A, i) => {
        const B   = pts[(i+1) % n];
        const dx  = B.x - A.x, dy = B.y - A.y;
        const len = Math.hypot(dx, dy);
        if (len < 0.1) return null;

        // Zewnętrzna normalna
        const outNx = isCCW ?  dy/len : -dy/len;
        const outNy = isCCW ? -dx/len :  dx/len;
        const az    = (Math.atan2(outNx, outNy) * 180 / Math.PI + 360) % 360;
        const dist  = setbackForAzimuth(az);

        // Wewnętrzna normalna × dist
        return {
            p: { x: A.x - outNx * dist, y: A.y - outNy * dist },
            d: { x: dx, y: dy },
        };
    });

    // 4. Wierzchołki strefy = przecięcia sąsiednich przesuniętych prostych
    //    + clamp: nie pozwól wierzchołkowi odejść dalej niż maxSb*4 od oryginału
    const maxSb = Math.max(
        parseFloat(document.getElementById('sb_front').value) || 0,
        parseFloat(document.getElementById('sb_back').value)  || 0,
        parseFloat(document.getElementById('sb_left').value)  || 0,
        parseFloat(document.getElementById('sb_right').value) || 0,
    );
    const clampDist = Math.max(maxSb * 4, 1);

    const result = [];
    for (let i = 0; i < n; i++) {
        const e1 = offEdges[i];
        const e2 = offEdges[(i+1) % n];
        if (!e1 || !e2) continue;
        let pt = lineIntersect(e1.p, e1.d, e2.p, e2.d);

        // Clamp: jeśli przecięcie jest zbyt daleko od oryginalnego wierzchołka
        const orig = pts[i];
        const d = Math.hypot(pt.x - orig.x, pt.y - orig.y);
        if (d > clampDist) {
            const s = clampDist / d;
            pt = { x: orig.x + (pt.x - orig.x) * s, y: orig.y + (pt.y - orig.y) * s };
        }

        result.push([pt.y / mPerLat, pt.x / mPerLng]);
    }

    if (result.length < 3) return null;

    // 5. Przytnij do ORYGINALNEGO polygonu działki (z skosami)
    //    Normalizuj do CCW — S-H wymaga CCW clip polygon
    const signedAreaOrig = origPts.reduce((s, p, i) => {
        const q = origPts[(i+1) % origPts.length];
        return s + p.x * q.y - q.x * p.y;
    }, 0);
    const clipForSH = signedAreaOrig < 0 ? [...origPts].reverse() : origPts;

    const zonePts = result.map(([lat, lng]) => ({ x: lng * mPerLng, y: lat * mPerLat }));
    const clipped = shClip(zonePts, clipForSH);

    if (clipped.length >= 3) {
        return clipped.map(p => [p.y / mPerLat, p.x / mPerLng]);
    }
    return result; // fallback
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
let map, plotPolygon, setbackLayers = [], houseLayer, houseMarker;
let houseLat = null, houseLng = null;
let plotLatLngs = [];

window.addEventListener('DOMContentLoaded', () => {
    // Init map
    map = L.map('plotMap', { zoomControl: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 22,
        keepBuffer: 4,
    }).addTo(map);
    // Fix: kafelki nie ładują się gdy kontener nie ma wymiaru w chwili init
    setTimeout(() => map.invalidateSize(), 150);

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
    setTimeout(() => map.invalidateSize(), 200);

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
        `<strong>Optymalny kąt paneli:</strong> ${optTilt}–${optTilt+8}°<br><br>` +
        `<span class="text-xs font-semibold text-amber-700">Sektory na mapie:</span><br>` +
        `<span class="text-xs">🟢 &gt;95% — południe ±25°</span><br>` +
        `<span class="text-xs">🟡 85–95% — ±45° (SE/SW)</span><br>` +
        `<span class="text-xs">🟠 70–85% — ±65° (ESE/WSW)</span>`;
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
    setbackLayers.forEach(l => map.removeLayer(l));
    setbackLayers = [];
    if (!plotLatLngs.length) return;

    // ── Zamknięty wielokąt strefy zabudowy ────────────────────────────────────
    const zone = buildZonePolygon(plotLatLngs);
    if (zone && zone.length >= 3) {
        const zoneLayer = L.polygon(zone, {
            color: '#f97316',
            weight: 2,
            dashArray: '10 6',
            fillColor: '#fef3c7',
            fillOpacity: 0.18,
        }).addTo(map);
        setbackLayers.push(zoneLayer);
    }

    // ── Klikalne krawędzie działki — popup z inputem odległości ───────────────
    const refLat  = plotLatLngs[0][0];
    const mPerLat = 111320;
    const mPerLng = 111320 * Math.cos(refLat * Math.PI / 180);
    const n = plotLatLngs.length;

    for (let i = 0; i < n; i++) {
        const j = (i+1) % n;
        const A = plotLatLngs[i], B = plotLatLngs[j];

        // Azymut zewnętrznej normalnej tej krawędzi
        const dx = (B[1] - A[1]) * mPerLng;
        const dy = (B[0] - A[0]) * mPerLat;
        const len = Math.hypot(dx, dy);
        if (len < 0.5) continue;

        // Pole z wyznacznikiem orientacji (uproszczone — isCCW z całego polygon)
        // Liczymy tylko raz, poza pętlą:
        let area2 = 0;
        for (let k = 0; k < n; k++) {
            const l = (k+1) % n;
            area2 += (plotLatLngs[k][1]*mPerLng) * (plotLatLngs[l][0]*mPerLat)
                   - (plotLatLngs[l][1]*mPerLng) * (plotLatLngs[k][0]*mPerLat);
        }
        const ccw = area2 > 0;
        const outNx = ccw ?  dy/len : -dy/len;
        const outNy = ccw ? -dx/len :  dx/len;
        const az    = (Math.atan2(outNx, outNy) * 180 / Math.PI + 360) % 360;
        const fieldId = azimuthToFieldId(az);

        // Gruba niewidoczna linia do klikania
        const edgeLine = L.polyline([A, B], {
            color: 'transparent', weight: 12, opacity: 0,
        }).addTo(map);

        // Na hover — podświetl
        edgeLine.on('mouseover', function() {
            this.setStyle({ color: '#f97316', opacity: 0.5, weight: 8 });
            this.bindTooltip(
                `Kliknij, aby ustawić odległość<br>od tej granicy`,
                { permanent: false, direction: 'auto' }
            ).openTooltip();
        });
        edgeLine.on('mouseout', function() {
            this.setStyle({ color: 'transparent', opacity: 0, weight: 12 });
            this.unbindTooltip();
        });

        // Na klik — popup z inputem
        edgeLine.on('click', function(e) {
            const current = parseFloat(document.getElementById(fieldId).value) || 0;
            const fieldLabel = {
                sb_front: 'Południe (przód)',
                sb_back:  'Północ (tył)',
                sb_left:  'Wschód (lewo)',
                sb_right: 'Zachód (prawo)',
            }[fieldId] || fieldId;

            const popup = L.popup({ closeButton: true, maxWidth: 240 })
                .setLatLng(e.latlng)
                .setContent(`
                    <div style="font-size:13px;padding:4px 0">
                        <b style="font-size:12px;color:#92400e">Linia zabudowy — ${fieldLabel}</b><br>
                        <div style="margin-top:8px;display:flex;align-items:center;gap:6px">
                            <input id="popupDist" type="number" min="0" max="30" step="0.5"
                                value="${current}"
                                style="width:72px;padding:4px 6px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">
                            <span style="color:#6b7280;font-size:12px">metrów</span>
                            <button onclick="applySetback('${fieldId}', document.getElementById('popupDist').value)"
                                style="padding:4px 10px;background:#f97316;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">
                                OK
                            </button>
                        </div>
                    </div>`)
                .openOn(map);
        });

        setbackLayers.push(edgeLine);
    }
}

function applySetback(fieldId, value) {
    const v = Math.max(0, Math.min(30, parseFloat(value) || 0));
    document.getElementById(fieldId).value = v;
    map.closePopup();
    updateSetbackLayer();
}

// Oblicz punkt w odległości distM od (lat,lng) w kierunku bearingDeg
function destPoint(lat, lng, bearingDeg, distM) {
    const R   = 6371000;
    const d   = distM / R;
    const brg = bearingDeg * Math.PI / 180;
    const φ1  = lat * Math.PI / 180;
    const λ1  = lng * Math.PI / 180;
    const φ2  = Math.asin(Math.sin(φ1)*Math.cos(d) + Math.cos(φ1)*Math.sin(d)*Math.cos(brg));
    const λ2  = λ1 + Math.atan2(Math.sin(brg)*Math.sin(d)*Math.cos(φ1), Math.cos(d)-Math.sin(φ1)*Math.sin(φ2));
    return [φ2 * 180/Math.PI, λ2 * 180/Math.PI];
}

// Punkty sektora kołowego (do L.polygon)
function sectorPoints(lat, lng, radiusM, startBrg, endBrg, steps) {
    steps = steps || 32;
    const pts = [[lat, lng]];
    for (let i = 0; i <= steps; i++) {
        const brg = startBrg + (endBrg - startBrg) * i / steps;
        pts.push(destPoint(lat, lng, brg, radiusM));
    }
    return pts;
}

let pvLayers = [];

function buildPvArrow(lat, lng) {
    // Usuń poprzednie warstwy PV
    pvLayers.forEach(l => map.removeLayer(l));
    pvLayers = [];

    // Radius = 30% szerokości działki, min 15m, max 80m
    let radius = 40;
    if (plotLatLngs.length > 1) {
        const lats = plotLatLngs.map(p => p[0]);
        const lngs = plotLatLngs.map(p => p[1]);
        const mPerLat = 111320;
        const mPerLng = 111320 * Math.cos(lat * Math.PI / 180);
        const plotW   = (Math.max(...lngs) - Math.min(...lngs)) * mPerLng;
        const plotH   = (Math.max(...lats) - Math.min(...lats)) * mPerLat;
        radius = Math.max(15, Math.min(80, Math.max(plotW, plotH) * 0.28));
    }

    // Sektory od zewnątrz do środka (rysujemy od najszerszego)
    // Strefa 3: ±65° (115°-245°) — 70-85%, pomarańczowy
    const s3 = L.polygon(sectorPoints(lat, lng, radius, 115, 245), {
        color: 'transparent', fillColor: '#fb923c', fillOpacity: 0.25,
    }).addTo(map);
    pvLayers.push(s3);

    // Strefa 2: ±45° (135°-225°) — 85-95%, żółtozielony
    const s2 = L.polygon(sectorPoints(lat, lng, radius, 135, 225), {
        color: 'transparent', fillColor: '#facc15', fillOpacity: 0.30,
    }).addTo(map);
    pvLayers.push(s2);

    // Strefa 1: ±25° (155°-205°) — >95%, zielony
    const s1 = L.polygon(sectorPoints(lat, lng, radius, 155, 205), {
        color: 'transparent', fillColor: '#4ade80', fillOpacity: 0.45,
    }).addTo(map);
    pvLayers.push(s1);

    // Linia południa — optymum (180°)
    const southPt = destPoint(lat, lng, 180, radius);
    const arrow = L.polyline([[lat, lng], southPt], {
        color: '#16a34a', weight: 2.5, dashArray: '6 3',
    }).addTo(map);
    pvLayers.push(arrow);

    // Etykieta optimum
    const southLabel = L.divIcon({
        html: `<div style="background:#16a34a;color:#fff;font-size:10px;font-weight:600;padding:1px 5px;border-radius:4px;white-space:nowrap;">S 180° ☀️</div>`,
        iconSize: [70, 18], iconAnchor: [35, -2], className: '',
    });
    const lbl = L.marker(southPt, { icon: southLabel, interactive: false }).addTo(map);
    pvLayers.push(lbl);

    // Etykiety kątowe na brzegach sektora
    [
        [115, '-65°\n70%', '#fb923c'],
        [135, '-45°\n85%', '#ca8a04'],
        [225, '+45°\n85%', '#ca8a04'],
        [245, '+65°\n70%', '#fb923c'],
    ].forEach(([brg, txt, color]) => {
        const pt  = destPoint(lat, lng, brg, radius * 1.08);
        const ico = L.divIcon({
            html: `<div style="color:${color};font-size:9px;font-weight:600;white-space:nowrap;line-height:1.2;">${txt.replace('\n','<br>')}</div>`,
            iconSize: [40, 24], iconAnchor: [20, 12], className: '',
        });
        pvLayers.push(L.marker(pt, { icon: ico, interactive: false }).addTo(map));
    });
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
