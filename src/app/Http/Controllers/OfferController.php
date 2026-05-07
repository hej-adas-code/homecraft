<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Estimate;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::where('user_id', auth()->id())
            ->with('estimate')
            ->latest()
            ->get();
        return view('offers.index', compact('offers'));
    }

    public function create()
    {
        $estimates = Estimate::where('user_id', auth()->id())->orderBy('title')->get();
        return view('offers.create', compact('estimates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'estimate_id'     => 'nullable|exists:estimates,id',
            'contractor_name' => 'nullable|string|max:255',
            'amount'          => 'nullable|numeric|min:0',
            'status'          => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'valid_until'     => 'nullable|date',
        ]);
        $validated['user_id'] = auth()->id();
        Offer::create($validated);
        return redirect()->route('offers.index')->with('success', 'Oferta została dodana.');
    }

    public function edit(Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403);
        $estimates = Estimate::where('user_id', auth()->id())->orderBy('title')->get();
        return view('offers.create', compact('offer', 'estimates'));
    }

    public function update(Request $request, Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'estimate_id'     => 'nullable|exists:estimates,id',
            'contractor_name' => 'nullable|string|max:255',
            'amount'          => 'nullable|numeric|min:0',
            'status'          => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'valid_until'     => 'nullable|date',
        ]);
        $offer->update($validated);
        return redirect()->route('offers.index')->with('success', 'Oferta została zaktualizowana.');
    }

    public function destroy(Offer $offer)
    {
        abort_if($offer->user_id !== auth()->id(), 403);
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Oferta została usunięta.');
    }
}
