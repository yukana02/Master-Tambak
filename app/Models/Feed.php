<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['feed_category_id', 'name', 'composition', 'sack_weight_kg', 'fcr', 'is_active'])]
class Feed extends Model
{
    protected function casts(): array
    {
        return [
            'sack_weight_kg' => 'decimal:2',
            'fcr' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeedCategory::class, 'feed_category_id');
    }

    public function ponds(): HasMany
    {
        return $this->hasMany(Pond::class);
    }

    public function pondFeedings(): HasMany
    {
        return $this->hasMany(PondFeeding::class);
    }
}
