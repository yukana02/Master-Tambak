<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->foreignId('feed_id')->nullable()->after('fish_count')->constrained()->nullOnDelete();
            $table->decimal('target_harvest_weight_kg', 10, 2)->nullable()->after('feed_id');
            $table->decimal('planned_feed_sacks', 10, 2)->nullable()->after('target_harvest_weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('feed_id');
            $table->dropColumn(['target_harvest_weight_kg', 'planned_feed_sacks']);
        });
    }
};
