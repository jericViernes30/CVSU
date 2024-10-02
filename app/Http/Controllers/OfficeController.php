<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Authentication;
use App\Models\History;
use App\Models\GCash;
use App\Models\PendingAccount;
use App\Models\PendingItems;
use App\Models\Stocks;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OfficeController extends Controller
{
    public function adminLogin()
    {
        return view('backoffice/admin_login');
    }

    public function login(Request $request)
    {
        $dateToday = Carbon::now();
        $admin_id = 'Administrator';
        $admin_password = 'administrator1';
        $id = $request->input('name');
        $pass = $request->input('password');
        if ($admin_id == $id) {
            if ($admin_password == $pass) {
                $currentYear = Carbon::now()->year;
                $currentMonth = Carbon::now()->month;

                $itemSoldThisMonth = History::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->count();

                // Get all rows for the current month
                $rowsThisMonth = History::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->get();

                // Initialize arrays to store daily sales and formatted dates
                $dailySalesFormatted = [];
                $dailySales = [];
                $dailyTotalSales = []; // Array to store daily total sales
                $dailySubTotalSales = []; // Array to store daily sub_total sales

                // Calculate gross and net sales for the entire month
                $gross_sales = $rowsThisMonth->sum('total');
                $net_sales = $rowsThisMonth->sum('sub_total');

                // Loop through each day of the current month
                for ($day = 1; $day <= Carbon::now()->daysInMonth; $day++) {
                    // Create a Carbon instance for the current date
                    $date = Carbon::createFromDate($currentYear, $currentMonth, $day);

                    // Format the date as "Month Day, Year" (e.g., "April 24, 2024")
                    $formattedDate = $date->isoFormat('MMMM D, YYYY');

                    // Get all rows with the current date in the created_at column
                    $rowsWithDate = $rowsThisMonth->where('created_at', '>=', $date->startOfDay())
                        ->where('created_at', '<=', $date->endOfDay());

                    // Calculate total sales for the current date
                    $totalSales = $rowsWithDate->sum('total');
                    $subTotalSales = $rowsWithDate->sum('sub_total');

                    // Store the total sales in the array with the formatted date as the key
                    $dailySalesFormatted[$formattedDate] = $totalSales;
                    $dailySales[$date->day] = $totalSales;

                    // Store the daily total sales
                    $dailyTotalSales[$formattedDate] = $rowsWithDate->sum('total');
                    $dailySubTotalSales[$formattedDate] = $subTotalSales;
                }

                // Pass $dailySalesFormatted and $dailyTotalSales to your view
                return view('backoffice/admin_dashboard', [
                    'sales' => $dailySalesFormatted,
                    'dailySales' => $dailySales, // Pass daily sales for charting
                    'dailyTotalSales' => $dailyTotalSales, // Pass daily total sales
                    'dailySubTotalSales' => $dailySubTotalSales, // Pass daily sub_total sales
                    'gross_sales' => $gross_sales,
                    'net_sales' => $net_sales,
                    'totalItemSoldThisMonth' => $itemSoldThisMonth,
                ]);
            } else {
                return redirect()->route('office.login')->with('error', 'The password is incorrect!');
            }
        } else if ($id == '' && $pass == '') {
            return redirect()->route('office.login')->with('error', 'Both fields are empty!');
        } else if ($admin_id != $id && $pass != $admin_password) {
            return redirect()->route('office.login')->with('error', 'Both fields are wrong!');
        } else if ($id != $admin_id && $pass == $admin_password) {
            return redirect()->route('office.login')->with('error', 'The admin id you entered is wrong!');
        }
    }

    public function backToQr()
    {
        $items = Stocks::all();
        return view('backoffice/qr', ['menus' => $items]);
    }

    public function backToItems()
    {
        $data = Stocks::paginate(10); // Paginate based on the selected number of rows
        return view('backoffice/items/items_list', ['items' => $data]);
    }

    public function exportExcel(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = 5; // Adjust as needed, or pass via request parameters

        return Excel::download(new SalesReportExport($currentYear, $currentMonth), 'sales_report.xlsx');
    }

    public function dashboard(Request $request)
    {
        $dateToday = Carbon::now();
        $dateTodayFormatted = $dateToday->isoFormat('MMMM D, YYYY');
        $salesToday = History::whereDate('created_at', $dateToday)->get();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $itemSoldThisMonth = History::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // Get all rows for the current year
        $rowsThisYear = History::whereYear('created_at', $currentYear)->get();

        // Initialize counters
        $morningCount = 0;
        $afternoonCount = 0;
        $eveningCount = 0;

        // Count rows in each time frame
        foreach ($rowsThisYear as $row) {
            $hour = Carbon::parse($row->created_at)->hour;
            if ($hour >= 8 && $hour < 12) {
                $morningCount++;
            } elseif ($hour >= 12 && $hour < 18) {
                $afternoonCount++;
            } elseif ($hour >= 18 && $hour < 22) {
                $eveningCount++;
            }
        }

        // Calculate averages
        $totalRows = $rowsThisYear->count();
        $morningAverage = ($totalRows > 0) ? ($morningCount / $totalRows) * 100 : 0; // Percentage
        $afternoonAverage = ($totalRows > 0) ? ($afternoonCount / $totalRows) * 100 : 0; // Percentage
        $eveningAverage = ($totalRows > 0) ? ($eveningCount / $totalRows) * 100 : 0; // Percentage

        $gross_sales = $rowsThisYear->sum('total');
        $net_sales_today = History::whereDate('created_at', $dateToday)->sum('total');
        $net_sales = $rowsThisYear->sum('sub_total');

        // Get all rows for the current month
        $rowsThisMonth = History::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->get();

        $dailySalesFormatted = [];
        $dailySales = [];
        $dailyTotalSales = []; // Array to store daily total sales
        $dailySubTotalSales = []; // Array to store daily sub_total sales

        // Calculate gross and net sales for the entire month
        $gross_sales = $rowsThisMonth->sum('total');
        $net_sales = $rowsThisMonth->sum('sub_total');

        // Loop through each day of the current month
        for ($day = 1; $day <= Carbon::now()->daysInMonth; $day++) {
            // Create a Carbon instance for the current date
            $date = Carbon::createFromDate($currentYear, $currentMonth, $day);

            // Format the date as "Month Day, Year" (e.g., "April 24, 2024")
            $formattedDate = $date->isoFormat('MMMM D, YYYY');

            // Get all rows with the current date in the created_at column
            $rowsWithDate = $rowsThisMonth->where('created_at', '>=', $date->startOfDay())
                ->where('created_at', '<=', $date->endOfDay());

            // Calculate total sales for the current date
            $totalSales = $rowsWithDate->sum('total');
            $subTotalSales = $rowsWithDate->sum('sub_total');

            // Store the total sales in the array with the formatted date as the key
            $dailySalesFormatted[$formattedDate] = $totalSales;
            $dailySales[$date->day] = $totalSales;

            // Store the daily total sales
            $dailyTotalSales[$formattedDate] = $rowsWithDate->sum('total');
            $dailySubTotalSales[$formattedDate] = $subTotalSales;
        }

        // Fetch data for chart
        $chartLabels = [];
        $chartDataValues = [];

        $monthlyData = $rowsThisYear->groupBy(function ($item) {
            return $item->created_at->format('F Y'); // Group by Month Year
        });

        foreach ($monthlyData as $month => $items) {
            $totalSum = $items->sum('total'); // Sum of 'total' column
            $chartLabels[] = $month; // Month Year
            $chartDataValues[] = $totalSum; // Sum of 'total' column
        }

        // Prepare data for Polar Chart
        $polarLabels = ['Morning', 'Afternoon', 'Evening'];
        $polarData = [$morningAverage, $afternoonAverage, $eveningAverage];

        // Pass data to your view
        return view('backoffice/admin_dashboard', [
            'sales' => $dailySalesFormatted,
            'dailySales' => $dailySales,
            'dailyTotalSales' => $dailyTotalSales,
            'dailySubTotalSales' => $dailySubTotalSales,
            'gross_sales' => $gross_sales,
            'net_sales' => $net_sales,
            'date_today' => $dateTodayFormatted,
            'salesToday' => $salesToday,
            'totalItemSoldThisMonth' => $itemSoldThisMonth,
            'chartLabels' => $chartLabels,
            'chartData' => $chartDataValues,
            'polarLabels' => $polarLabels,
            'polarData' => $polarData,
            'netSalesToday' => $net_sales_today
        ]);
    }
    public function getMonthlySales(Request $request)
    {
        // Your logic to fetch monthly sales based on the selected date
        $selectedDate = $request->input('selectedDate');

        $parsedDate = date('n', strtotime($selectedDate));

        // For demonstration purposes, let's assume you have a method to get monthly sales from your database
        $currentYear = Carbon::now()->year;
        $currentMonth = $parsedDate;

        $dailySalesFormatted = [];

        for ($day = 1; $day <= Carbon::now()->daysInMonth; $day++) {
            // Create a Carbon instance for the current date
            $date = Carbon::createFromDate($currentYear, $currentMonth, $day);

            // Format the date as "Month Day, Year" (e.g., "April 24, 2024")
            $formattedDate = $date->isoFormat('MMMM D, YYYY');

            // Get all rows with the current date in the created_at column
            $rowsWithDate = History::whereDate('created_at', $date->toDateString())->get();

            $rowsThisMonth = History::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->get();

            // Calculate total sales for the current date
            $totalSales = $rowsWithDate->sum('total');

            $gross_sales = $rowsThisMonth->sum('total');
            $net_sales = $rowsThisMonth->sum('sub_total');

            // Store the total sales in the array with the formatted date as the key
            $dailySalesFormatted[$formattedDate] = $totalSales;
        }

        // Return the monthly sales as response
        return response()->json([
            'daily_sales' => $dailySalesFormatted,
            'gross_sales' => $gross_sales,
            'net_sales' => $net_sales,
        ]);
    }

    public function inventory()
    {
        $data = Stocks::paginate(10);
        $cost = Stocks::sum('cost');
        $retail = Stocks::sum('retail');
        $quantity = Stocks::sum('quantity');
        $total_cost = $cost * $quantity;
        $total_retail = $retail * $quantity;
        $profit = $total_retail - $total_cost;

        // Calculate margin percentage
        if ($total_retail != 0) {
            $margin_percentage = ($profit / $total_retail) * 100;
        } else {
            // Handle division by zero or cases where total_retail is zero
            $margin_percentage = 0;
        }

        $margin_percentage = number_format($margin_percentage, 2);
        return view('backoffice/inventory/inventory', [
            'items' => $data,
            'total_cost' => $total_cost,
            'total_retail' => $total_retail,
            'profit' => $profit,
            'margin' => $margin_percentage,
        ]);
    }

    // public function inventory(){
    //     $data = Stocks::paginate(10);
    //     $cost = Stocks::sum('cost');
    //     $retail = Stocks::sum('retail');
    //     $quantity = Stocks::sum('quantity');
    //     $total_cost = $cost * $quantity;
    //     $total_retail = $retail * $quantity; // Calculate total retail value correctly
    //     return view('backoffice/inventory/inventory', [
    //         'items' => $data,
    //         'total_cost' => $total_cost,
    //         'total_retail' => $total_retail
    //     ]);
    // }

    public function purchasedDate(Request $request)
    {
        $date = $request->input('date');
        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $date)->paginate(10);

        return view('backoffice/sales_history', ['history' => $history]);
    }

    public function stocksAdjustment()
    {
        // Get all items from the Stocks model
        $stockItems = Stocks::all();

        // Get the distinct item names from the Stocks model
        $stockItemNames = $stockItems->pluck('item')->toArray();

        // Find the latest occurrence for each item in the SupplierOrder model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item')
            ->keyBy('item'); // Key by item name for easy lookup

        // Pass both stocks and the latest matching orders to the view
        return view('backoffice/inventory/stocks_adjustment', [
            'items' => $stockItems,
            'latestOrders' => $latestMatchingOrders,
        ]);
    }


    public function filterItems(Request $request)
{
    try {
        $query = Stocks::query()
            ->leftJoin('supplier_orders', 'stocks.item', '=', 'supplier_orders.item')
            ->select('stocks.*', 'supplier_orders.expiration_date');

        // Apply filters only if they are present
        if ($request->filled('color')) {
            $query->where('stocks.color', $request->color); // Specify the table name
        }

        if ($request->filled('size')) {
            $size = $request->size;
            switch ($size) {
                case 'less_200':
                    $query->where('stocks.size', '<', 200); // Specify the table name
                    break;
                case '200_400':
                    $query->whereBetween('stocks.size', [200, 400]); // Specify the table name
                    break;
                case '400_1000':
                    $query->whereBetween('stocks.size', [400, 1000]); // Specify the table name
                    break;
                case '1000_2000':
                    $query->whereBetween('stocks.size', [1000, 2000]); // Fixed the range
                    break;
            }
        }

        if ($request->filled('quantity')) {
            $quantity = $request->quantity;
            switch ($quantity) {
                case 'less_50':
                    $query->where('stocks.quantity', '<', 50); // Specify the table name
                    break;
                case 'less_100':
                    $query->where('stocks.quantity', '<', 100); // Specify the table name
                    break;
            }
        }

        if ($request->filled('category')) {
            $query->where('stocks.category', $request->category); // Specify the table name
        }

        if ($request->filled('price_from') && $request->filled('price_to')) {
            $query->whereBetween('stocks.retail', [$request->price_from, $request->price_to]); // Specify the table name
        }

        if ($request->filled('expiration_date')) {
            $expirationFilter = $request->expiration_date;
            switch ($expirationFilter) {
                case 'less_15':
                    $query->where('supplier_orders.expiration_date', '<=', now()->addDays(15));
                    break;
                case 'less_60':
                    $query->where('supplier_orders.expiration_date', '<=', now()->addDays(60));
                    break;
            }
        }

        // Get all filtered items
        $filteredItems = $query->get();

        $stockItemNames = $filteredItems->pluck('item')->toArray();

        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item') // Keep only the latest unique items
            ->keyBy('item'); // Key by item name for easy lookup

        // Combine stocks and latest matching orders
        $combinedResults = $filteredItems->map(function ($stock) use ($latestMatchingOrders) {
            // Add latest order details if available
            $latestOrder = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
            $stock->setAttribute('latest_order', $latestOrder); // Use setAttribute to safely add the property
            return $stock;
        });

        return response()->json($combinedResults);
    } catch (\Exception $e) {
        Log::error('Error filtering items: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}




    public function itemsList()
    {
        $data = Stocks::paginate(10); // Paginate based on the selected number of rows
        $pendingCount = PendingItems::all()->count();
        return view('backoffice/items/items_list', ['items' => $data, 'pending' => $pendingCount]);
    }

    public function createItem()
    {
        $suppliers = Supplier::all();
        return view('backoffice/items/create_item', [
            'suppliers' => $suppliers,
        ]
        );
    }

    public function addItem(Request $request)
    {
        // dd($request);
        $quantity = 1;
        $stocks = Stocks::all();
        $profit = 0;
        $cost = $request->input('cost');
        $retail = $request->input('retail');
        $profit = $retail - $cost;
        $size = $request->input('size');
        $legend = $request->input('size_legend');

        // Handle the image upload
        if ($request->hasFile('item_image')) {
            $image = $request->file('item_image');
            $imageName = $request->input('item_name') . '.' . $image->extension();
            $image->move(public_path('images/items'), $imageName);
        } else {
            $imageName = null;
        }

        $data = [
            'item' => $request->input('item_name'),
            'category' => $request->input('category'),
            'supplier' => $request->input('supplier'),
            'product_unit' => $request->input('product_unit'),
            'color' => $request->input('color'),
            'size' => $size . ' ' . $legend,
            'barcode' => $request->input('barcode'),
            'quantity' => $quantity,
            'cost' => $request->input('cost'),
            'retail' => $request->input('retail'),
            'profit' => $profit,
        ];

        $add = Stocks::create($data);

        if ($add) {
            return redirect()->intended(route('office.create_item', ['items' => $stocks]));
        }
    }

    public function viewItem($sku)
    {
        $item = Stocks::where('id', $sku)->first();
        $suppliers = Supplier::all();

        return view('backoffice/items/item_details', ['item' => $item, 'suppliers' => $suppliers]);
    }

    public function updateItem(Request $request)
    {
        $stocks = Stocks::all();
        $barcode = $request->input('barcode');
        $item = Stocks::where('barcode', $barcode)->first();
        $cost = $request->input('cost');
        $retail = $request->input('retail');
        if ($item) {
            $item->item = $request->input('item_name');
            $item->category = $request->input('category');
            $item->supplier = $request->input('supplier');
            $item->product_unit = $request->input('product_unit');
            $item->barcode = $request->input('barcode');
            $item->cost = $request->input('cost');
            $item->retail = $request->input('retail');

            $item->save();
            return redirect()->route('office.items_list')->with('items', $stocks);
        } else {
            return redirect()->route('office.items_list')->with('items', $stocks);
        }
    }

    public function updateStocks(Request $request)
    {
        $data = Stocks::where('item', $request->input('item'))->first();
        $option = $request->input('option');
        $quantity = $request->input('quantity');
        $current_stock = $data->quantity;
        if ($data) {
            $new_stock = $current_stock + $quantity;
            $data->quantity = $new_stock;
            $data->update_reason = 'New stocks';
            $data->save();
        }
        $stocks = Stocks::paginate(5);
        return redirect()->route('office.stocks_adjustment')->with('item', $stocks);
    }

public function salesByItem()
{
    // Get the current month as a Carbon instance
    $currentDate = Carbon::now();

    // Query for top items
    $topItems = Ticket::select('food_name', DB::raw('COUNT(*) AS occurrence'))
        ->whereMonth('created_at', '=', $currentDate->month) // Use the current month
        ->groupBy('food_name')
        ->orderByDesc('occurrence')
        ->limit(10)
        ->get();

    // Query for least items
    $leastItems = Ticket::select('food_name', DB::raw('COUNT(*) AS occurrence'))
        ->whereMonth('created_at', '=', $currentDate->month) // Use the current month
        ->groupBy('food_name')
        ->orderBy('occurrence', 'asc')
        ->limit(10)
        ->get();

    // Query for items from orders with stock details
    $result = DB::table('orders')
        ->select('orders.food_name', DB::raw('COUNT(orders.food_name) as occurrence'), 'stocks.cost', 'stocks.retail')
        ->join('stocks', 'orders.food_name', '=', 'stocks.item')
        ->groupBy('orders.food_name', 'stocks.cost', 'stocks.retail')
        ->paginate(10);

    // Get distinct food names and their associated tickets
    $distinctNames = Ticket::distinct()->pluck('food_name');
    $ticketsByFoodName = [];

    foreach ($distinctNames as $name) {
        $ticketsForName = Ticket::where('food_name', $name)->get();
        $ticketsByFoodName[$name] = $ticketsForName;
    }

    // Format the month for display, e.g., 'Oct' for October
    $monthNow = $currentDate->format('M');

    return view('backoffice/sales_by_items', [
        'topItems' => $topItems,
        'leastItems' => $leastItems,
        'items' => $result,
        'ticketsByFoodName' => $ticketsByFoodName,
        'monthNow' => $monthNow, // Pass the formatted month to the view
    ]);
}


    public function salesHistory()
    {
        // Get today's date
        $today = Carbon::today();

        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $today)->get();
        $gcash_transactions = GCash::whereDate('created_at', $today)->get();

        return view('backoffice/sales_history', ['history' => $history, 'gcash' => $gcash_transactions]);
    }

    public function historyTicket(Request $request, $ticket)
    {
        $sanitizedTicket = str_replace('1-', '', $ticket);

        // Query the database with the sanitized ticket
        $orders = Ticket::where('ticket', $sanitizedTicket)->get();

        $foodQuantities = [];

        // Loop through each order
        foreach ($orders as $order) {
            // Get the food_name for this order
            $foodName = $order->food_name;

            // Increment the quantity of this food name in the associative array
            if (isset($foodQuantities[$foodName])) {
                $foodQuantities[$foodName]++;
            } else {
                $foodQuantities[$foodName] = 1;
            }
        }

        $foodPrices = [];

        // Loop through each unique food name
        foreach ($foodQuantities as $foodName => $quantity) {
            // Query the Menu model to get the price of this food
            $foodPrice = Stocks::where('item', $foodName)->value('retail');

            // Add the food name, its price, and quantity to the foodPrices array
            $foodPrices[] = [
                'food_name' => $foodName,
                'price' => $foodPrice,
                'quantity' => $quantity,
            ];
        }

        // Query the database for other ticket details
        $result = History::where('ticket', $sanitizedTicket)->get();

        $data = [
            'result' => $result,
            'food_prices' => $foodPrices,
        ];

        // Return the combined data as JSON
        return response()->json($data);
    }

    public function itemName($item_name)
    {
        // Retrieve the current year and month
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Retrieve the item name
        $item = $item_name;

        // Get the counts of the specified item sold on the same day
        $ticketCounts = DB::table('orders')
            ->join('tbl_history', 'orders.ticket', '=', 'tbl_history.ticket')
            ->where('orders.food_name', $item)
            ->whereYear('tbl_history.created_at', $currentYear)
            ->whereMonth('tbl_history.created_at', $currentMonth)
            ->select(DB::raw("DATE_FORMAT(tbl_history.created_at, '%M %d %Y') as date"), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->get();

        // dd($ticketCounts);

        // Create an array to store the summed counts for each date in the month
        $summedCounts = [];

        // Create a DatePeriod object to loop through all the days in the current month
        $startDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $datePeriod = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);

        // Loop through all the items in $ticketCounts
        foreach ($ticketCounts as $ticket) {
            // Retrieve the date and count for each item
            $formattedDate = $ticket->date;
            $count = $ticket->count;

            // Store the count for the corresponding date in the summedCounts array
            $summedCounts[$formattedDate] = $count;
        }

        // Return the summed counts for each date in the month as JSON response
        return response()->json([
            'summed_counts' => $summedCounts,
            'month' => $currentMonth,
        ]);
    }

    public function qrPrinting()
    {
        $items = Stocks::where('category', 'Meals')->get();
        return view('backoffice/qr', ['menus' => $items]);
    }

    public function qrToPrint(Request $request)
    {
        // dd($request->input('item'));
        $item = $request->input('item');
        $retail = $request->input('retail');
        return view('backoffice/qr_for_printing', [
            'item' => $item,
            'retail' => $retail,
        ]);
    }

    public function adjustStocks($item_name)
    {
        $item = Stocks::where('item', $item_name)->first();
        return view('backoffice/inventory/adjust', ['item' => $item]);
    }

    public function cashiers()
    {
        $cashiers = Authentication::where('role', 'Cashier')->get();
        $pending = PendingAccount::all();
        return view('backoffice/cashiers/cashiers', ['cashiers' => $cashiers, 'pendings' => $pending]);
    }

    public function suppliers()
    {
        $supplier = Supplier::all();
        return view('backoffice/suppliers/suppliers', [
            'suppliers' => $supplier,
        ]);
    }

    public function addSuppliers(Request $request)
    {
        // dd($request);
        $supplier = Supplier::all();
        $data = [
            'name' => $request->input('supplier_name'),
            'contact_person' => $request->input('contact_person'),
            'contact_number' => $request->input('contact_number'),
            'address' => $request->input('address'),
        ];

        $add = Supplier::create($data);

        if ($add) {
            return redirect()->intended(route('office.supplier', ['suppliers' => $supplier]));
        }
    }

    public function ordering()
    {
        // Get all orders
        $orders = SupplierOrder::all();

        // Group orders by batch number and calculate summary data
        $summary = $orders->groupBy('batch_number')->map(function ($group) {
            return [
                'batch_number' => $group->first()->batch_number,
                'total_items' => $group->sum('quantity'),
                'total_rows' => $group->count(),
                'created_at' => Carbon::parse($group->first()->created_at)->format('F j, Y - g:i A'),
                'updated_at' => Carbon::parse($group->first()->updated_at)->format('F j, Y - g:i A'),
            ];
        });

        // Pass the summary data to the view
        return view('backoffice/ordering/order', [
            'summaries' => $summary,
        ]);
    }

    public function itemSearch($key = null)
    {
        // Check if the search key is empty
        if (empty($key)) {
            // Return an empty array if no key is provided
            return response()->json(['items' => []]);
        }

        // Perform the search query if the key is not empty
        $items = Stocks::where('item', 'LIKE', "%{$key}%")->get();

        // Return the results as a JSON response
        return response()->json(['items' => $items]);
    }

    public function supplierSearch($key = null)
    {
        // Check if the search key is empty
        if (empty($key)) {
            // Return an empty array if no key is provided
            return response()->json(['items' => []]);
        }

        // Perform the search query if the key is not empty
        $items = Supplier::where('name', 'LIKE', "%{$key}%")->get();

        // Return the results as a JSON response
        return response()->json(['items' => $items]);
    }

    public function placeOrder(Request $request)
    {
        // Get the batch number
        $batch_number = $request->input('batch_number');

        // Get the arrays of food names, suppliers, and quantities
        $food_names = $request->input('food_name');
        $suppliers = $request->input('suppliername');
        $quantities = $request->input('quantity');

        // Initialize an empty array to collect the data to be inserted
        $data = [];

        // Get the current timestamp
        $timestamp = now();

        // Loop through the arrays and collect data for each order
        for ($i = 0; $i < count($food_names); $i++) {
            $data[] = [
                'item' => $food_names[$i],
                'batch_number' => $batch_number,
                'quantity' => $quantities[$i],
                'supplier' => $suppliers[$i],
                'status' => 'Pending',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert the data into the database
        SupplierOrder::insert($data);

        // Get all orders
        $orders = SupplierOrder::all();

        // Redirect to the intended route with the orders
        return redirect()->intended(route('office.ordering', ['orders' => $orders]));
    }

    public function newOrder()
    {
        $previous_batch_number = SupplierOrder::latest()->value('batch_number');
        $new = $previous_batch_number + 1;
        $orders = SupplierOrder::where('batch_number', $previous_batch_number)->get();
        // dd($orders);
        return view('backoffice/ordering/ordering', ['newBn' => $new, 'prevBn' => $previous_batch_number, 'orders' => $orders]);
    }

    public function acceptAccount($name)
    {
        $cashier = PendingAccount::where('name', $name)->first();

        $data = [
            'name' => $cashier->name,
            'password' => $cashier->password,
            'role' => 'Cashier',
        ];

        $add_cashier = Authentication::create($data);
        if ($add_cashier) {
            PendingAccount::where('name', $name)->delete();
        }

        return redirect()->back();
    }

    public function getSalesPerMonth($date)
    {
        $datePicked = $date;
        $formattedDate = Carbon::parse($datePicked)->format('F d, Y');
        $sales = History::whereDate('created_at', $date)->get();
        return response()->json(['sales' => $sales, 'datePicked' => $formattedDate]);
    }

    public function itemListSearch($key = null)
    {
        if (empty($key)) {
            // Return all items if the search key is empty
            $menus = Stocks::all();
        } else {
            // Perform the search query
            $menus = Stocks::where('item', 'LIKE', "%{$key}%")->get();
        }

        // Return the results as a JSON response
        return response()->json(['menus' => $menus]);
    }

    public function filterSupplierAddress(Request $request)
    {
        // Validate the input to allow only permitted parameters
        $validatedData = $request->validate([
            'address' => 'required|string|max:255', // Example validation rule
        ]);

        // Use the validated address to search for suppliers
        $address = $validatedData['address'];
        $suppliers = Supplier::where('address', 'like', '%' . $address . '%')->get();

        // Return the matching suppliers as a JSON response
        return response()->json($suppliers);
    }

    public function supplierLiveSearch($key)
    {

    }

    public function pendingItems(){
        $items = PendingItems::all();
        return view('backoffice/items/pending_items', [
            'items' => $items
        ]);
    }

    public function acceptAddedItem($id){
        $stocks = Stocks::paginate(8);
        $item = PendingItems::find($id);
        if(!$item) {
            // Handle the case where the item is not found
            return redirect()->back()->with('error', 'Item not found.');
        }

        $data = [
            'item' => $item->item,
            'category' => $item->category,
            'supplier' => $item->supplier,
            'product_unit' => $item->product_unit,
            'color' => $item->color,
            'size' => $item->size,
            'barcode' => $item->barcode,
            'quantity' => 0,
            'cost' => $item->cost,
            'retail' => $item->retail,
        ];

        $add = Stocks::create($data);

        if($add){
            $item->delete();
            return redirect()->route('office.items_list', [
                'items' => $stocks
            ]);
        } else {
            return redirect()->back()->with('error', 'Failed to add item to stocks.');
        }
    }

    public function fetchBatchDetails(Request $request)
    {
        // Get the batch number from the AJAX request
        $batchNumber = $request->input('batch_number');

        // Fetch the order details based on the batch number
        $orderDetails = SupplierOrder::where('batch_number', $batchNumber)->get();

        // Return the view with the order details
        return response()->json([
            'orderDetails' => $orderDetails
        ]);
    }

    public function resignCashier($id){
        $cashiers = Authentication::where('role', 'Cashier')->get();
        $pending = PendingAccount::all();
        $delete = Authentication::where('id', $id)->delete();
        if ($delete) {
            // Redirect to the cashiers page with a success message
            return redirect()->route('office.cashiers')->with('success', 'Cashier removed successfully!');
        } else {
            // Optionally handle the case where deletion fails
            return redirect()->route('office.cashiers')->with('error', 'Failed to remove cashier.');
        }
    }

    public function nameOrder() {
        // Fetch stocks ordered by item name
        $stocks = Stocks::orderBy('item', 'asc')->get();
        
        // Get item names from the fetched stocks
        $stockItemNames = $stocks->pluck('item')->toArray();
    
        // Find the latest occurrence for each item in the SupplierOrder model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item') // Keep only the latest unique items
            ->keyBy('item'); // Key by item name for easy lookup
    
        // Combine stocks and latest matching orders
        $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
            // Add latest order details if available
            $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
            return $stock;
        });
    
        // Return combined results as JSON
        return response()->json($combinedResults);
    }
    
    

    public function defaultOrder() {
        // Fetch stocks ordered by item name
        $stocks = Stocks::orderBy('id', 'asc')->get();
        
        // Get item names from the fetched stocks
        $stockItemNames = $stocks->pluck('item')->toArray();
    
        // Find the latest occurrence for each item in the SupplierOrder model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item') // Keep only the latest unique items
            ->keyBy('item'); // Key by item name for easy lookup
    
        // Combine stocks and latest matching orders
        $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
            // Add latest order details if available
            $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
            return $stock;
        });
    
        // Return combined results as JSON
        return response()->json($combinedResults);
    }

    public function stockOrder(){
        $stocks = Stocks::orderByRaw('CAST(quantity AS UNSIGNED) ASC')->get();
        // Get item names from the fetched stocks
        $stockItemNames = $stocks->pluck('item')->toArray();
    
        // Find the latest occurrence for each item in the SupplierOrder model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item') // Keep only the latest unique items
            ->keyBy('item'); // Key by item name for easy lookup
    
        // Combine stocks and latest matching orders
        $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
            // Add latest order details if available
            $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
            return $stock;
        });
    
        // Return combined results as JSON
        return response()->json($combinedResults);
    }

    public function defaultStockOrder(){
        $stocks = Stocks::orderByRaw('CAST(quantity AS UNSIGNED) DESC')->get();
        // Get item names from the fetched stocks
        $stockItemNames = $stocks->pluck('item')->toArray();
    
        // Find the latest occurrence for each item in the SupplierOrder model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item') // Keep only the latest unique items
            ->keyBy('item'); // Key by item name for easy lookup
    
        // Combine stocks and latest matching orders
        $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
            // Add latest order details if available
            $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
            return $stock;
        });
    
        // Return combined results as JSON
        return response()->json($combinedResults);
    }

    public function expirationDate(){
        // Get all distinct item names from the Stocks model
        $stockItems = Stocks::pluck('item')->toArray();

        // Find SupplierOrders that have an item matching any item in the Stocks model
        $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItems)
            ->orderBy('created_at', 'desc') // Ensure this orders by the latest created record
            ->get()
            ->unique('item'); // Get only the latest occurrence per item

        dd($latestMatchingOrders);
        // Display the matching rows
        foreach ($matchingOrders as $order) {
            echo 'Order ID: ' . $order->id . ' - Item: ' . $order->item . '<br>';
        }
    }

    public function itemSearchV2($key = null){
        if (empty($key)) {
            // Return all items if the search key is empty
            $stocks = Stocks::all();
            $stockItemNames = $stocks->pluck('item')->toArray();
    
            // Find the latest occurrence for each item in the SupplierOrder model
            $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
                ->get()
                ->unique('item') // Keep only the latest unique items
                ->keyBy('item'); // Key by item name for easy lookup
        
            // Combine stocks and latest matching orders
            $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
                // Add latest order details if available
                $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
                return $stock;
            });
            // Return the results as a JSON response
            return response()->json($combinedResults);
        } else {
            // Perform the search query
            $stocks = Stocks::where('item', 'LIKE', "%{$key}%")->get();$stockItemNames = $stocks->pluck('item')->toArray();
    
            // Find the latest occurrence for each item in the SupplierOrder model
            $latestMatchingOrders = SupplierOrder::whereIn('item', $stockItemNames)
                ->get()
                ->unique('item') // Keep only the latest unique items
                ->keyBy('item'); // Key by item name for easy lookup
        
            // Combine stocks and latest matching orders
            $combinedResults = $stocks->map(function ($stock) use ($latestMatchingOrders) {
                // Add latest order details if available
                $stock->latest_order = $latestMatchingOrders->get($stock->item, null); // Default to null if not found
                return $stock;
            });
            // Return the results as a JSON response
            return response()->json($combinedResults);
        }
    }

    public function addMultiple(Request $request)
    {
        $stocks = Stocks::paginate(8);
        // Retrieve the selected items from the form as an array
        $selectedItems = explode(',', $request->input('selected_items'));

        // Query all items from the PendingItems model that match the selected IDs
        $items = PendingItems::whereIn('item', $selectedItems)->get();

        foreach ($items as $item) {
            $data = [
                'item' => $item->item,
                'category' => $item->category,
                'supplier' => $item->supplier,
                'product_unit' => $item->product_unit,
                'color' => $item->color,
                'size' => $item->size,
                'barcode' => $item->barcode,
                'quantity' => 0,
                'cost' => $item->cost,
                'retail' => $item->retail,
            ];

            // Create a new stock entry for each item
            $add = Stocks::create($data);

            // Delete the item from PendingItems if added successfully
            if ($add) {
                $item->delete();
            }
        }

        return redirect()->route('office.items_list', [
            'items' => $stocks
        ])->with('success', 'Items successfully added to stocks.');
    }

    public function deleteItem($id){
        $stocks = Stocks::paginate(8);
        $item = PendingItems::find($id);

        $item->delete();

        return redirect()->route('office.items_list', [
            'items' => $stocks
        ])->with('success', 'Items successfully deleted.');
    }

    public function printReport($month)
    {
        $month = (int) $month;
        $dateToday = Carbon::today();
        $startOfMonth = Carbon::createFromDate(null, $month, 1)->startOfMonth(); // First day of the provided month
        $endOfMonth = Carbon::createFromDate(null, $month, 1)->endOfMonth(); // Last day of the provided month   
        
        $dates = [];
        
        // Iterate through each day of the month
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            
            // Calculate the sum of the 'total' column for the specific day
            $totalSales = History::whereDate('created_at', $formattedDate)->sum('total');
            
            // Get all unique ticket numbers for the specific day
            $tickets = History::whereDate('created_at', $formattedDate)
                ->pluck('ticket')
                ->unique()
                ->toArray();
            
            // Initialize an array to hold food names and their costs/retail values
            $foodDetails = [];
            $totalCost = 0;
            $totalRetail = 0;

            // Use ticket numbers to get food names from the Ticket model
            if (!empty($tickets)) {
                // Retrieve food names along with their counts
                $foodCounts = Ticket::whereIn('ticket', $tickets)
                    ->select('food_name', DB::raw('count(*) as count')) // Use DB facade
                    ->groupBy('food_name')
                    ->get();

                foreach ($foodCounts as $foodCount) {
                    $foodName = $foodCount->food_name;
                    $count = $foodCount->count;

                    $stockItem = Stocks::where('item', $foodName)->first(['cost', 'retail']);
                    if ($stockItem) {
                        // Multiply cost and retail by the occurrence count
                        $costTotal = $stockItem->cost * $count;
                        $retailTotal = $stockItem->retail * $count;

                        $foodDetails[] = [
                            'food_name' => $foodName,
                            'cost' => $costTotal, // Total cost for this item
                            'retail' => $retailTotal, // Total retail for this item
                            'occurrences' => $count, // Add the occurrence count
                        ];
                        // Add to overall total cost and retail
                        $totalCost += $costTotal;
                        $totalRetail += $retailTotal;
                    } else {
                        // If not found, you can set cost and retail to null or any default value
                        $foodDetails[] = [
                            'food_name' => $foodName,
                            'cost' => null,
                            'retail' => null,
                            'occurrences' => $count, // Add the occurrence count
                        ];
                    }
                }
            }

            // Add the date, total sales, tickets, food details, and totals to the array
            $dates[] = [
                'date' => $formattedDate,
                'total' => $totalSales > 0 ? $totalSales : 0, // Set to 0 if there are no sales
                'tickets' => $tickets, // Use 'tickets' instead of 'ticket_numbers'
                'food_details' => $foodDetails, // Add food details (cost and retail)
                'total_cost' => $totalCost, // Total cost for the day
                'total_retail' => $totalRetail, // Total retail for the day
                'monthStart' => $startOfMonth->format('M d, Y'),
                'monthEnd' => $endOfMonth->format('M d, Y'),
                'dateToday' => $dateToday->format('M d, Y'),
            ];
        }

        return response()->json($dates); // Return the data as JSON or use as needed
    }

    public function generatePDF($month)
    {
        // Ensure the month is provided in numeric format (1-12)
        $month = (int) $month;
        $dateToday = Carbon::today();
        $startOfMonth = Carbon::createFromDate(null, $month, 1)->startOfMonth(); // First day of the provided month
        $endOfMonth = Carbon::createFromDate(null, $month, 1)->endOfMonth(); // Last day of the provided month    

        $dates = [];
        
        // Iterate through each day of the month
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            
            // Calculate the sum of the 'total' column for the specific day
            $totalSales = History::whereDate('created_at', $formattedDate)->sum('total');
            
            // Get all unique ticket numbers for the specific day
            $tickets = History::whereDate('created_at', $formattedDate)
                ->pluck('ticket')
                ->unique()
                ->toArray();
            
            // Initialize an array to hold food names and their costs/retail values
            $foodDetails = [];
            $totalCost = 0;
            $totalRetail = 0;

            // Use ticket numbers to get food names from the Ticket model
            if (!empty($tickets)) {
                // Retrieve food names along with their counts
                $foodCounts = Ticket::whereIn('ticket', $tickets)
                    ->select('food_name', DB::raw('count(*) as count')) // Use DB facade
                    ->groupBy('food_name')
                    ->get();

                foreach ($foodCounts as $foodCount) {
                    $foodName = $foodCount->food_name;
                    $count = $foodCount->count;

                    $stockItem = Stocks::where('item', $foodName)->first(['cost', 'retail']);
                    if ($stockItem) {
                        // Multiply cost and retail by the occurrence count
                        $costTotal = $stockItem->cost * $count;
                        $retailTotal = $stockItem->retail * $count;

                        $foodDetails[] = [
                            'food_name' => $foodName,
                            'cost' => $costTotal, // Total cost for this item
                            'retail' => $retailTotal, // Total retail for this item
                            'occurrences' => $count, // Add the occurrence count
                        ];
                        // Add to overall total cost and retail
                        $totalCost += $costTotal;
                        $totalRetail += $retailTotal;
                    } else {
                        // If not found, you can set cost and retail to null or any default value
                        $foodDetails[] = [
                            'food_name' => $foodName,
                            'cost' => null,
                            'retail' => null,
                            'occurrences' => $count, // Add the occurrence count
                        ];
                    }
                }
            }

            // Add the date, total sales, tickets, food details, and totals to the array
            $dates[] = [
                'date' => $formattedDate,
                'total' => $totalSales > 0 ? $totalSales : 0, // Set to 0 if there are no sales
                'tickets' => $tickets, // Use 'tickets' instead of 'ticket_numbers'
                'food_details' => $foodDetails, // Add food details (cost and retail)
                'total_cost' => $totalCost, // Total cost for the day
                'total_retail' => $totalRetail, // Total retail for the day
                'monthStart' => $startOfMonth->format('M d, Y'),
                'monthEnd' => $endOfMonth->format('M d, Y'),
                'dateToday' => $dateToday->format('M d, Y'),
                'reportMonth' => $startOfMonth->format('M Y'),
            ];
        }

        // Load a view and pass the data to it
        $pdf = Pdf::loadView('pdf', ['dates' => $dates]);

        // Return the PDF for download or as a response
        return $pdf->download('report.pdf');
        // Alternatively, to display it in the browser:
        // return $pdf->stream('report.pdf');
    }

    public function removeCashier($id)
    {
        try {
            // Find the account by ID
            $pendingAccount = PendingAccount::findOrFail($id);

            // Delete the account
            $pendingAccount->delete();

            return response()->json(['message' => 'Account removed successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Account not found.'], 404);
        } catch (\Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error removing cashier: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

}
