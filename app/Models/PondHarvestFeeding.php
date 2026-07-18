<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pond_harvest_id', 'feed_id', 'feed_name', 'feed_category_name',
    'fed_at', 'quantity', 'unit', 'feed_weight_kg', 'estimated_meat_kg', 'notes',
])]
class PondHarvestFeeding extends Model
{
    protected function casts(): array
    {
        return [
            'fed_at'              => 'date',
            'quantity'            => 'decimal:2',
            'feed_weight_kg'      => 'decimal:2',
            'estimated_meat_kg'   => 'decimal:2',
        ];
    }

    public function harvest(): BelongsTo
    {
        return $this->belongsTo(PondHarvest::class, 'pond_harvest_id');
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class);
    }
}
