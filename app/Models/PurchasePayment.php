<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePayment extends Model
{
    use SoftDeletes;
    protected $table = "purchase_payments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "payment_date", "amount", "due_date", "payment_method", "status", "note", "purchase_id"
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, "purchase_id", "id");
    }
}
