<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'notes', 'fish_type', 'fish_count', 'stocking_date', 'harvest_date', 'x', 'y', 'width', 'height'])]
class Pond extends Model
{
    protected function casts(): array
    {
        return [
            'stocking_date' => 'date',
            'harvest_date' => 'date',
            'fish_count' => 'integer',
            'x' => 'integer',
            'y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function getStatusAttribute(): string
    {
        if (! $this->harvest_date) {
            return 'active';
        }

        if ($this->harvest_date->isPast()) {
            return 'overdue';
        }

        if (now()->diffInDays($this->harvest_date, false) <= 14) {
            return 'soon';
        }

        return 'active';
    }
}
