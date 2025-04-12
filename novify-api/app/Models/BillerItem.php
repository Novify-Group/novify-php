<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillerItem extends Model
{
    protected $fillable = [
        'biller_id',
        'name',
        'code',
        'amount',
        'min_amount',
        'max_amount',
        'is_amount_fixed',
        'status',
        'description'
    ];

    public function biller(): BelongsTo
    {
        return $this->belongsTo(Biller::class);
    }
} 