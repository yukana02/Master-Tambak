<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pond_harvest_feedings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_harvest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feed_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feed_name'); // snapshot nama pakan saat panen
            $table->string('feed_category_name')->nullable(); // snapshot kategori
            $table->date('fed_at');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 10);
            $table->decimal('feed_weight_kg', 10, 2);
            $table->decimal('estimated_meat_kg', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pond_harvest_feedings');
    }
};
