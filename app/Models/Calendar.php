<?php

namespace App\Models;

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
        'date' => 'date'
    ];

    //relationships
    public function user() : BelongsTo 
    {
        return $this->belongsTo(User::class);    
    }
}
