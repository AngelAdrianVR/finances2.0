<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'type',
        'beneficiary_name',
        'lender_name',
        'amount',
        'status',
        'loan_date',
        'expired_date',
        'profitability',
        'profitability_type',
        'profitability_mode',
        'profitability_period',
        'payment_periodicity',
        'description',
        'automatic',
        'is_for_me',
        'user_id',
    ];

    protected $casts = [
        'amount'        => 'float',
        'profitability' => 'float',
        'automatic'     => 'boolean',
        'is_for_me'     => 'boolean',
        'loan_date'     => 'datetime',
        'expired_date'  => 'datetime',
    ];

    // ========================
    // Relationships
    // ========================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ========================
    // Scopes
    // ========================

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'En curso');
    }

    // ========================
    // Accessors / Helpers
    // ========================

    /**
     * Get the remaining balance of this loan based on the latest payment.
     */
    public function getRemainingAttribute(): float
    {
        $lastPayment = $this->payments()->latest('id')->first();

        return $lastPayment ? (float) $lastPayment->remaining : (float) $this->amount;
    }
}

