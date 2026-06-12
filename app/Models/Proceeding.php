<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proceeding extends Model
{
    /** @use HasFactory<\Database\Factories\ProceedingFactory> */
    use HasFactory;

	public  function investor(): BelongsTo
	{
	  return $this->belongsTo(MediaFiles::class, 'signed_document');
	}    
}
