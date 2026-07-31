<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpinionDocument extends Model
{
    protected $fillable = [

        'opinion_id',

        'original_name',

        'file_name',

        'path',

        'mime_type',

        'size'

    ];

    public function opinion(): BelongsTo
    {
        return $this->belongsTo(
            Opinion::class
        );
    }
}