<?php
namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\BudgetCategory;
use App\Models\Estimate;
use App\Models\Offer;
use App\Models\Document;
use App\Models\Milestone;
use App\Models\TimelineEntry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalIncome    = BudgetItem::where('user_id', $user->id)->where('type', 'income')->sum('actual_amount');
        $totalExpense   = BudgetItem::where('user_id', $user->id)->where('type', 'expense')->sum('actual_amount');
        $totalPlanned   = BudgetItem::where('user_id', $user->id)->where('type', 'expense')->sum('planned_amount');
        $documentsCount = Document::where('user_id', $user->id)->count();
        $offersCount    = Offer::where('user_id', $user->id)->count();
        $estimatesCount = Estimate::where('user_id', $user->id)->count();

        $recentDocs = Document::where('user_id', $user->id)->latest()->take(5)->get();
        $upcomingMilestones = Milestone::where('user_id', $user->id)
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('start_date')
            ->take(5)
            ->get();
        $recentOffers = Offer::where('user_id', $user->id)->latest()->take(3)->get();

        $categories = BudgetCategory::where('user_id', $user->id)->withSum(['items as spent' => fn($q) => $q->where('type','expense')], 'actual_amount')->get();

        $recentTimeline = TimelineEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'totalIncome', 'totalExpense', 'totalPlanned',
            'documentsCount', 'offersCount', 'estimatesCount',
            'recentDocs', 'upcomingMilestones', 'recentOffers', 'categories',
            'recentTimeline'
        ));
    }
}
