<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringOutcome extends Model
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

    //relationships
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
