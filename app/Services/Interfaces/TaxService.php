<?php

namespace App\Services\Interfaces;

use App\Http\Requests\UpdateTaxRequest;
use App\Models\Tax;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaxService
{
    public function getTaxById($id): Tax;
    public function updateTax($id, UpdateTaxRequest $request): Tax;
    public function getAll():LengthAwarePaginator;
    public function softdelete($id);
    public function restore($id);
    public function hardDelete($id);
    public function trashed();
}
