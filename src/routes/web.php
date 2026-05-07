<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetCategoryController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\EstimateItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PlotController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaCategoryController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::get("/", fn() => view("landing"))->name("home");

Route::middleware(["auth", "verified"])->group(function () {
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");

    Route::resource("budget/categories", BudgetCategoryController::class)->names("budget-categories");
    Route::resource("budget", BudgetItemController::class)->names("budget");

    Route::resource("estimates", EstimateController::class);
    Route::post('estimates/{estimate}/items', [EstimateItemController::class, 'store'])->name('estimates.items.store');
    Route::delete('estimates/{estimate}/items/{item}', [EstimateItemController::class, 'destroy'])->name('estimates.items.destroy');

    Route::resource("offers", OfferController::class);
    Route::resource("documents", DocumentController::class);
    Route::resource("plots", PlotController::class);
    Route::get("/api/plots/search", [PlotController::class, "search"])->name("plots.search");
    Route::patch("/plots/{plot}/house", [PlotController::class, "updateHouse"])->name("plots.update-house");
    Route::resource("ideas/categories", IdeaCategoryController::class)->names("idea-categories");
    Route::resource("ideas", IdeaController::class);
    Route::resource("milestones", MilestoneController::class);
    Route::resource("contacts", ContactController::class);
    Route::resource("meetings", MeetingController::class);

    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
    Route::post('/timeline', [TimelineController::class, 'store'])->name('timeline.store');
    Route::delete('/timeline/{timelineEntry}', [TimelineController::class, 'destroy'])->name('timeline.destroy');

    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");
});

require __DIR__."/auth.php";
