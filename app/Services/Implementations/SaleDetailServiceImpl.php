<?php

namespace App\Services\Implementations;

use App\Exceptions\ProductNotFound;
use App\Exceptions\SaleDetailNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Interfaces\SaleDetailService;

class SaleDetailServiceImpl implements SaleDetailService
{
    public function store($data, $saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $product = Product::with('taxes')->find($data['product_id']);
        if(!$product) {
            throw new ProductNotFound();
        }

        if ($product->stock < $data['quantity']) {
            throw new StockNotEnoughException($product->name);
        }

        $subTotal = $data['quantity'] * $product->selling_price;

        $saleDetail = $sale->details()->create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'unit_price' => $product->selling_price,
            'sub_total' => $subTotal,
            'note' => $data['note'] ?? null,
        ]);

        $product->decrement('stock', $data['quantity']);

        return $saleDetail;
    }

    public function delete($saleId, $saleDetailId)
    {
        $sale = Sale::findOrFail($saleId);
        $detail = $sale->details()->where('id', $saleDetailId)->first();

        if (!$detail) {
            throw new SaleDetailNotFoundException();
        }

        $detail->product->increment('stock', $detail->quantity);
        $detail->forceDelete();
    }

}
