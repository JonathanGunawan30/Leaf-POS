<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = "sales";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "invoice_number", "sale_date", "total_amount", "total_tax", "total_discount",
        "grand_total", "status", "payment_status", "due_date", "note", "user_id", "customer_id"
        ];

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class, "sale_id", "id")->withTrashed();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, "sale_id", "id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }

public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, "sale_id", "id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function saleReturn(): HasMany
    {
        return $this->hasMany(SaleReturn::class, "sale_id", "id");
    }
    protected static function booted()
    {
        static::deleting(function ($sale) {
            $sale->details()->delete();
            $sale->payments()->delete();
            $sale->shipments()->delete();
        });

        static::restored(function ($sale) {
            $sale->details()->withTrashed()->restore();
            $sale->payments()->withTrashed()->restore();
            $sale->shipments()->withTrashed()->restore();
        });

        static::deleting(function ($sale) {
            if ($sale->isForceDeleting()) {
                $sale->details()->forceDelete();
                $sale->payments()->forceDelete();
                $sale->shipments()->forceDelete();
            }
        });
    }

}
