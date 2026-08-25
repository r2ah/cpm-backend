<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'person_id',
        'commission_id',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Persona asociada a la cita.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(
            Person::class,
            'person_id'
        );
    }

    /**
     * Comisión asociada a la cita.
     */
    public function commission(): BelongsTo
    {
        return $this->belongsTo(
            Commission::class,
            'commission_id'
        );
    }
}