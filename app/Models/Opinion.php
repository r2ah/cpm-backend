<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Intervention;

class Opinion extends Model
{
    use HasFactory;


    protected $fillable = [

        'entity',
        'address',

        'designer_id',
        'investor_id',
        'builder_id',

        'general_characteristics',

        'issuing_company',
        'issuing_document_code',

        'considerations',
        'observations',

        'state',
        'date',

        'commission_id',

        'prepared_by',
        'reviewed_by',
        'approved_by',
    ];



    public function designer(): BelongsTo
    {
        return $this->belongsTo(
            Person::class,
            'designer_id'
        );
    }


    public function investor(): BelongsTo
    {
        return $this->belongsTo(
            Person::class,
            'investor_id'
        );
    }


    public function builder(): BelongsTo
    {
        return $this->belongsTo(
            Person::class,
            'builder_id'
        );
    }


    public function issuingCompany(): BelongsTo
    {
        return $this->belongsTo(
            Authority::class,
            'issuing_company'
        );
    }


    public function commission(): BelongsTo
    {
        return $this->belongsTo(
            Commission::class,
            'commission_id'
        );
    }


    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'prepared_by'
        );
    }


    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }


    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
    public function interventions(): BelongsToMany
{
    return $this->belongsToMany(
        Intervention::class,
        'intervention_opinion',
        'opinion_id',
        'intervention_id'
    );
}

}