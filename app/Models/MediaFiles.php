<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MediaFiles extends Model
{
    use HasFactory;

    protected $fillable = ['path'];

	public  function opinions(): BelongsToMany
	{
	  return $this->belongsToMany(Opinion::class);
	}    

    public function proceeding(): HasOne
    {
        return $this->hasOne(Proceeding::class, 'designer');
    }    
}
