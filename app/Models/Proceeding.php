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
        'signed_document',
    ];


    /**
     * Documento firmado asociado al acta
     */
    public function signedDocument(): BelongsTo
    {
        return $this->belongsTo(
            MediaFiles::class,
            'signed_document'
        );
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
        );
    }


    /**
     * Comisión a la que pertenece el acta
     */
    public function commission(): BelongsTo
    {
        return $this->belongsTo(
            Commission::class,
            'commission_id'
        );
    }
}