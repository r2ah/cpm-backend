<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intervention extends Model
{
    /** @use HasFactory<\Database\Factories\InterventionFactory> */
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Intervention::class, 'parent_id');
    }

    public function childs(): HasMany
    {
        return $this->hasMany(Intervention::class, 'parent_id')->with('childs');
    }

    public  function opinions(): BelongsToMany
    {
        return $this->belongsToMany(Opinion::class, 'intervention_opinion');
    }
}
