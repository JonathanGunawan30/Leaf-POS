<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Sale::with(['customer', 'shipments.courier', 'user', 'shipments']);

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('sale_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('sale_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['customer_id'])) {
            $query->where('customer_id', $this->filters['customer_id']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query->get()->map(function ($sale) {
            $invoiceNumber = $sale->invoice_number ?? $sale->invoice_downpayment_number ?? '-';

            return [
                'Invoice Number' => $invoiceNumber,
                'Sale Date' => $sale->sale_date,
                'Customer Name' => $sale->customer->name ?? '-',
                'Grand Total' => number_format($sale->grand_total, 2),
                'Payment Status' => ucfirst($sale->payment_status),
                'Status' => ucfirst($sale->status),
                'Courier' => $sale->shipments->pluck('courier.name')->filter()->join(', ') ?: '-',
                'Created By' => $sale->user->name ?? '-',
                'Due Date' => $sale->due_date ?? '-',
            ];
        });

    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Sale Date',
            'Customer Name',
            'Grand Total',
            'Payment Status',
            'Status',
            'Courier',
            'Created By',
            'Due Date',
        ];
    }
}
