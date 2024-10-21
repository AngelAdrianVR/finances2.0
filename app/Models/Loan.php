<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'type',
        'beneficiary_name',
        'amount',
        'profitability',
        'profiability_period',
        'expired_date',
        'status',
        'description',
        'user_id',
    ];
    
    protected $casts = [
        'expired_date' => 'date',
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
