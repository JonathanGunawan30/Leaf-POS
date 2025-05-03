<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesReportPdfExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Sale::with(['customer', 'shipments.courier', 'user']);

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
                'invoice_number' => $invoiceNumber,
                'sale_date' => $sale->sale_date,
                'customer_name' => $sale->customer->name ?? '-',
                'grand_total' => number_format($sale->grand_total, 2),
                'payment_status' => ucfirst($sale->payment_status),
                'status' => ucfirst($sale->status),
                'courier' => $sale->shipments->pluck('courier.name')->filter()->join(', ') ?: '-',
                'due_date' => $sale->due_date ?? '-',
                'created_by' => $sale->user->name ?? '-',
            ];
        });
    }
}
