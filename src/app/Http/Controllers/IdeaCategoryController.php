<?php

namespace App\Http\Controllers;

use App\Models\IdeaCategory;
use Illuminate\Http\Request;

class IdeaCategoryController extends Controller
{
    public function index()
    {
        $categories = IdeaCategory::where('user_id', auth()->id())
            ->withCount('ideas')
            ->latest()
            ->get();
        return view('ideas.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('ideas.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon'  => 'nullable|string|max:50',
        ]);
        $validated['user_id'] = auth()->id();
        IdeaCategory::create($validated);
        return redirect()->route('idea-categories.index')->with('success', 'Kategoria pomysłów została dodana.');
    }

    public function edit(IdeaCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        return view('ideas.categories.create', compact('category'));
    }

    public function update(Request $request, IdeaCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon'  => 'nullable|string|max:50',
        ]);
        $category->update($validated);
        return redirect()->route('idea-categories.index')->with('success', 'Kategoria pomysłów została zaktualizowana.');
    }

    public function destroy(IdeaCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        $category->delete();
        return redirect()->route('idea-categories.index')->with('success', 'Kategoria pomysłów została usunięta.');
    }
}
