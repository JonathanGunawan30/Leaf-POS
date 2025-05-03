<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
<h2>Sales Report - {{ $date }}</h2>
<table>
    <thead>
    <tr>
        <th>Invoice Number</th>
        <th>Sale Date</th>
        <th>Customer Name</th>
        <th>Grand Total</th>
        <th>Payment Status</th>
        <th>Status</th>
        <th>Courier</th>
        <th>Due Date</th>
        <th>Created By</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($sales as $sale)
        <tr>
            <td>{{ $sale['invoice_number'] }}</td>
            <td>{{ $sale['sale_date'] }}</td>
            <td>{{ $sale['customer_name'] }}</td>
            <td>{{ $sale['grand_total'] }}</td>
            <td>{{ $sale['payment_status'] }}</td>
            <td>{{ $sale['status'] }}</td>
            <td>{{ $sale['courier'] }}</td>
            <td>{{ $sale['due_date'] }}</td>
            <td>{{ $sale['created_by'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9">No sales data found</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
