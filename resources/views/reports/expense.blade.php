<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>
<h2>Expense Report</h2>
<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Note</th>
        <th>Category</th>
        <th>User</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($expenses as $expense)
        <tr>
            <td>{{ $expense->expense_date }}</td>
            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            <td>{{ $expense->description }}</td>
            <td>{{ $expense->note }}</td>
            <td>{{ $expense->category->name ?? '' }}</td>
            <td>{{ $expense->user->name ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
