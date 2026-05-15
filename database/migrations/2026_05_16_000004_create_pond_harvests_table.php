<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pond_harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_id')->constrained()->cascadeOnDelete();
            $table->date('harvested_at');
            $table->string('fish_type');
            $table->unsignedInteger('fish_count')->default(0);
            $table->decimal('target_harvest_weight_kg', 10, 2)->nullable();
            $table->decimal('total_feed_weight_kg', 10, 2)->default(0);
            $table->decimal('total_estimated_meat_kg', 10, 2)->default(0);
            $table->date('feeding_started_at')->nullable();
            $table->date('feeding_ended_at')->nullable();
            $table->unsignedInteger('feeding_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pond_harvests');
    }
};
