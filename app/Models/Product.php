<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "products";

    protected $fillable = [
        "name", "sku", "purchase_price", "selling_price", "stock", "stock_alert", "unit_id",
        "description", "brand", "discount", "weight", "volume", "barcode", "category_id",
        "images"
    ];

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_taxes')
            ->using(\App\Models\ProductTax::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, "product_id", "id");
    }

    public function sales(): HasMany
    {
        return $this->hasMany(SaleDetail::class, "product_id", "id");
    }




}
