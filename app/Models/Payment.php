<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'payment_method',
        'notes',
        'date',
        'remaining',
        'interest',
        'capital',
        'loan_id',
    ];

    protected $casts = [
        'amount'    => 'float',
        'remaining' => 'float',
        'interest'  => 'float',
        'capital'   => 'float',
        'date'      => 'datetime',
    ];

    // ========================
    // Relationships
    // ========================

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}

