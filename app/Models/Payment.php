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
        'remainig',
        'interest',
        'capital',
        'loan_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    //relationships
    public function loan() :BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
