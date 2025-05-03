<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #ccc;
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        .info-table td {
            border: none;
            padding: 4px 0;
        }
        .signature {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .signature td {
            padding-top: 60px;
        }
    </style>
</head>
<body>

<h2>SURAT JALAN</h2>

<table class="info-table">
    <tr>
        <td><strong>Invoice:</strong></td>
        <td>{{ $sale->invoice_number ?? $sale->invoice_downpayment_number ?? '-' }}</td>
        <td><strong>Tanggal Kirim:</strong></td>
        <td>{{ $shipments->shipping_date }}</td>
    </tr>
    <tr>
        <td><strong>No Surat Jalan:</strong></td>
        <td>{{ $sale->delivery_number ?? '-' }}</td>
        <td><strong>Tanggal Jatuh Tempo:</strong></td>
        <td>{{ $sale->due_date ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Nama Sales:</strong></td>
        <td>{{ $sale->user->name ?? '-' }}</td>
        <td><strong>Kurir:</strong></td>
        <td>{{ $shipment->courier->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Customer:</strong></td>
        <td colspan="3">
            {{ $sale->customer->name ?? '-' }}<br>
        </td>
    </tr>
    <tr>
        <td><strong>Address:</strong></td>
        <td colspan="3">
            {{ $sale->customer->address ?? '-' }}
        </td>
    </tr>
    <tr>
        <td><strong>Payment Method:</strong></td>
        <td>
            {{ $sale->payments()->payment_method ?? '-' }}
        </td>
        <td><strong>Payment Status:</strong></td>
        <td>
            {{ $sale->payments()->status ?? '-' }}
        </td>
    </tr>
    <tr>
        <td><strong>Catatan:</strong></td>
        <td colspan="3">{{ $shipments->notes ?? '-' }}</td>
    </tr>
</table>

<h4 style="margin-top: 25px;">Detail Produk</h4>
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>SKU</th>
        <th>Qty</th>
        <th>Unit</th>
        <th>Harga Satuan</th>
        <th>Sub Total</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($sale->details as $index => $detail)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $detail->product->name ?? '-' }}</td>
            <td>{{ $detail->product->sku ?? '-' }}</td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ $detail->product->unit->name ?? '-' }}</td>
            <td>{{ $detail->product->selling_price ? 'Rp ' . number_format($detail->product->selling_price, 0, ',', '.') : '-' }}</td>
            <td>{{ 'Rp ' . number_format($detail->sub_total, 0, ',', '.') }}</td>
            <td>{{ $detail->notes ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="signature">
    <tr>
        <td><strong>Dibuat oleh</strong></td>
        <td><strong>Diperiksa oleh</strong></td>
        <td><strong>Disetujui oleh</strong></td>
        <td><strong>Diterima oleh</strong></td>
    </tr>
    <tr>
        <td>__________________</td>
        <td>__________________</td>
        <td>__________________</td>
        <td>__________________</td>
    </tr>
</table>

</body>
</html>
