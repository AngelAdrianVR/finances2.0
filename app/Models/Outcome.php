<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outcome extends Model
{
    protected $fillable = [
        'is_recurring_outcome',
        'payment_method',
        'periodicity',
        'description',
        'category',
        'concept',
        'user_id',
        'amount',
    ];

    //relationships
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
