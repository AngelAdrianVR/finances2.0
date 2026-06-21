<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outcome extends Model
{
    protected $fillable = [
        'amount',
        'concept',
        'category',
        'payment_method',
        'description',
        'created_at',
        'automatically_created',
        'split_enabled',
        'split_with',
        'user_id',
    ];

    protected $casts = [
        'amount'                => 'float',
        'automatically_created' => 'boolean',
        'split_enabled'         => 'boolean',
        'split_with'            => 'array',
        'created_at'            => 'datetime',
    ];

    // ========================
    // Relationships
    // ========================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========================
    // Scopes
    // ========================

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeBetweenDates(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeSplit(Builder $query): Builder
    {
        return $query->where('split_enabled', true);
    }
}

