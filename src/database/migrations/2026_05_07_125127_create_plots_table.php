<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plot_number');
            $table->string('address')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->text('geometry_wkt')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('house_x', 10, 2)->nullable();
            $table->decimal('house_y', 10, 2)->nullable();
            $table->decimal('house_width', 10, 2)->nullable();
            $table->decimal('house_height', 10, 2)->nullable();
            $table->decimal('house_rotation', 6, 2)->default(0);
            $table->decimal('setback_front', 6, 2)->default(4);
            $table->decimal('setback_back', 6, 2)->default(4);
            $table->decimal('setback_left', 6, 2)->default(3);
            $table->decimal('setback_right', 6, 2)->default(3);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('plots'); }
};