<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Authentication;
use App\Models\History;
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
        $data = Stocks::paginate(8);
        return view('backoffice/inventory/stocks_adjustment', ['item' => $data]);
    }

    public function filterItems(Request $request)
    {
        try {
            $query = Stocks::query();

            if ($request->filled('color')) {
                $query->where('color', $request->color);
            }

            if ($request->filled('size')) {
                $size = $request->size;
                switch ($size) {
                    case 'less_200':
                        $query->where('size', '<', 200);
                        break;
                    case '200_400':
                        $query->whereBetween('size', [200, 400]);
                        break;
                    case '400_1000':
                        $query->whereBetween('size', [400, 1000]);
                        break;
                    case '1000_2000':
                        $query->whereBetween('size', [1000, 2000]);
                        break;
                }
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('price_from') && $request->filled('price_to')) {
                $query->whereBetween('retail', [$request->price_from, $request->price_to]);
            }

            $filteredItems = $query->get('item');

            $itemNames = $filteredItems->pluck('item')->toArray();
            $filterResult = Stocks::whereIn('item', $itemNames)->get();

            return response()->json($filterResult);
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
        $quantity = 0;
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
            if ($option == 'increase') {
                $new_stock = $current_stock + $quantity;
                $data->quantity = $new_stock;
                $data->update_reason = $request->input('reason');
                $data->save();
            } elseif ($option == 'decrease') {
                $new_stock = $current_stock - $quantity;
                $data->quantity = $new_stock;
                $data->update_reason = $request->input('reason');
                $data->save();
            }
        }
        $stocks = Stocks::paginate(10);
        return view('backoffice/inventory/stocks_adjustment', ['item' => $stocks]);
    }

    public function salesByItem()
    {
        $topItems = Ticket::select('food_name', DB::raw('COUNT(*) AS occurrence'))
            ->whereMonth('created_at', '=', 7) // Filter for June
            ->groupBy('food_name')
            ->orderByDesc('occurrence')
            ->limit(10)
            ->get();

        $leastItems = Ticket::select('food_name', DB::raw('COUNT(*) AS occurrence'))
            ->whereMonth('created_at', '=', 7) // Filter for June
            ->groupBy('food_name')
            ->orderBy('occurrence', 'asc')
            ->limit(10)
            ->get();

        $result = DB::table('orders')
            ->select('orders.food_name', DB::raw('COUNT(orders.food_name) as occurrence'), 'stocks.cost', 'stocks.retail')
            ->join('stocks', 'orders.food_name', '=', 'stocks.item')
            ->groupBy('orders.food_name', 'stocks.cost', 'stocks.retail')
            ->paginate(10);

        // dd($result);

        // Get distinct food names and their associated tickets
        $distinctNames = Ticket::distinct()->pluck('food_name');
        $ticketsByFoodName = [];

        foreach ($distinctNames as $name) {
            $ticketsForName = Ticket::where('food_name', $name)->get();
            $ticketsByFoodName[$name] = $ticketsForName;
        }

        return view('backoffice/sales_by_items', [
            'topItems' => $topItems,
            'leastItems' => $leastItems,
            'items' => $result,
            'ticketsByFoodName' => $ticketsByFoodName,
        ]);
    }

    public function salesHistory()
    {
        // Get today's date
        $today = Carbon::today();

        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $today)->get();

        return view('backoffice/sales_history', ['history' => $history]);
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


}
