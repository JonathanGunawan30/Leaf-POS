<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentReturn extends Model
{
    use SoftDeletes;

    protected $table = "shipment_returns";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        "sale_return_id", "courier_id", "shipping_date", "estimated_arrival_date",
        "actual_arrival_date", "vehicle_number", "vehicle_type",
        "status", "shipping_cost", "note"
    ];

    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, "sale_return_id", "id");
    }
}
