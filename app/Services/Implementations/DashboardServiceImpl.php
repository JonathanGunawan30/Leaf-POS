<?php

namespace App\Services\Implementations;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Interfaces\DashboardService;
use Carbon\Carbon;

class DashboardServiceImpl implements DashboardService
{
    public function getSummary()
    {
        $request = request();

        $month = $request->query('month');
        $year = $request->query('year');
        $now = Carbon::now();

        $month = $month ?? $now->month;
        $year = $year ?? $now->year;

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return [
            "total_products" => Product::count(),
            "low_stock_products" => Product::whereColumn('stock', '<=', 'stock_alert')->count(),
            "sales_this_month" => Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('grand_total'),
            "purchases_this_month" => Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])->sum('grand_total'),
            "expenses_this_month" => Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount'),
            "total_customers" => Customer::count(),
            "total_suppliers" => Supplier::count(),
            "sales_indent" => Sale::where('status', 'indent')->count(),
            "sales_shipped" => Sale::where('status', 'shipped')->count(),
            "purchases_shipped" => Purchase::where('status', 'shipped')->count(),
            "partially_paid_sales" => Sale::where('payment_status', 'partially_paid')->count(),
            "partially_paid_purchases" => Purchase::where('payment_status', 'partially_paid')->count(),
            "top_products_all_time" => Product::withSum('salesDetails', 'quantity')
                ->orderByDesc('sales_details_sum_quantity')
                ->limit(5)
                ->get(['id', 'name']),
            "top_products_this_month" => Product::whereHas('salesDetails.sale', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('sale_date', [$startOfMonth, $endOfMonth]);
            })
                ->withSum(['salesDetails as total_sold_this_month' => function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereHas('sale', function ($q2) use ($startOfMonth, $endOfMonth) {
                        $q2->whereBetween('sale_date', [$startOfMonth, $endOfMonth]);
                    });
                }], 'quantity')
                ->orderByDesc('total_sold_this_month')
                ->limit(5)
                ->get(['id', 'name']),

            "couriers_available" => Courier::where('status', 'available')->count(),
            "users_active" => User::where('status', 'active')->count(),
        ];
    }


}
