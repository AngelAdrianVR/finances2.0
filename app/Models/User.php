<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'total_money',
        'link_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'total_money'       => 'float',
        ];
    }

    // ========================
    // Relationships
    // ========================

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function recurringIncomes(): HasMany
    {
        return $this->hasMany(RecurringIncome::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(Outcome::class);
    }

    public function recurringOutcomes(): HasMany
    {
        return $this->hasMany(RecurringOutcome::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(Calendar::class);
    }

    public function bankCards(): HasMany
    {
        return $this->hasMany(BankCard::class);
    }

    // User links — vinculaciones con otros usuarios
    public function sentLinks(): HasMany
    {
        return $this->hasMany(UserLink::class, 'user_id');
    }

    public function receivedLinks(): HasMany
    {
        return $this->hasMany(UserLink::class, 'linked_user_id');
    }

    // ========================
    // User Links
    // ========================

    /**
     * Get all accepted linked users.
     */
    public function linkedUsers()
    {
        $sentIds = $this->sentLinks()->where('status', 'accepted')->pluck('linked_user_id');
        $receivedIds = $this->receivedLinks()->where('status', 'accepted')->pluck('user_id');

        return User::whereIn('id', $sentIds->merge($receivedIds))->get();
    }

    /**
     * Generate a unique 8-character link code.
     */
    public static function generateUniqueLinkCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('link_code', $code)->exists());

        return $code;
    }

    // ========================
    // Helpers
    // ========================

    /**
     * Total amount of active loans given (money the user is owed).
     */
    public function getTotalLoansGiven(): float
    {
        return (float) $this->loans()
            ->where('is_for_me', false)
            ->where('status', 'En curso')
            ->get()
            ->sum(fn (Loan $loan) => $loan->remaining);
    }
}

