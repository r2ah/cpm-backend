<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    protected $fillable = [
    'name',
    'email',
    'phone',
    'is_natural_person',
];

    public function designerOpinions(): HasMany
    {
        return $this->hasMany(Opinion::class, 'designer');
    }

    public function investorOpinions(): HasMany
    {
        return $this->hasMany(Opinion::class, 'investor');
    }

    public function builderOpinions(): HasMany
    {
        return $this->hasMany(Opinion::class, 'builder');
    }
}
