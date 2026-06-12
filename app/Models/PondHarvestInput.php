<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pond_id',
    'pond_harvest_id',
    'harvested_at',
    'bucket_name',
    'kg',
    'price_per_kg',
    'total_price',
    'payment_method',
    'cash_amount',
    'tf_amount',
    'notes',
    'status',
])]
class PondHarvestInput extends Model
{
    protected function casts(): array
    {
        return [
            'harvested_at' => 'date',
            'kg' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'cash_amount' => 'decimal:2',
            'tf_amount' => 'decimal:2',
        ];
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }

    public function pondHarvest(): BelongsTo
    {
        return $this->belongsTo(PondHarvest::class);
    }
}
