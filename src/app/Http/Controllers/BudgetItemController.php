<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\BudgetCategory;
use Illuminate\Http\Request;

class BudgetItemController extends Controller
{
    public function index()
    {
        $query = BudgetItem::where('user_id', auth()->id())->with('category')->latest();
        if (request('type')) {
            $query->where('type', request('type'));
        }
        $items = $query->get();

        $totalIncome  = BudgetItem::where('user_id', auth()->id())->where('type', 'income')->sum('actual_amount');
        $totalExpense = BudgetItem::where('user_id', auth()->id())->where('type', 'expense')->sum('actual_amount');
        $totalPlanned = BudgetItem::where('user_id', auth()->id())->where('type', 'expense')->sum('planned_amount');
        $totalActual  = $totalExpense;

        return view('budget.index', compact('items', 'totalPlanned', 'totalActual', 'totalIncome', 'totalExpense'));
    }

    public function create()
    {
        $categories = BudgetCategory::where('user_id', auth()->id())->orderBy('name')->get();
        return view('budget.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'nullable|exists:budget_categories,id',
            'planned_amount' => 'required|numeric|min:0',
            'actual_amount'  => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'date'           => 'nullable|date',
            'type'           => 'nullable|string|max:50',
        ]);
        $validated['user_id'] = auth()->id();
        BudgetItem::create($validated);
        return redirect()->route('budget.index')->with('success', 'Pozycja budżetu została dodana.');
    }

    public function edit(BudgetItem $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $categories = BudgetCategory::where('user_id', auth()->id())->orderBy('name')->get();
        return view('budget.create', ['item' => $budget, 'categories' => $categories]);
    }

    public function update(Request $request, BudgetItem $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'nullable|exists:budget_categories,id',
            'planned_amount' => 'required|numeric|min:0',
            'actual_amount'  => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'date'           => 'nullable|date',
            'type'           => 'nullable|string|max:50',
        ]);
        $budget->update($validated);
        return redirect()->route('budget.index')->with('success', 'Pozycja budżetu została zaktualizowana.');
    }

    public function destroy(BudgetItem $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $budget->delete();
        return redirect()->route('budget.index')->with('success', 'Pozycja budżetu została usunięta.');
    }
}
