<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intervention extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'parent_id',
    ];



    /**
     * Intervención superior
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Intervention::class,
            'parent_id'
        );
    }



    /**
     * Intervenciones hijas
     */
    public function children(): HasMany
    {
        return $this->hasMany(
            Intervention::class,
            'parent_id'
        )->with('children');
    }



    /**
     * Dictámenes asociados
     */
    public function opinions(): BelongsToMany
    {
        return $this->belongsToMany(
            Opinion::class,
            'intervention_opinion'
        );
    }
}