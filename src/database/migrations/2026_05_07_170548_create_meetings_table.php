<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("meetings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->string("title");
            $table->string("contractor_name")->nullable();
            $table->foreignId("contact_id")->nullable()->constrained()->nullOnDelete();
            $table->datetime("meeting_at");
            $table->string("location")->nullable();
            $table->text("agenda")->nullable();
            $table->text("notes")->nullable();
            $table->string("status")->default("planned"); // planned | done | cancelled
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("meetings"); }
};