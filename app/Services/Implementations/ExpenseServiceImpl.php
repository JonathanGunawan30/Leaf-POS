<?php

namespace App\Services\Implementations;

use App\Exceptions\ExpenseForceException;
use App\Exceptions\ExpenseRestoreException;
use App\Models\Expense;
use App\Services\Interfaces\ExpenseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseServiceImpl implements ExpenseService
{
    public function store(array $request)
    {
        $user = Auth::user();

        return Expense::create([
            'description' => $request['description'] ?? null,
            'expense_date' => $request['expense_date'],
            'note' => $request['note'] ?? null,
            'amount' => $request['amount'],
            'category_id' => $request['category_id'],
            'user_id' => $user->id,
        ]);
    }

    public function show($id): Expense
    {
        $expense = Expense::with(['user.role', 'category'])->find($id);

        if(!$expense){
            throw new ModelNotFoundException();
        }

        return $expense;
    }

    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = Expense::with(['user.role', 'category']);

        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        return $query->orderByDesc('expense_date')->paginate($request->get('per_page', 10));
    }

    public function update($id, array $data): Expense
    {
        $expense = Expense::find($id);

        if(!$expense){
            throw new ModelNotFoundException();
        }

        $expense->update($data);

        return $expense->load(['user.role', 'category']);
    }

    public function delete($id): Expense
    {
        $expense = Expense::find($id);
        if(!$expense){
            throw new ModelNotFoundException();
        }

        $expense->delete();

        return $expense;
    }

    public function restore($id): Expense
    {
        $expense = Expense::withTrashed()->find($id);
        if(!$expense){
            throw new ModelNotFoundException();
        }

        if (!$expense->trashed()) {
            throw ExpenseRestoreException::notDeleted();
        }

        $expense->restore();

        return $expense;
    }

    public function force($id): Expense
    {
        $expense = Expense::withTrashed()->find($id);
        if(!$expense){
            throw new ModelNotFoundException();
        }

        if (!$expense->trashed()) {
            throw ExpenseForceException::notDeleted();
        }

        $expense->forceDelete();

        return $expense;

    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        return Expense::onlyTrashed()->latest("deleted_at")->paginate($perPage);
    }
}
