<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "taxes";

    protected $fillable = [
        "name", "rate"
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_taxes')
            ->using(\App\Models\ProductTax::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_taxes')
            ->using(\App\Models\PurchaseTax::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

}
