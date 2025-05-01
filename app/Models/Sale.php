<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
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
        return $this->hasMany(SaleDetail::class, "sale_id", "id");
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
}
