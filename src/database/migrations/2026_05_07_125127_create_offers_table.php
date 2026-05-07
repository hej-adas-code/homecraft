<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('contractor_name');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending | accepted | rejected
            $table->text('description')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('offers'); }
};