<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;
use Illuminate\Http\Request;

class BudgetCategoryController extends Controller
{
    public function index()
    {
        $categories = BudgetCategory::where('user_id', auth()->id())
            ->withCount('items')
            ->latest()
            ->get();
        return view('budget.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('budget.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'color'        => 'nullable|string|max:20',
            'budget_limit' => 'nullable|numeric|min:0',
        ]);
        $validated['user_id'] = auth()->id();
        BudgetCategory::create($validated);
        return redirect()->route('budget-categories.index')->with('success', 'Kategoria została dodana.');
    }

    public function edit(BudgetCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        return view('budget.categories.create', compact('category'));
    }

    public function update(Request $request, BudgetCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'color'        => 'nullable|string|max:20',
            'budget_limit' => 'nullable|numeric|min:0',
        ]);
        $category->update($validated);
        return redirect()->route('budget-categories.index')->with('success', 'Kategoria została zaktualizowana.');
    }

    public function destroy(BudgetCategory $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        $category->delete();
        return redirect()->route('budget-categories.index')->with('success', 'Kategoria została usunięta.');
    }
}
