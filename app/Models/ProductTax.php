<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTax extends Pivot
{
    // use SoftDeletes;
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "product_taxes";

    protected $fillable = [
        "name", "product_id", "tax_id", "amount"
    ];
}
