<?php

namespace App\Services\Interfaces;

use App\Models\ExpenseCategory;

interface ExpenseCategoryService
{
    public function update(int $id, array $payload): ExpenseCategory;
    public function deleteExpenseCategory(int $id): void;
    public function restoreExpenseCategory(int $id): bool;
    public function forceDeleteExpenseCategory(int $id): void;
    public function trashed();
}
