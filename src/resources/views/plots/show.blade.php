<x-layouts.app title="Działka {{ $plot->plot_number }}">
<x-slot name="actions">
    <a href="{{ route('plots.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Moje działki</a>
</x-slot>

<div class="flex gap-6 items-start">

    {{-- Panel boczny --}}
    <div class="w-72 flex-shrink-0 space-y-4">

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
                        onchange="updateHouseDims()">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Głębokość (m)</label>
                    <input type="number" id="houseH" value="{{ $plot->house_height ?? 9 }}" min="3" max="50" step="0.5"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        onchange="updateHouseDims()">
                </div>
            </div>
            <div class="mb-3">
                <label class="text-xs text-gray-500">Obrót (°)</label>
                <input type="range" id="houseRot" min="-180" max="180" step="1" value="{{ $plot->house_rotation ?? 0 }}"
                    class="w-full" oninput="document.getElementById('houseRotVal').textContent=this.value+'°'; editor && editor.draw()">
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
                        onchange="editor && editor.draw()">
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
                <div class="flex items-center gap-2"><span class="w-4 h-3 rounded bg-blue-400 opacity-70"></span> Dom</div>
                <div class="flex items-center gap-2"><span class="w-4 h-1 border-t-2 border-yellow-400 inline-block"></span> Kierunek słońca (S)</div>
            </div>
        </div>

    </div>

    {{-- Canvas --}}
    <div class="flex-1">
        <div class="bg-white rounded-xl border border-gray-200 p-4 relative">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-400">Przeciągnij dom myszką · Scroll = zoom · Prawy przycisk = pan</p>
                <div class="flex gap-2">
                    <button onclick="editor && editor.resetView()" class="px-3 py-1 text-xs border rounded hover:bg-gray-50">Reset widoku</button>
                    <button onclick="saveHousePosition()" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">💾 Zapisz pozycję</button>
                </div>
            </div>
            <canvas id="plotCanvas" class="w-full rounded-lg bg-gray-50 cursor-crosshair" style="height:580px"></canvas>
            <div id="canvasMsg" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <p class="text-gray-400 text-sm" id="canvasMsgText">Ładowanie działki...</p>
            </div>
        </div>
    </div>
</div>

<script>
// --- Data from server ---
const PLOT_WKT  = @json($plot->geometry_wkt);
const PLOT_DATA = {
    house_x:        {{ $plot->house_x ?? 'null' }},
    house_y:        {{ $plot->house_y ?? 'null' }},
    house_width:    {{ $plot->house_width ?? 12 }},
    house_height:   {{ $plot->house_height ?? 9 }},
    house_rotation: {{ $plot->house_rotation ?? 0 }},
};
const SAVE_URL  = "{{ route('plots.update-house', $plot) }}";
const CSRF      = "{{ csrf_token() }}";

// ================================================================
// PlotEditor class
// ================================================================
class PlotEditor {
    constructor(canvasId, wkt) {
        this.canvas  = document.getElementById(canvasId);
        this.ctx     = this.canvas.getContext('2d');
        this.polygon = [];
        this.bounds  = null;
        this.viewScale   = 1;
        this.viewOffsetX = 0;
        this.viewOffsetY = 0;

        // House state (in plot metres)
        this.houseX   = PLOT_DATA.house_x;
        this.houseY   = PLOT_DATA.house_y;
        this.dragging = false;
        this.dragOffX = 0;
        this.dragOffY = 0;

        this.resizeCanvas();
        window.addEventListener('resize', () => { this.resizeCanvas(); this.resetView(); this.draw(); });

        if (wkt) {
            this.polygon = this.parseWKT(wkt);
            if (this.polygon.length > 2) {
                this.calcBounds();
                this.resetView();
                this.initLat();
                this.draw();
                document.getElementById('canvasMsg').style.display = 'none';
            } else {
                document.getElementById('canvasMsgText').textContent = 'Nie można odczytać geometrii WKT.';
            }
        }

        this.setupEvents();
    }

    resizeCanvas() {
        const rect = this.canvas.parentElement.getBoundingClientRect();
        this.canvas.width  = rect.width - 32;
        this.canvas.height = 580;
    }

    // ---- WKT parsing ----
    parseWKT(wkt) {
        // Support POLYGON((...)) and MULTIPOLYGON(((...)))
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
            return { x: parseFloat(parts[0]), y: parseFloat(parts[1]) };
        }).filter(p => !isNaN(p.x) && !isNaN(p.y));
    }

    calcBounds() {
        const xs = this.polygon.map(p => p.x);
        const ys = this.polygon.map(p => p.y);
        this.bounds = {
            minX: Math.min(...xs), maxX: Math.max(...xs),
            minY: Math.min(...ys), maxY: Math.max(...ys),
        };
        this.bounds.cx = (this.bounds.minX + this.bounds.maxX) / 2;
        this.bounds.cy = (this.bounds.minY + this.bounds.maxY) / 2;
        this.metersPerUnit = 1; // PUWG92 is in meters
    }

    resetView() {
        if (!this.bounds) return;
        const pad = 60;
        const W = this.canvas.width, H = this.canvas.height;
        const plotW = this.bounds.maxX - this.bounds.minX;
        const plotH = this.bounds.maxY - this.bounds.minY;
        const scaleX = (W - 2*pad) / plotW;
        const scaleY = (H - 2*pad) / plotH;
        this.viewScale   = Math.min(scaleX, scaleY);
        this.viewOffsetX = W/2 - this.bounds.cx * this.viewScale;
        this.viewOffsetY = H/2 + this.bounds.cy * this.viewScale;
    }

    // ---- Coordinate transforms ----
    toCanvas(x, y) {
        // PUWG92: x=easting (E), y=northing (N)
        // Canvas: right=E, up=N → flip Y
        return {
            cx: x * this.viewScale + this.viewOffsetX,
            cy: -y * this.viewScale + this.viewOffsetY
        };
    }

    fromCanvas(cx, cy) {
        return {
            x: (cx - this.viewOffsetX) / this.viewScale,
            y: -(cy - this.viewOffsetY) / this.viewScale
        };
    }

    // Approximate lat from PUWG92 northing (for sun calc)
    initLat() {
        const northing = this.bounds.cy;
        this.lat = (northing + 5300000) / 111320;
        this.updatePvInfo();
    }

    updatePvInfo() {
        const lat = this.lat || 52;
        const optTilt = Math.round(lat - 5);
        const lats = lat.toFixed(1);
        document.getElementById('pvInfo').innerHTML =
            `<strong>Szerokość geogr.:</strong> ~${lats}°N<br>` +
            `<strong>Optymalny azymut:</strong> 180° (południe ↓)<br>` +
            `<strong>Optymalny kąt paneli:</strong> ${optTilt}–${optTilt+8}°<br>` +
            `<span class="text-xs">Strzałka żółta = kierunek S (max nasłonecznienie)</span>`;
    }

    // ---- Polygon offset (inward) ----
    offsetPolygon(pts, dist) {
        const n = pts.length;
        if (n < 3 || dist <= 0) return pts;

        // Compute signed area to determine orientation
        let area = 0;
        for (let i = 0; i < n; i++) {
            const j = (i+1) % n;
            area += (pts[j].x - pts[i].x) * (pts[j].y + pts[i].y);
        }
        // In PUWG92 coords (Y up): CCW = positive area = exterior
        // sign=1 rotates normal inward for CCW polygon
        const sign = area > 0 ? -1 : 1;

        const result = [];
        for (let i = 0; i < n; i++) {
            const a = pts[(i-1+n) % n];
            const b = pts[i];
            const c = pts[(i+1) % n];

            const ab = { x: b.x-a.x, y: b.y-a.y };
            const ab_len = Math.hypot(ab.x, ab.y);
            if (ab_len < 1e-9) { result.push({...b}); continue; }
            const n_ab = { x: sign*ab.y/ab_len, y: -sign*ab.x/ab_len };

            const bc = { x: c.x-b.x, y: c.y-b.y };
            const bc_len = Math.hypot(bc.x, bc.y);
            if (bc_len < 1e-9) { result.push({...b}); continue; }
            const n_bc = { x: sign*bc.y/bc_len, y: -sign*bc.x/bc_len };

            const bis = { x: n_ab.x + n_bc.x, y: n_ab.y + n_bc.y };
            const bisLen = Math.hypot(bis.x, bis.y);
            if (bisLen < 1e-6) {
                result.push({ x: b.x + n_ab.x*dist, y: b.y + n_ab.y*dist });
                continue;
            }
            const dot = n_ab.x * bis.x/bisLen + n_ab.y * bis.y/bisLen;
            const sc  = Math.abs(dot) > 0.15 ? dist/dot : dist*4;
            result.push({
                x: b.x + bis.x/bisLen * sc,
                y: b.y + bis.y/bisLen * sc,
            });
        }
        return result;
    }

    // ---- Drawing ----
    draw() {
        const ctx  = this.ctx;
        const W    = this.canvas.width;
        const H    = this.canvas.height;
        ctx.clearRect(0, 0, W, H);

        if (!this.polygon.length) return;

        // Background
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        this.drawGrid();
        this.drawPlot();
        this.drawSetbacks();
        this.drawHouse();
        this.drawCompass(W - 70, H - 70, 40);
        this.drawSunArrow(W - 70, H - 70);
        this.drawScaleBar();
        this.updateHouseInfo();
    }

    drawGrid() {
        const ctx = this.ctx;
        const W = this.canvas.width, H = this.canvas.height;
        const gridM = this.niceGridStep();
        const p0 = this.fromCanvas(0, 0);
        const p1 = this.fromCanvas(W, H);

        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 0.5;

        const startX = Math.floor(Math.min(p0.x,p1.x)/gridM)*gridM;
        const endX   = Math.ceil(Math.max(p0.x,p1.x)/gridM)*gridM;
        const startY = Math.floor(Math.min(p0.y,p1.y)/gridM)*gridM;
        const endY   = Math.ceil(Math.max(p0.y,p1.y)/gridM)*gridM;

        for (let x = startX; x <= endX; x += gridM) {
            const {cx} = this.toCanvas(x, 0);
            ctx.beginPath(); ctx.moveTo(cx, 0); ctx.lineTo(cx, H); ctx.stroke();
        }
        for (let y = startY; y <= endY; y += gridM) {
            const {cy} = this.toCanvas(0, y);
            ctx.beginPath(); ctx.moveTo(0, cy); ctx.lineTo(W, cy); ctx.stroke();
        }

        // Grid labels
        ctx.fillStyle = '#9ca3af';
        ctx.font = '9px sans-serif';
        for (let x = startX; x <= endX; x += gridM*2) {
            const {cx} = this.toCanvas(x, 0);
            if (cx > 10 && cx < W-10) ctx.fillText(`${Math.round((x - this.bounds.minX))}m`, cx+2, H-4);
        }
    }

    niceGridStep() {
        const targetPx = 80;
        const meters   = targetPx / this.viewScale;
        const mag = Math.pow(10, Math.floor(Math.log10(meters)));
        const nice = [1,2,5,10];
        for (const n of nice) if (n*mag >= meters) return n*mag;
        return mag*10;
    }

    drawPlot() {
        const ctx = this.ctx;
        const pts = this.polygon;

        ctx.beginPath();
        const p0 = this.toCanvas(pts[0].x, pts[0].y);
        ctx.moveTo(p0.cx, p0.cy);
        for (let i=1; i<pts.length; i++) {
            const p = this.toCanvas(pts[i].x, pts[i].y);
            ctx.lineTo(p.cx, p.cy);
        }
        ctx.closePath();
        ctx.fillStyle   = 'rgba(74,222,128,0.15)';
        ctx.strokeStyle = '#16a34a';
        ctx.lineWidth   = 2;
        ctx.fill();
        ctx.stroke();

        // Area label
        if (this.bounds) {
            const ctr = this.toCanvas(this.bounds.cx, this.bounds.cy);
            ctx.fillStyle = '#15803d';
            ctx.font = 'bold 13px sans-serif';
            ctx.textAlign = 'center';
            const plotW = Math.round(this.bounds.maxX - this.bounds.minX);
            const plotH = Math.round(this.bounds.maxY - this.bounds.minY);
            ctx.fillText(`${plotW}m × ${plotH}m`, ctr.cx, ctr.cy - 8);
            ctx.font = '11px sans-serif';
            ctx.fillStyle = '#166534';
            ctx.fillText(`~${Math.round(this.polygonArea(this.polygon))} m²`, ctr.cx, ctr.cy + 8);
            ctx.textAlign = 'left';
        }
    }

    drawSetbacks() {
        const ctx = this.ctx;
        const minSb = Math.min(
            parseFloat(document.getElementById('sb_front').value)||0,
            parseFloat(document.getElementById('sb_back').value)||0,
            parseFloat(document.getElementById('sb_left').value)||0,
            parseFloat(document.getElementById('sb_right').value)||0,
        );
        if (minSb <= 0) return;

        const offset = this.offsetPolygon(this.polygon, minSb);
        if (offset.length < 3) return;

        ctx.beginPath();
        const p0 = this.toCanvas(offset[0].x, offset[0].y);
        ctx.moveTo(p0.cx, p0.cy);
        for (let i=1; i<offset.length; i++) {
            const p = this.toCanvas(offset[i].x, offset[i].y);
            ctx.lineTo(p.cx, p.cy);
        }
        ctx.closePath();
        ctx.fillStyle   = 'rgba(99,102,241,0.06)';
        ctx.strokeStyle = '#f97316';
        ctx.lineWidth   = 1.5;
        ctx.setLineDash([6,4]);
        ctx.fill();
        ctx.stroke();
        ctx.setLineDash([]);

        // Label
        const px = this.toCanvas(offset[0].x, offset[0].y);
        ctx.fillStyle = '#c2410c';
        ctx.font = '10px sans-serif';
        ctx.fillText(`linia zabudowy (min ${minSb}m)`, px.cx+4, px.cy-4);
    }

    drawHouse() {
        const ctx = this.ctx;
        const w   = parseFloat(document.getElementById('houseW').value) || 12;
        const h   = parseFloat(document.getElementById('houseH').value) || 9;
        const rot = (parseFloat(document.getElementById('houseRot').value) || 0) * Math.PI / 180;

        if (!this.houseX || !this.houseY) {
            // Default: center of plot
            this.houseX = this.bounds.cx;
            this.houseY = this.bounds.cy;
        }

        const ctr = this.toCanvas(this.houseX, this.houseY);

        ctx.save();
        ctx.translate(ctr.cx, ctr.cy);
        ctx.rotate(-rot); // negative because Y is flipped

        const pw = w * this.viewScale;
        const ph = h * this.viewScale;

        // Shadow
        ctx.shadowColor = 'rgba(0,0,0,0.15)';
        ctx.shadowBlur  = 8;

        // Fill
        ctx.fillStyle   = 'rgba(96,165,250,0.6)';
        ctx.strokeStyle = '#1d4ed8';
        ctx.lineWidth   = 2;
        ctx.shadowColor = 'transparent';
        ctx.fillRect(-pw/2, -ph/2, pw, ph);
        ctx.strokeRect(-pw/2, -ph/2, pw, ph);

        // Roof lines
        ctx.strokeStyle = '#1e40af';
        ctx.lineWidth   = 1;
        ctx.beginPath();
        ctx.moveTo(-pw/2, -ph/2); ctx.lineTo(0, -ph/2 - ph*0.35); ctx.lineTo(pw/2, -ph/2); // front gable
        ctx.stroke();

        // Label
        ctx.fillStyle   = '#1e3a8a';
        ctx.font        = `bold ${Math.max(10, Math.min(14, pw/8))}px sans-serif`;
        ctx.textAlign   = 'center';
        ctx.fillText(`${w}×${h}m`, 0, ph*0.15);
        ctx.textAlign   = 'left';

        ctx.restore();
        this.houseCanvasCx = ctr.cx;
        this.houseCanvasCy = ctr.cy;
        this.houseCanvasW  = w * this.viewScale;
        this.houseCanvasH  = h * this.viewScale;
    }

    drawCompass(cx, cy, r) {
        const ctx = this.ctx;
        ctx.save();
        ctx.translate(cx, cy);

        // Background circle
        ctx.beginPath();
        ctx.arc(0, 0, r+2, 0, 2*Math.PI);
        ctx.fillStyle = 'rgba(255,255,255,0.9)';
        ctx.fill();
        ctx.strokeStyle = '#d1d5db';
        ctx.lineWidth   = 1;
        ctx.stroke();

        // N arrow (up = north, because we orient canvas north-up)
        ctx.fillStyle = '#dc2626';
        ctx.beginPath();
        ctx.moveTo(0, -r+2); ctx.lineTo(5, 0); ctx.lineTo(-5, 0); ctx.closePath(); ctx.fill();
        ctx.fillStyle = '#9ca3af';
        ctx.beginPath();
        ctx.moveTo(0, r-2); ctx.lineTo(5, 0); ctx.lineTo(-5, 0); ctx.closePath(); ctx.fill();

        ctx.fillStyle = '#dc2626';
        ctx.font = 'bold 11px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('N', 0, -r+14);
        ctx.fillStyle = '#6b7280';
        ctx.fillText('S', 0, r-3);
        ctx.textAlign = 'left';
        ctx.restore();
    }

    drawSunArrow(compassCx, compassCy) {
        // Arrow pointing south (optimal PV = south-facing) = pointing down on canvas
        const ctx = this.ctx;
        ctx.save();
        ctx.translate(compassCx, compassCy);
        ctx.strokeStyle = '#fbbf24';
        ctx.fillStyle   = '#fbbf24';
        ctx.lineWidth   = 2;
        // Arrow down (south)
        ctx.beginPath();
        ctx.moveTo(0, 0); ctx.lineTo(0, 28);
        ctx.moveTo(0, 28); ctx.lineTo(-5, 18); ctx.moveTo(0, 28); ctx.lineTo(5, 18);
        ctx.stroke();
        ctx.font = '9px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('☀️', 0, -12);
        ctx.textAlign = 'left';
        ctx.restore();
    }

    drawScaleBar() {
        const ctx = this.ctx;
        const W = this.canvas.width;
        const barMeters = this.niceGridStep();
        const barPx     = barMeters * this.viewScale;
        const x = 20, y = this.canvas.height - 20;

        ctx.fillStyle = 'rgba(255,255,255,0.8)';
        ctx.fillRect(x-4, y-16, barPx+8, 22);
        ctx.strokeStyle = '#374151';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(x, y); ctx.lineTo(x+barPx, y);
        ctx.moveTo(x, y-5); ctx.lineTo(x, y+2);
        ctx.moveTo(x+barPx, y-5); ctx.lineTo(x+barPx, y+2);
        ctx.stroke();
        ctx.fillStyle = '#374151';
        ctx.font = '10px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(`${barMeters >= 1000 ? (barMeters/1000)+'km' : barMeters+'m'}`, x+barPx/2, y-6);
        ctx.textAlign = 'left';
    }

    polygonArea(pts) {
        let a = 0;
        for (let i = 0; i < pts.length; i++) {
            const j = (i+1) % pts.length;
            a += pts[i].x * pts[j].y - pts[j].x * pts[i].y;
        }
        return Math.abs(a / 2);
    }

    updateHouseInfo() {
        const w = parseFloat(document.getElementById('houseW').value) || 12;
        const h = parseFloat(document.getElementById('houseH').value) || 9;
        const houseArea = w * h;
        const plotArea  = this.polygonArea(this.polygon);
        const ratio     = plotArea > 0 ? ((houseArea/plotArea)*100).toFixed(1) : 0;
        const el = document.getElementById('houseAreaInfo');
        el.textContent = `Rzut domu: ${houseArea} m² · Wskaźnik zabudowy: ${ratio}%`;
        el.className = 'text-sm mt-2 ' + (parseFloat(ratio) > 30 ? 'text-red-600' : 'text-indigo-600');
        el.classList.remove('hidden');
    }

    // ---- Mouse / touch interaction ----
    setupEvents() {
        const c = this.canvas;
        c.addEventListener('mousedown',  this.onMouseDown.bind(this));
        c.addEventListener('mousemove',  this.onMouseMove.bind(this));
        c.addEventListener('mouseup',    this.onMouseUp.bind(this));
        c.addEventListener('mouseleave', this.onMouseUp.bind(this));
        c.addEventListener('wheel',      this.onWheel.bind(this), {passive:false});
        // Right-click pan
        c.addEventListener('contextmenu', e => e.preventDefault());

        // Touch
        c.addEventListener('touchstart',  this.onTouchStart.bind(this), {passive:false});
        c.addEventListener('touchmove',   this.onTouchMove.bind(this),  {passive:false});
        c.addEventListener('touchend',    this.onTouchEnd.bind(this));
    }

    isOverHouse(cx, cy) {
        if (!this.houseX || !this.houseY) return false;
        const ctr = this.toCanvas(this.houseX, this.houseY);
        const hw  = (this.houseCanvasW||0) / 2 + 8;
        const hh  = (this.houseCanvasH||0) / 2 + 8;
        return Math.abs(cx - ctr.cx) < hw && Math.abs(cy - ctr.cy) < hh;
    }

    onMouseDown(e) {
        if (e.button === 2) { // right-click pan
            this.panning = true;
            this.panStartX = e.offsetX;
            this.panStartY = e.offsetY;
            this.canvas.style.cursor = 'grabbing';
            return;
        }
        if (this.isOverHouse(e.offsetX, e.offsetY)) {
            this.dragging = true;
            const mp = this.fromCanvas(e.offsetX, e.offsetY);
            this.dragOffX = mp.x - this.houseX;
            this.dragOffY = mp.y - this.houseY;
            this.canvas.style.cursor = 'move';
        }
    }

    onMouseMove(e) {
        if (this.panning) {
            this.viewOffsetX += e.offsetX - this.panStartX;
            this.viewOffsetY += e.offsetY - this.panStartY;
            this.panStartX = e.offsetX;
            this.panStartY = e.offsetY;
            this.draw();
            return;
        }
        if (this.dragging) {
            const mp = this.fromCanvas(e.offsetX, e.offsetY);
            this.houseX = mp.x - this.dragOffX;
            this.houseY = mp.y - this.dragOffY;
            this.draw();
        } else {
            this.canvas.style.cursor = this.isOverHouse(e.offsetX, e.offsetY) ? 'move' : 'crosshair';
        }
    }

    onMouseUp() {
        this.dragging = false;
        this.panning  = false;
        this.canvas.style.cursor = 'crosshair';
    }

    onWheel(e) {
        e.preventDefault();
        const factor = e.deltaY < 0 ? 1.12 : 0.88;
        const ox = e.offsetX, oy = e.offsetY;
        this.viewOffsetX = ox - factor*(ox - this.viewOffsetX);
        this.viewOffsetY = oy - factor*(oy - this.viewOffsetY);
        this.viewScale  *= factor;
        this.draw();
    }

    onTouchStart(e) {
        if (e.touches.length === 1) {
            const t = e.touches[0];
            const rect = this.canvas.getBoundingClientRect();
            const cx = t.clientX - rect.left, cy = t.clientY - rect.top;
            if (this.isOverHouse(cx, cy)) {
                this.dragging = true;
                const mp = this.fromCanvas(cx, cy);
                this.dragOffX = mp.x - this.houseX;
                this.dragOffY = mp.y - this.houseY;
            } else {
                this.panning  = true;
                this.panStartX = cx; this.panStartY = cy;
            }
        }
        e.preventDefault();
    }

    onTouchMove(e) {
        if (e.touches.length === 1) {
            const t = e.touches[0];
            const rect = this.canvas.getBoundingClientRect();
            const cx = t.clientX - rect.left, cy = t.clientY - rect.top;
            if (this.dragging) {
                const mp = this.fromCanvas(cx, cy);
                this.houseX = mp.x - this.dragOffX;
                this.houseY = mp.y - this.dragOffY;
                this.draw();
            } else if (this.panning) {
                this.viewOffsetX += cx - this.panStartX;
                this.viewOffsetY += cy - this.panStartY;
                this.panStartX = cx; this.panStartY = cy;
                this.draw();
            }
        }
        e.preventDefault();
    }

    onTouchEnd() { this.dragging = false; this.panning = false; }
}

// ================================================================
// Global helpers
// ================================================================
let editor = null;

window.addEventListener('DOMContentLoaded', () => {
    if (PLOT_WKT) {
        editor = new PlotEditor('plotCanvas', PLOT_WKT);
        if (PLOT_DATA.house_x) {
            editor.houseX = PLOT_DATA.house_x;
            editor.houseY = PLOT_DATA.house_y;
            editor.draw();
        }
    } else {
        document.getElementById('canvasMsgText').textContent = 'Brak danych geometrii. Wróć i wyszukaj działkę ponownie.';
    }
});

function updateHouseDims() { editor && editor.draw(); }

function centerHouse() {
    if (!editor || !editor.bounds) return;
    editor.houseX = editor.bounds.cx;
    editor.houseY = editor.bounds.cy;
    editor.draw();
}

function syncSetbacks() {
    ['front','back','left','right'].forEach(k => {
        document.getElementById(`sb_${k}_hidden`).value = document.getElementById(`sb_${k}`).value;
    });
}

async function saveHousePosition() {
    if (!editor) return;
    const btn = document.querySelector('[onclick="saveHousePosition()"]');
    btn.textContent = 'Zapisuję...';
    btn.disabled = true;

    try {
        const resp = await fetch(SAVE_URL, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                house_x:        editor.houseX,
                house_y:        editor.houseY,
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