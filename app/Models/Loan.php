<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'profitability_period',
        'payment_periodicity',
        'profitability_type',
        'profitability_mode',
        'beneficiary_name',
        'profitability',
        'expired_date',
        'description',
        'lender_name',
        'is_for_me',
        'loan_date',
        'automatic', // para registrar ingreso o gasto automatico al abonar
        'user_id',
        'amount',
        'status',
        'type',
    ];
    
    protected $casts = [
        'expired_date' => 'date',
        'loan_date' => 'date',
    ];

    //relationships
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments() :HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
