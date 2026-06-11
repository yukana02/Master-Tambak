<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pond_harvest_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pond_harvest_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('harvested_at');
            $table->string('bucket_name');
            $table->decimal('kg', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pond_harvest_inputs');
    }
};
