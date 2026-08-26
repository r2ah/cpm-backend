<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Proceeding extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'address',
        'agenda',
        'approaches',
        'aggreements',
        'commission_id',
        'elaborado_por',
        'location',
    ];

    /**
     * Documentos asociados al acta
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaFiles::class,
            'proceeding_media_files',
            'proceeding_id',
            'media_file_id'
        )->withTimestamps();
    }

    /**
     * Usuarios participantes del acta
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'users_proceedings',
            'proceeding_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Comisión
     */
    public function commission(): BelongsTo
    {
        return $this->belongsTo(
            Commission::class,
            'commission_id'
        );
    }

    /**
     * Usuario que elaboró el acta
     */
    public function elaboradoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'elaborado_por'
        );
    }
}