<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Authentication;
use App\Models\History;
use App\Models\Stocks;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OfficeController extends Controller
{
    public function adminLogin(){
        return view('backoffice/admin_login');
    }

    public function login(Request $request){
        $admin_id = 'Administrator';
        $admin_password = 'administrator1';
        $id = $request->input('name');
        $pass = $request->input('password');
        if($admin_id == $id){
            if($admin_password == $pass){
                $currentYear = Carbon::now()->year;
                $currentMonth = Carbon::now()->month;
            
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
                    'net_sales' => $net_sales
                ]);
            } else {
                return redirect()->route('office.login')->with('error', 'The password is incorrect!');
            }
        } else if($id == '' && $pass == '') {
            return redirect()->route('office.login')->with('error', 'Both fields are empty!');
        } else if($admin_id != $id && $pass != $admin_password) {
            return redirect()->route('office.login')->with('error', 'Both fields are wrong!');
        } else if($id != $admin_id && $pass == $admin_password) {
            return redirect()->route('office.login')->with('error', 'The admin id you entered is wrong!');
        }
    }

    public function backToQr(){
        $items = Stocks::all();
        return view('backoffice/qr', ['menus' => $items]);
    }

    public function backToItems(){
        $data = Stocks::paginate(10); // Paginate based on the selected number of rows
        return view('backoffice/items/items_list', ['items' => $data]);
    }

    public function exportExcel(Request $request) {
        $currentYear = Carbon::now()->year;
        $currentMonth = 5; // Adjust as needed, or pass via request parameters
    
        return Excel::download(new SalesReportExport($currentYear, $currentMonth), 'sales_report.xlsx');
    }

    public function dashboard(Request $request){
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
    
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
            'net_sales' => $net_sales
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

    public function inventory(){
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
            'margin' => $margin_percentage
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
    

    public function stocksAdjustment(){
        $data = Stocks::paginate(8);
        return view('backoffice/inventory/stocks_adjustment', ['item' => $data]);
    }

    public function itemsList(){
        $data = Stocks::paginate(10); // Paginate based on the selected number of rows
        return view('backoffice/items/items_list', ['items' => $data]);
    }
    
    public function createItem(){
        return view('backoffice/items/create_item');
    }

    public function addItem(Request $request){
        // dd($request);
        $quantity = 0;
        $stocks = Stocks::all();
        $profit = 0;
        $cost = $request->input('cost');
        $retail = $request->input('retail');
        $profit = $retail - $cost;

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
            'description' => $request->input('description'),
            'sku' => $request->input('item_sku'),
            'qr' => $request->input('item_qr'),
            'quantity' => $quantity,
            'cost' => $request->input('cost'),
            'retail' => $request->input('retail'),
            'profit' => $profit,
        ];

        $add = Stocks::create($data);

        if($add){
            return redirect()->intended(route('office.create_item', ['items' => $stocks]));
        }
    }
    
    public function viewItem($sku){
        $item = Stocks::where('sku', $sku)->first();

        return view('backoffice/items/item_details', ['item' => $item]);
    }

    public function updateItem(Request $request){
        $stocks = Stocks::all();
        $sku = $request->input('item_sku');
        $item = Stocks::where('sku', $sku)->first();
        $profit = 0;
        $cost = $request->input('cost');
        $retail = $request->input('retail');
        $profit = $retail - $cost;
        if($item){
            $item->item = $request->input('item_name');
            $item->category = $request->input('category');
            $item->description = $request->input('description');
            $item->sku = $request->input('item_sku');
            $item->qr = $request->input('item_qr');
            $item->cost = $request->input('cost');
            $item->retail = $request->input('retail');

            $item->save();
            return redirect()->route('office.items_list')->with('items', $stocks);
        } else{
            return redirect()->route('office.items_list')->with('items', $stocks);
        }
    }

    public function updateStocks(Request $request){
        $data = Stocks::where('item', $request->input('item'))->first();
        $option = $request->input('option');
        $quantity = $request->input('quantity');
        $current_stock = $data->quantity;
        if($data){
            if($option == 'increase'){
                $new_stock = $current_stock + $quantity;
                $data->quantity = $new_stock;
                $data->update_reason = $request->input('reason');
                $data->save();
            } elseif($option == 'decrease'){
                $new_stock = $current_stock - $quantity;
                $data->quantity = $new_stock;
                $data->update_reason = $request->input('reason');
                $data->save();
            }
        }
        $stocks = Stocks::paginate(10);
        return view('backoffice/inventory/stocks_adjustment', ['item' => $stocks]);
    }

    public function salesByItem(){
        $topItems = Ticket::select('food_name', DB::raw('COUNT(*) AS occurrence'))
            ->whereMonth('created_at', '=', 6) // Filter for June
            ->groupBy('food_name')
            ->orderByDesc('occurrence')
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
            'items' => $result,
            'ticketsByFoodName' => $ticketsByFoodName
        ]);
    }


    public function salesHistory(){
        // Get today's date
        $today = Carbon::today();

        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $today)->get();

        return view('backoffice/sales_history', ['history' => $history]);
    }

    public function historyTicket(Request $request, $ticket){
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
                'quantity' => $quantity
            ];
        }
    
        // Query the database for other ticket details
        $result = History::where('ticket', $sanitizedTicket)->get();
    
        $data = [
            'result' => $result,
            'food_prices' => $foodPrices
        ];
        
        // Return the combined data as JSON
        return response()->json($data);
    }

    public function itemName($item_name){
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

    public function qrPrinting(){
        $items = Stocks::where('category', 'Meals')->get();
        return view('backoffice/qr', ['menus' => $items]);
    }

    public function qrToPrint(Request $request){
        // dd($request->input('item'));
        $item = $request->input('item');
        $retail = $request->input('retail');
        return view('backoffice/qr_for_printing', [
            'item' => $item,
            'retail' => $retail
        ]);
    }

    public function adjustStocks($item_name){
        $item = Stocks::where('item', $item_name)->first();
        return view('backoffice/inventory/adjust', ['item' => $item]);
    }

    public function cashiers(){
        $cashiers = Authentication::where('role', 'Cashier')->get();
        return view('backoffice/cashiers/cashiers', ['cashiers' => $cashiers]);
    }
}
