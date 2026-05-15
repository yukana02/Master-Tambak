<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pond_id', 'harvested_at', 'fish_type', 'fish_count', 'target_harvest_weight_kg', 'total_feed_weight_kg', 'total_estimated_meat_kg', 'feeding_started_at', 'feeding_ended_at', 'feeding_count', 'notes'])]
class PondHarvest extends Model
{
    protected function casts(): array
    {
        return [
            'harvested_at' => 'date',
            'target_harvest_weight_kg' => 'decimal:2',
            'total_feed_weight_kg' => 'decimal:2',
            'total_estimated_meat_kg' => 'decimal:2',
            'feeding_started_at' => 'date',
            'feeding_ended_at' => 'date',
            'feeding_count' => 'integer',
        ];
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }
}
