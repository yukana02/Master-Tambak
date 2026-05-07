<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ponds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->string('fish_type');
            $table->unsignedInteger('fish_count')->default(0);
            $table->date('stocking_date')->nullable();
            $table->date('harvest_date')->nullable();
            $table->unsignedSmallInteger('x')->default(0);
            $table->unsignedSmallInteger('y')->default(0);
            $table->unsignedSmallInteger('width')->default(3);
            $table->unsignedSmallInteger('height')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponds');
    }
};
