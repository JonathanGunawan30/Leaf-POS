<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use SoftDeletes;
    protected $table = "sale_details";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "sale_id", "product_id", "quantity", "unit_price", "sub_total", "note"
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, "sale_id", "id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

}
