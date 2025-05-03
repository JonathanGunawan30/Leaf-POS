<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = "shipments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "sale_id", "courier_id", "vehicle_type", "vehicle_number", "shipping_date",
        "estimated_arrival_date", "actual_arrival_date", "status", "shipping_cost", "note"
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, "sale_id", "id");
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, "courier_id", "id");
    }
}
