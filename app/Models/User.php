<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Symfony\Component\Console\Output\Output;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'total_money',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relationships
    public function incomes() :HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function recurringIncomes() :HasMany
    {
        return $this->hasMany(RecurringIncome::class);
    }

    public function outcomes() :HasMany
    {
        return $this->hasMany(Outcome::class);
    }

    public function loans() :HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function calendars() :HasMany
    {
        return $this->hasMany(Calendar::class);
    }

    public function bankCards() :HasMany
    {
        return $this->hasMany(BankCard::class);
    }

    // otros metodos
    public function getTotalLoan()
    {
        $loans = $this->loans()->where('is_for_me', false)->where('status', 'En curso')->get();
        $totalLoan = 0;
        foreach ($loans as $loan) {
            $totalLoan += $loan->payments->last()->remaining ?? $loan->amount;
        }
        return $totalLoan;        
    }
}
