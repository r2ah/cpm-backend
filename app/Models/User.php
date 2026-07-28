<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'last_activity',
];


    protected $guard_name = 'api';


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
        //
    ];


    /*
    |--------------------------------------------------------------------------
    | OPINIONS
    |--------------------------------------------------------------------------
    */


    public function opinionsPreparedBy(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'prepared_by'
        );
    }


    public function opinionsReviewedBy(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'reviewed_by'
        );
    }


    public function opinionsApprovedBy(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'approved_by'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | PROCEEDINGS (ACTAS)
    |--------------------------------------------------------------------------
    */


    /**
     * Actas donde el usuario participa.
     */
    public function proceedings(): BelongsToMany
    {
        return $this->belongsToMany(
            Proceeding::class,
            'users_proceedings',
            'user_id',
            'proceeding_id'
        );
    }


    /**
 * Actas elaboradas por el usuario.
 */
public function proceedingsPrepared(): HasMany
{
    return $this->hasMany(
        Proceeding::class,
        'elaborado_por'
    );
}
    /*
    |--------------------------------------------------------------------------
    | COMMISSIONS
    |--------------------------------------------------------------------------
    */


    /**
     * Comisiones donde participa el usuario.
     */
  public function commissions(): BelongsToMany
{
    return $this->belongsToMany(
        Commission::class,
        'users_commissions',
        'user_id',
        'commission_id'
    )
    ->withPivot('position')
    ->withTimestamps();
}



    /*
    |--------------------------------------------------------------------------
    | OPINION STATUS HISTORY
    |--------------------------------------------------------------------------
    */


    public function opinionsStatus(): BelongsToMany
    {
        return $this->belongsToMany(
            Opinion::class,
            'historical_opinion_states'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | COMMISSION SIGNATURE / ATTENDANCE
    |--------------------------------------------------------------------------
    */


    public function commissionsSignedTo(): HasMany
    {
        return $this->hasMany(
            Commission::class,
            'signed_to'
        );
    }


    public function commissionsAttendedBy(): HasMany
    {
        return $this->hasMany(
            Commission::class,
            'attended_by'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */


    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'password' => 'hashed',
    ];
}
}