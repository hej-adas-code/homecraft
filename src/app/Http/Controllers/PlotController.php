<?php
namespace App\Http\Controllers;

use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlotController extends Controller
{
    public function index()
    {
        $plots = Plot::where('user_id', auth()->id())->latest()->get();
        return view('plots.index', compact('plots'));
    }

    public function create()
    {
        return view('plots.create');
    }

    public function store(Request $request)
    {
        $request->validate(['plot_number' => 'required|string|max:100']);

        $data = $this->fetchFromUldk(trim($request->plot_number));

        if (!$data['success']) {
            return back()->withInput()->withErrors(['plot_number' => 'Nie znaleziono działki: ' . $data['error']]);
        }

        $plot = Plot::create([
            'user_id'       => auth()->id(),
            'plot_number'   => $request->plot_number,
            'address'       => $request->address ?? null,
            'area'          => $data['area'],
            'geometry_wkt'  => $data['geometry'],
            'setback_front' => 4,
            'setback_back'  => 4,
            'setback_left'  => 3,
            'setback_right' => 3,
        ]);

        return redirect()->route('plots.show', $plot)->with('success', 'Działka dodana pomyślnie!');
    }

    public function show(Plot $plot)
    {
        abort_if($plot->user_id !== auth()->id(), 403);
        return view('plots.show', compact('plot'));
    }

    public function update(Request $request, Plot $plot)
    {
        abort_if($plot->user_id !== auth()->id(), 403);

        $plot->update($request->validate([
            'address'       => 'nullable|string|max:255',
            'setback_front' => 'nullable|numeric|min:0|max:50',
            'setback_back'  => 'nullable|numeric|min:0|max:50',
            'setback_left'  => 'nullable|numeric|min:0|max:50',
            'setback_right' => 'nullable|numeric|min:0|max:50',
        ]));

        return back()->with('success', 'Zapisano.');
    }

    public function updateHouse(Request $request, Plot $plot)
    {
        abort_if($plot->user_id !== auth()->id(), 403);

        $plot->update($request->validate([
            'house_x'        => 'nullable|numeric',
            'house_y'        => 'nullable|numeric',
            'house_width'    => 'nullable|numeric|min:1|max:100',
            'house_height'   => 'nullable|numeric|min:1|max:100',
            'house_rotation' => 'nullable|numeric',
        ]));

        return response()->json(['ok' => true]);
    }

    public function destroy(Plot $plot)
    {
        abort_if($plot->user_id !== auth()->id(), 403);
        $plot->delete();
        return redirect()->route('plots.index')->with('success', 'Działka usunięta.');
    }

    public function search(Request $request)
    {
        $request->validate(['plot_number' => 'required|string']);
        return response()->json($this->fetchFromUldk(trim($request->plot_number)));
    }

    private function fetchFromUldk(string $plotNumber): array
    {
        try {
            $response = Http::timeout(15)->get('https://uldk.gugik.gov.pl/', [
                'request' => 'GetParcelById',
                'id'      => $plotNumber,
                'result'  => 'geom_wkt,area',
            ]);

            $body  = trim($response->body());

            // Dziel tylko po \n — NIE po średniku, bo SRID=2180;POLYGON(...) to jedna linia
            $lines = array_values(array_filter(array_map('trim', explode("\n", $body))));

            if (empty($lines) || $lines[0] !== '0') {
                $msg = $lines[1] ?? 'Nieznany błąd API ULDK';
                return ['success' => false, 'error' => $msg];
            }

            $geometry = $lines[1] ?? null;
            $area     = isset($lines[2]) ? (float) $lines[2] : null;

            if (!$geometry) {
                return ['success' => false, 'error' => 'Brak geometrii w odpowiedzi ULDK'];
            }

            // Usuń prefix SRID jeśli istnieje: "SRID=2180;POLYGON(...)" → "POLYGON(...)"
            if (preg_match('/^SRID=\d+;(.+)$/i', $geometry, $m)) {
                $geometry = $m[1];
            }

            $geomUpper = strtoupper($geometry);
            if (!str_starts_with($geomUpper, 'POLYGON') && !str_starts_with($geomUpper, 'MULTIPOLYGON')) {
                return ['success' => false, 'error' => 'Nieoczekiwany format geometrii: ' . substr($geometry, 0, 60)];
            }

            return ['success' => true, 'geometry' => $geometry, 'area' => $area];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Błąd połączenia: ' . $e->getMessage()];
        }
    }
}