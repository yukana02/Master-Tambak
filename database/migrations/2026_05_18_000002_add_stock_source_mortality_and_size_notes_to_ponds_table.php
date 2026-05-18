<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->string('seed_source')->nullable()->after('fish_count');
            $table->unsignedInteger('dead_fish_count')->default(0)->after('seed_source');
            $table->text('pond_size_notes')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->dropColumn(['seed_source', 'dead_fish_count', 'pond_size_notes']);
        });
    }
};
