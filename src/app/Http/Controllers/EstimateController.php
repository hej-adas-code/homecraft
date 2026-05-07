<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function index()
    {
        $estimates = Estimate::where('user_id', auth()->id())
            ->withCount('items')
            ->latest()
            ->get();
        return view('estimates.index', compact('estimates'));
    }

    public function create()
    {
        return view('estimates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|max:50',
            'total'       => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
        ]);
        $validated['user_id'] = auth()->id();
        $estimate = Estimate::create($validated);
        return redirect()->route('estimates.show', $estimate)->with('success', 'Kosztorys został dodany.');
    }

    public function show(Estimate $estimate)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        $estimate->load('items');
        return view('estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        return view('estimates.create', compact('estimate'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|max:50',
            'total'       => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
        ]);
        $estimate->update($validated);
        return redirect()->route('estimates.show', $estimate)->with('success', 'Kosztorys został zaktualizowany.');
    }

    public function destroy(Estimate $estimate)
    {
        abort_if($estimate->user_id !== auth()->id(), 403);
        $estimate->delete();
        return redirect()->route('estimates.index')->with('success', 'Kosztorys został usunięty.');
    }
}
