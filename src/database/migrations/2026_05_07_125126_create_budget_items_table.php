<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('budget_categories')->nullOnDelete();
            $table->string('name');
            $table->decimal('planned_amount', 12, 2)->default(0);
            $table->decimal('actual_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->default('expense'); // expense | income
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('budget_items'); }
};