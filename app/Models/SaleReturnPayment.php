<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnPayment extends Model
{
    use SoftDeletes;
    protected $table = "sale_return_payments";
    protected $primaryKey = "id";
    protected $fillable = [
        'sale_return_id',
        'payment_date',
        'payment_method',
        'due_date',
        'amount',
        'note',
        'status',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }

}
