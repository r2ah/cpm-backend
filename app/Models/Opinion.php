<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Opinion extends Model
{
    /** @use HasFactory<\Database\Factories\OpinionFactory> */
    use HasFactory;

	public  function designer(): BelongsTo
	{
	  return $this->belongsTo(Person::class, 'designer_id');
	}

	public  function investor(): BelongsTo
	{
	  return $this->belongsTo(Person::class, 'investor_id');
	}

	public  function builder(): BelongsTo
	{
	  return $this->belongsTo(Person::class, 'investor_id');
	}

	public  function issuingCompany(): BelongsTo
	{
	  return $this->belongsTo(Authority::class, 'issuing_company');
	}

	public  function preparedBy(): BelongsTo
	{
	  return $this->belongsTo(User::class, 'prepared_by');
	}

	public  function reviewedBy(): BelongsTo
	{
	  return $this->belongsTo(User::class, 'reviewed_by');
	}

	public  function approvedBy(): BelongsTo
	{
	  return $this->belongsTo(User::class, 'approved_by');
	}

	public  function interventions(): BelongsToMany
	{
	  return $this->belongsToMany(Intervention::class, 'intervention_opinion');
	}

	public  function attachedFiles(): BelongsToMany
	{
	  return $this->belongsToMany(MediaFiles::class, 'oponion_media_files');
	}	
}
