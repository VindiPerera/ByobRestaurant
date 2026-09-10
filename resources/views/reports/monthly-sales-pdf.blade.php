<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.4;
        }
        .page {
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 12px;
        }
        .timestamp {
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        thead {
            background: #f3f4f6;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 11px;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 9px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody tr:nth-child(even) {
            background: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            border-top: 2px solid #1f2937;
            border-bottom: none;
            font-weight: 700;
            font-size: 14px;
            color: #1f2937;
            padding-top: 12px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>MONTHLY SALES REPORT</h1>
            <p>{{ config('app.name', 'Restaurant') }} - Sales Amount by Month</p>
            <p>{{ $rangeLabel }}</p>
        </div>

        <div class="timestamp">
            Generated: {{ $generatedAt }}
        </div>

        @if($monthlySales->isEmpty())
            <div class="no-data">No sales data available</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-right">Sales Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlySales as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td>Grand Total</td>
                        <td class="text-right">LKR {{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
