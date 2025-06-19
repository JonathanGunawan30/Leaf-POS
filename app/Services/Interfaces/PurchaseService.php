<?php

namespace App\Services\Interfaces;

use App\Models\Purchase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response;

interface PurchaseService
{
    public function store(array $data): Purchase;
    public function show($id): Purchase;
    public function update(array $data, $id): Purchase;
    public function getAll(): LengthAwarePaginator;
    public function softdelete($id): Purchase;
    public function restore($id): Purchase;
    public function trashed(): LengthAwarePaginator;
    public function harddelete($id): Purchase;
}
