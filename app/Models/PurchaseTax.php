<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseTax extends Pivot
{
    //
    protected $table = "purchase_taxes";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        "name", "purchase_id", "tax_id", "amount"
    ];
}
