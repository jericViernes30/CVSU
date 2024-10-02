<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite('resources/css/app.css')
    <title>Monthly Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 5px;
        }

        td {
            font-size: 12px;
        }

        .total{
            font-weight: 600;
            font-size: 15px;
        }

        .date-1{
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="">
    @php
        $totalItemsSold = 0;
        $totalCost = 0;
        $totalRetail = 0;
        $totalSales = 0;
        $totalNetSales = 0;
        $totalGrossProfit = 0;
    @endphp

    @foreach($dates as $report)
        @php
            // Calculate the total occurrences for this day
            $totalOccurrences = array_reduce($report['food_details'], function($sum, $food) {
                return $sum + $food['occurrences'];
            }, 0);

            // Calculate net sales for the day
            $netSales = $report['total'];

            // Calculate gross profit for the day
            $grossProfit = $totalOccurrences > 0 ? $netSales - $report['total_cost'] : 0;

            // Update totals
            $totalItemsSold += $totalOccurrences;
            $totalCost += $report['total_cost'];
            $totalRetail += $report['total_retail'];
            $totalSales += $netSales;
            $totalNetSales += $netSales;
            $totalGrossProfit += $grossProfit;
        @endphp
    @endforeach

    <h1>{{ $dates[0]['reportMonth'] }} Sales Report</h1>
    <div class="dates">
        <p class="date-1">Report Date: <span>{{ $dates[0]['dateToday'] }}</span></p>
        <p>Report Period: <span>{{ $dates[0]['monthStart'] }} to {{ $dates[0]['monthEnd'] }}</span></p>
    </div>
    <table class="min-w-full border border-gray-300 text-lg bg-red-500">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Date</th>
                <th class="py-2 px-4 border-b">Items Sold</th>
                <th class="py-2 px-4 border-b">Cost</th>
                <th class="py-2 px-4 border-b">Retail</th>
                <th class="py-2 px-4 border-b">Total</th>
                <th class="py-2 px-4 border-b">Net Sales</th>
                <th class="py-2 px-4 border-b">Gross Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $data)
                <tr>
                    <td class="py-1 px-4 border-b">{{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') }}</td>
                    <td class="py-1 px-4 border-b">{{ count($data['tickets']) }}</td>
                    <td class="py-1 px-4 border-b">{{ number_format($data['total_cost'], 2) }}</td>
                    <td class="py-1 px-4 border-b">{{ number_format($data['total_retail'], 2) }}</td>
                    <td class="py-1 px-4 border-b">{{ number_format($data['total'], 2) }}</td>
                    <td class="py-1 px-4 border-b">{{ number_format($data['total_retail'] - $data['total_cost'], 2) }}</td> <!-- Net Sales -->
                    <td class="py-1 px-4 border-b">{{ number_format($data['total_retail'] - $data['total_cost'], 2) }}</td> <!-- Gross Profit -->
                </tr>
            @endforeach
                <tr class="">
                    <td class="py-1 px-4 border-b total">Total</td>
                    <td class="py-1 px-4 border-b total">{{ $totalItemsSold }}</td>
                    <td class="py-1 px-4 border-b total">{{ number_format($totalCost, 2) }}</td>
                    <td class="py-1 px-4 border-b total">{{ number_format($totalRetail, 2) }}</td>
                    <td class="py-1 px-4 border-b total">{{ number_format($totalSales, 2) }}</td>
                    <td class="py-1 px-4 border-b total">{{ number_format($totalNetSales, 2) }}</td>
                    <td class="py-1 px-4 border-b total">{{ number_format($totalGrossProfit, 2) }}</td>
                </tr>
        </tbody>
    </table>
</body>
</html>
