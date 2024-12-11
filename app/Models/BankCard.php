<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankCard extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'bank_name',
        'type',
        'notes',
        'is_active',
        'user_id',
    ];

    //relationships
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
