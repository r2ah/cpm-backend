<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commission extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
    ];


    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'users_commissions',
            'commission_id',
            'user_id'
        )
        ->withPivot('position')
        ->withTimestamps();
    }


    public function parent()
    {
        return $this->belongsTo(
            Commission::class,
            'parent_id'
        );
    }
}