<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\EstimateItem;
use Illuminate\Http\Request;

class EstimateItemController extends Controller
{
    public function store(Request $request, Estimate $estimate)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        $d = $request->validate([
            'name'       => 'required|string|max:255',
            'quantity'   => 'required|numeric|min:0',
            'unit'       => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
        ]);
        $d['unit'] = $d['unit'] ?: 'szt.';
        $d['total'] = $d['quantity'] * $d['unit_price'];
        $estimate->items()->create($d);
        $estimate->update(['total' => $estimate->items()->sum('total')]);
        return redirect()->route('estimates.show', $estimate)->with('success', 'Pozycja dodana.');
    }

    public function destroy(Estimate $estimate, EstimateItem $item)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        abort_if($item->estimate_id !== $estimate->id, 404);
        $item->delete();
        $estimate->update(['total' => $estimate->items()->sum('total')]);
        return redirect()->back()->with('success', 'Pozycja usunięta.');
    }
}
