<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'notes', 'pond_size_notes', 'fish_type', 'fish_count', 'seed_source', 'dead_fish_count', 'feed_id', 'target_harvest_weight_kg', 'planned_feed_sacks', 'stocking_date', 'harvest_date', 'x', 'y', 'width', 'height'])]
class Pond extends Model
{
    protected function casts(): array
    {
        return [
            'stocking_date' => 'date',
            'harvest_date' => 'date',
            'fish_count' => 'integer',
            'dead_fish_count' => 'integer',
            'target_harvest_weight_kg' => 'decimal:2',
            'planned_feed_sacks' => 'decimal:2',
            'x' => 'integer',
            'y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function getEstimatedLiveFishCountAttribute(): int
    {
        return max(0, $this->fish_count - $this->dead_fish_count);
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class);
    }

    public function feedings(): HasMany
    {
        return $this->hasMany(PondFeeding::class);
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(PondHarvest::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->harvest_plan_status === 'ready') {
            return 'ready';
        }

        $predictionDate = $this->predicted_harvest_date;

        if (! $predictionDate) {
            return 'active';
        }

        if ($predictionDate->isPast()) {
            return 'overdue';
        }

        if (now()->diffInDays($predictionDate, false) <= 14) {
            return 'soon';
        }

        return 'active';
    }

    public function getPlannedFeedWeightKgAttribute(): ?float
    {
        if (! $this->feed || ! $this->planned_feed_sacks) {
            return null;
        }

        return (float) $this->planned_feed_sacks * (float) $this->feed->sack_weight_kg;
    }

    public function getEstimatedHarvestWeightKgAttribute(): ?float
    {
        if ($this->actual_estimated_meat_kg > 0) {
            return $this->actual_estimated_meat_kg;
        }

        if (! $this->planned_feed_weight_kg || ! $this->feed || (float) $this->feed->fcr <= 0) {
            return null;
        }

        return $this->planned_feed_weight_kg / (float) $this->feed->fcr;
    }

    public function getPlannedEstimatedHarvestWeightKgAttribute(): ?float
    {
        if (! $this->planned_feed_weight_kg || ! $this->feed || (float) $this->feed->fcr <= 0) {
            return null;
        }

        return $this->planned_feed_weight_kg / (float) $this->feed->fcr;
    }

    public function getActualFeedWeightKgAttribute(): float
    {
        if ($this->relationLoaded('feedings')) {
            return (float) $this->feedings->sum('feed_weight_kg');
        }

        return (float) $this->feedings()->sum('feed_weight_kg');
    }

    public function getActualEstimatedMeatKgAttribute(): float
    {
        if ($this->relationLoaded('feedings')) {
            return (float) $this->feedings->sum('estimated_meat_kg');
        }

        return (float) $this->feedings()->sum('estimated_meat_kg');
    }

    public function getDailyEstimatedMeatKgAttribute(): ?float
    {
        if ($this->actual_estimated_meat_kg <= 0) {
            return null;
        }

        $feedings = $this->relationLoaded('feedings')
            ? $this->feedings
            : $this->feedings()->get();

        if ($feedings->isEmpty()) {
            return null;
        }

        $firstFedAt = $feedings->min('fed_at');
        $lastFedAt = $feedings->max('fed_at');
        $days = max(1, $firstFedAt->diffInDays($lastFedAt) + 1);

        return $this->actual_estimated_meat_kg / $days;
    }

    public function getPredictedHarvestDateAttribute(): ?CarbonInterface
    {
        if (! $this->target_harvest_weight_kg || (float) $this->target_harvest_weight_kg <= 0 || $this->actual_estimated_meat_kg <= 0) {
            return null;
        }

        if ($this->actual_estimated_meat_kg >= (float) $this->target_harvest_weight_kg) {
            $feedings = $this->relationLoaded('feedings')
                ? $this->feedings
                : $this->feedings()->get();

            return $feedings->max('fed_at');
        }

        if (! $this->daily_estimated_meat_kg || $this->daily_estimated_meat_kg <= 0) {
            return null;
        }

        $feedings = $this->relationLoaded('feedings')
            ? $this->feedings
            : $this->feedings()->get();

        if ($feedings->isEmpty()) {
            return null;
        }

        $remainingKg = (float) $this->target_harvest_weight_kg - $this->actual_estimated_meat_kg;
        $remainingDays = (int) ceil($remainingKg / $this->daily_estimated_meat_kg);

        return $feedings->max('fed_at')->copy()->addDays($remainingDays);
    }

    public function getRequiredFeedWeightKgAttribute(): ?float
    {
        if (! $this->feed || ! $this->target_harvest_weight_kg) {
            return null;
        }

        return (float) $this->target_harvest_weight_kg * (float) $this->feed->fcr;
    }

    public function getRequiredFeedSacksAttribute(): ?float
    {
        if (! $this->required_feed_weight_kg || ! $this->feed || (float) $this->feed->sack_weight_kg <= 0) {
            return null;
        }

        return $this->required_feed_weight_kg / (float) $this->feed->sack_weight_kg;
    }

    public function getHarvestProgressPercentAttribute(): ?float
    {
        if ($this->actual_estimated_meat_kg <= 0 || ! $this->target_harvest_weight_kg || (float) $this->target_harvest_weight_kg <= 0) {
            return null;
        }

        return min(100, ($this->actual_estimated_meat_kg / (float) $this->target_harvest_weight_kg) * 100);
    }

    public function getHarvestPlanStatusAttribute(): string
    {
        if ($this->actual_estimated_meat_kg > 0 && $this->target_harvest_weight_kg && $this->actual_estimated_meat_kg >= (float) $this->target_harvest_weight_kg) {
            return 'ready';
        }

        return 'not_ready';
    }
}
