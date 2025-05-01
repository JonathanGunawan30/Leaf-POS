<?php

namespace App\Services\Interfaces;

use App\Models\Product;

interface ProductService
{
    public function create(array $data): Product;
    public function showProduct($id): Product;
    public function generateBarcodeBase64(string $barcode): string;
    public function update(array $data, $id);
    public function getAll();
    public function softdelete($id);
    public function restore($id);
    public function harddelete($id);
    public function trashed();
}
