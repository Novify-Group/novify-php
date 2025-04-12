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

    protected $appends =['item_input_fields'];
    public function biller(): BelongsTo
    {
        return $this->belongsTo(Biller::class);
    }

    public function getItemInputFieldsAttribute(){

        $beneficiaryLabel = (strpos($this->name,'rent')>-1)?'PRN':'Beneficiary Account';
        
        return [
            ['label'=>$beneficiaryLabel,'is_required'=>true,'postField'=>'beneficiary_account','type'=>'string'],
            ['label'=>'Narration','is_required'=>false,'postField'=>'narration','type'=>'string']
        ];

    }
} 