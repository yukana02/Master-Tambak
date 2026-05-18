<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class FeedCategory extends Model
{
    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }
}
