<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_natural_person',
    ];

    /**
     * Opiniones donde la persona es diseñador.
     */
    public function designerOpinions(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'designer'
        );
    }

    /**
     * Opiniones donde la persona es inversionista.
     */
    public function investorOpinions(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'investor'
        );
    }

    /**
     * Opiniones donde la persona es constructor.
     */
    public function builderOpinions(): HasMany
    {
        return $this->hasMany(
            Opinion::class,
            'builder'
        );
    }

    /**
     * Citas de la persona.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(
            Appointment::class,
            'person_id'
        );
    }
}