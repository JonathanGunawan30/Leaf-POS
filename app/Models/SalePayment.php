<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalePayment extends Model
{
    use SoftDeletes;
    protected $table = "sale_payments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "sale_id", "payment_date", "amount", "due_date", "status", "payment_method", "note"
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, "sale_id", "id");
    }
}
