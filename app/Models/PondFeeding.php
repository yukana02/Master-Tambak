<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pond_id', 'feed_id', 'fed_at', 'quantity', 'unit', 'feed_weight_kg', 'estimated_meat_kg', 'notes'])]
class PondFeeding extends Model
{
    protected function casts(): array
    {
        return [
            'fed_at' => 'date',
            'quantity' => 'decimal:2',
            'feed_weight_kg' => 'decimal:2',
            'estimated_meat_kg' => 'decimal:2',
        ];
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class);
    }

    public function feedCategoryName(): string
    {
        return $this->feed?->category?->name ?? 'Tanpa Kategori';
    }
}
