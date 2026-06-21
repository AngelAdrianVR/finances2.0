<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringIncome extends Model
{
    protected $fillable = [
        'concept',
        'is_active',
        'periodicity',
        'amount',
        'payment_method',
        'description',
        'category',
        'created_at',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'amount'    => 'float',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ========================
    // Helpers
    // ========================

    public function toggle(): void
    {
        $this->update(['is_active' => ! $this->is_active]);
    }
}

