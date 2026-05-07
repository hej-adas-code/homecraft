<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index()
    {
        $milestones = Milestone::where('user_id', auth()->id())
            ->orderBy('start_date')
            ->get();
        return view('milestones.index', compact('milestones'));
    }

    public function create()
    {
        return view('milestones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
        ]);
        $validated['user_id'] = auth()->id();
        Milestone::create($validated);
        return redirect()->route('milestones.index')->with('success', 'Kamień milowy został dodany.');
    }

    public function edit(Milestone $milestone)
    {
        abort_if($milestone->user_id !== auth()->id(), 403);
        return view('milestones.create', compact('milestone'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        abort_if($milestone->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'nullable|string|max:50',
            'color'       => 'nullable|string|max:20',
        ]);
        $milestone->update($validated);
        return redirect()->route('milestones.index')->with('success', 'Kamień milowy został zaktualizowany.');
    }

    public function destroy(Milestone $milestone)
    {
        abort_if($milestone->user_id !== auth()->id(), 403);
        $milestone->delete();
        return redirect()->route('milestones.index')->with('success', 'Kamień milowy został usunięty.');
    }
}
