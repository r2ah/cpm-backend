<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    /** @use HasFactory<\Database\Factories\CommissionFactory> */
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Commission::class, 'parent_id');
    }

    public function childs(): HasMany
    {
        return $this->hasMany(Commission::class, 'parent_id');
    }

    public  function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }    
}
