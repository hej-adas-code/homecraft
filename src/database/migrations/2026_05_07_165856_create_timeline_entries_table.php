<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // milestone, budget_item, offer, document, idea, note, photo
            $table->string('entry_title');
            $table->text('entry_body')->nullable();
            $table->string('image_path')->nullable();
            $table->datetime('entry_date');
            $table->string('entryable_type')->nullable();
            $table->unsignedBigInteger('entryable_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_entries');
    }
};
