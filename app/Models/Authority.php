<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Authority extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorityFactory> */
    use HasFactory;

    public function opinions(): HasMany
    {
        return $this->hasMany(Opinion::class);
    }    
}
