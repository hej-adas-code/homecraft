<?php

namespace App\Providers;

use App\Models\BudgetItem;
use App\Models\Document;
use App\Models\Idea;
use App\Models\Milestone;
use App\Models\Offer;
use App\Observers\TimelineObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        BudgetItem::observe(new TimelineObserver('budget_item'));
        Offer::observe(new TimelineObserver('offer'));
        Document::observe(new TimelineObserver('document'));
        Milestone::observe(new TimelineObserver('milestone'));
        Idea::observe(new TimelineObserver('idea'));
    }
}
