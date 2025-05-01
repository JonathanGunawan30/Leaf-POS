<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "purchases";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "invoice_number", "purchase_date", "total_amount", "total_tax",
        "total_discount", "shipping_amount", "grand_total", "status", "payment_status",
        "due_date", "estimated_arrival_date", "actual_arrival_date", "user_id", "supplier_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, "supplier_id", "id");
    }


    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'purchase_taxes')
            ->using(\App\Models\PurchaseTax::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, "purchase_id", "id");
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class, "purchase_id", "id");
    }

    protected static function booted()
    {
        static::deleting(function ($purchase) {
            $purchase->details()->each(function ($detail) {
                $detail->delete();
            });

            $purchase->payments()->each(function ($payment) {
                $payment->delete();
            });
        });
    }


}
