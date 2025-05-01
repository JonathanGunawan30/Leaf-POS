<?php

namespace App\Services\Interfaces;

use App\Models\Supplier;

interface SupplierService
{
    public function getSupplierById($id): Supplier;
    public function updateSupplier($id, $data);
    public function getAll();
    public function deleteSupplier($id);
    public function restore($id);
    public function force($id);
    public function trashed();
}
