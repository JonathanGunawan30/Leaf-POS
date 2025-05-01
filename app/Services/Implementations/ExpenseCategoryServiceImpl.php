<?php

namespace App\Services\Implementations;

use App\Exceptions\ExpenseCategoryNotFoundException;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\Interfaces\ExpenseCategoryService;

class ExpenseCategoryServiceImpl implements ExpenseCategoryService
{

    public function update(int $id, array $payload): ExpenseCategory
    {
        $category = ExpenseCategory::find($id);
        if (!$category) {
            throw new ExpenseCategoryNotFoundException();
        }
        $category->update($payload);

        return $category;
    }

    public function deleteExpenseCategory(int $id): void
    {
        $category = ExpenseCategory::find($id);

        if (!$category) {
            throw new ExpenseCategoryNotFoundException();
        }

        if($category->expenses()->withTrashed()->exists()){
            throw new \Exception("Cannot delete this category because it is associated with existing expenses.");
        }

        $category->delete();
    }

    public function restoreExpenseCategory(int $id): bool
    {
        $category = ExpenseCategory::onlyTrashed()->find($id);
        if (!$category) {
            throw new ExpenseCategoryNotFoundException();
        }

        return $category->restore();
    }

    public function forceDeleteExpenseCategory(int $id): void
    {
        $category = ExpenseCategory::withTrashed()->find($id);

        if (!$category) {
            throw new ExpenseCategoryNotFoundException();
        }

        if (!$category->trashed()) {
            throw new \Exception("Expense category must be soft deleted before force delete");
        }

        if ($category->expenses()->withTrashed()->exists()) {
            throw new \Exception("Cannot force delete category with related expenses");
        }

        $category->forceDelete();
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        return ExpenseCategory::onlyTrashed()->latest("deleted_at")->paginate($perPage);
    }
}
