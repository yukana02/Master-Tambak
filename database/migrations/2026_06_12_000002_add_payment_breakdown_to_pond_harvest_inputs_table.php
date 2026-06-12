<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pond_harvest_inputs', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('price_per_kg');
            $table->decimal('cash_amount', 10, 2)->default(0)->after('payment_method');
            $table->decimal('tf_amount', 10, 2)->default(0)->after('cash_amount');
        });
    }

    public function down(): void
    {
        Schema::table('pond_harvest_inputs', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'cash_amount', 'tf_amount']);
        });
    }
};
