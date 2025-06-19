<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'previous_stock',
        'new_stock',
        'adjustment',
        'notes'
    ];

    /**
     * Get the product that was adjusted.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the adjustment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
