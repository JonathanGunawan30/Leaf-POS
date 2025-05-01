<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model
{
    use SoftDeletes;
    protected $table = "purchase_details";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "purchase_id", "product_id", "quantity", "unit_price", "sub_total", "note"
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, "purchase_id", "id");
    }
}
