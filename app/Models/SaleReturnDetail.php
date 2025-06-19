<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnDetail extends Model
{
    use SoftDeletes;
    protected $table = "sale_return_details";
    protected $primaryKey = "id";
    protected $fillable = [
        'sale_return_id',
        'product_id',
        'quantity',
        'unit_price',
        'note',
        'sub_total',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
