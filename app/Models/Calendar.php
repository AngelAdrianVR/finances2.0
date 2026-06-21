<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends Model
{
    protected $fillable = [
        'type',
        'title',
        'date',
        'amount',
        'category',
        'description',
        'periodicity',
        'payment_method',
        'status',
        'user_id',
    ];

    protected $casts = [
        'date'   => 'datetime',
        'amount' => 'float',
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

    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', $title);
    }

    public function scopeFromDate(Builder $query, string $date): Builder
    {
        return $query->where('date', '>=', $date);
    }
}

