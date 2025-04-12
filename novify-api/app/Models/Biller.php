<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Biller extends Model
{
    protected $fillable = [
        'bill_category_id',
        'name',
        'code',
        'logo',
        'status',
        'description'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BillCategory::class, 'bill_category_id');
    }

    public function billerItems(): HasMany
    {
        return $this->hasMany(BillerItem::class);
    }
} 