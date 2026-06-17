<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
//use Laravel\Jetstream\HasProfilePhoto;
// use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    //use HasProfilePhoto;
    // use HasTeams;
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

    public function opinionsPreparedBy(): HasMany
    {
        return $this->hasMany(Opinion::class, 'prepared_by');        
    }

    public function opinionsReviewedBy(): HasMany
    {
        return $this->hasMany(Opinion::class, 'reviewed_by');
    }

    public function opinionsApprovedBy(): HasMany
    {
        return $this->hasMany(Opinion::class, 'approved_by');
    }

    public function proceedings()
    {
        return $this->belongsToMany(Proceeding::class, 'users_proceedings');
    }

    public function commissions()
    {
        return $this->belongsToMany(Commission::class, 'users_commisions');
    }

    public function opinionsStatus(): HasMany
    {
        return $this->belongsToMany(Opinion::class, 'historical_opinion_states');
    }
 
    public function commissionsSignedTo(): HasMany
    {
        return $this->hasMany(Commission::class, 'signed_to');
    }

    public function commissionsAttendedBy(): HasMany
    {
        return $this->hasMany(Commission::class, 'attended_by');
    }    

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
}
