<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'ci',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'commission_id',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(
            Commission::class,
            'commission_id'
        );
    }
}