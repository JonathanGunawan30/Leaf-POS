<?php

namespace App\Exports;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpenseReportPdfExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(protected $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = Expense::query()->with(['category', 'user']);

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('expense_date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('expense_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query->get();
    }
}
