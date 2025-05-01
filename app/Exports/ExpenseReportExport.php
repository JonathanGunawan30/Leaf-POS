<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;



class ExpenseReportExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
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

        return $query->get()->map(function ($expense) {
            return [
                'Date' => $expense->expense_date,
                'Amount' => 'Rp ' . number_format($expense->amount, 0, ',', '.'),
                'Description' => $expense->description,
                'Note' => $expense->note,
                'Category' => optional($expense->category)->name ?? '',
                'User Name' => optional($expense->user)->name ?? '',
                'User Email' => optional($expense->user)->email ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Amount',
            'Description',
            'Note',
            'Category',
            'User Name',
            'User Email',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $rowCount = $sheet->getHighestRow();
                $columnCount = $sheet->getHighestColumn();

                $cellRange = "A1:{$columnCount}{$rowCount}";

                $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                foreach (range('A', $columnCount) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
