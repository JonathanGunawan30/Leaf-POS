<?php

namespace App\Services\Interfaces;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExpenseService
{
    public function store(array $request);
    public function show($id): Expense;
    public function getAll(Request $request): LengthAwarePaginator;
    public function update($id, array $data): Expense;
    public function delete($id): Expense;
    public function restore($id): Expense;
    public function force($id): Expense;
    public function trashed();
}
