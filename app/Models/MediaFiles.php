<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path'
    ];


    /**
     * Relación con opiniones
     */
    public function opinions(): BelongsToMany
    {
        return $this->belongsToMany(
            Opinion::class
        );
    }


    /**
     * Actas que tienen asociado este documento
     */
    public function proceedings(): BelongsToMany
    {
        return $this->belongsToMany(
            Proceeding::class,
            'proceeding_media_files',
            'media_file_id',
            'proceeding_id'
        )->withTimestamps();
    }
}