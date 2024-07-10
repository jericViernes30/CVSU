<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\History;
use App\Models\Shift;
use App\Models\Stocks;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class POSController extends Controller
{

    public function dashboard(){
        $cashier_name = session('cashier_name');
        if(!isset($cashier_name)){
            return redirect()->route('login');
        } else {
            $started_shift = false;
            $today = Carbon::today()->toDateString();
            $menu = Menu::all();
            $items = Stocks::all();
            $stocks_alert = Stocks::where('quantity', '<=', 20)->get();
            // dd($stocks_alert);
            $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
            $ticket = $lastTicket->ticket + 1;
            $cashier_name = session('cashier_name');
            $shift = Shift::where('cashier', $cashier_name)
                            ->whereDate('created_at', $today)
                            ->get();
            if($shift->isEmpty()){
                return view('cashier');
            } else {
                return view('dashboard', ['menus' => $items, 'ticket' => $ticket, 'alerts' => $stocks_alert]);
            }
        }
    }

    public function livesearch($key = null)
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

    public function ticketDetails(Request $request){
        $menu = Menu::all();

        $customer = $request->input('customer');
        if(empty($customer)){
            $customer = 'Customer';
        }
        $ticket = $request->input('ticket');
        $data = ([
            'ticket' => $ticket,
            'name' => $customer
        ]);
        Customer::create($data);

        $foodNames = $request->input('food_name');
        foreach ($foodNames as $foodName) {
            // Create a new Food instance
            $food = new Ticket();
            
            // Set the name attribute
            $food->food_name = $foodName;

            // Set the ticket_number attribute
            $food->ticket = $ticket; // Or whatever the specific ticket number is
            
            // Save the record to the database
            $food->save();
        }

        $foods = Ticket::select('food_name')
            ->selectRaw('COUNT(*) as count')
            ->where('ticket', $ticket)
            ->groupBy('food_name')
            ->get();

        $prices = Stocks::all();

        // Create the foodPrices array
        $foodPrices = [];
        foreach ($prices as $price) {
            $foodPrices[$price->item] = $price->retail;
        }

        $time = Ticket::where('ticket', $ticket)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

        $time_formatted = Carbon::parse($time)->format('F d, Y \a\t h:ia');

        return view('order_details', [
            'ticket' => $ticket,
            'customer' => $customer,
            'foods' => $foods,
            'prices' => $foodPrices,
            'time' => $time_formatted
        ]);
    }

    public function history(){
        $cashier_name = session('cashier_name');
        if(!isset($cashier_name)){
            return redirect()->route('login');
        }
        // Get today's date
        $today = Carbon::today();

        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $today)->get();

        return view('history', ['history' => $history]);
    }

    public function sale(Request $request){
        $ticketNumber = $request->input('ticket');
        $cashier_name = session('cashier_name');
        if(!isset($cashier_name)){
            return redirect()->route('login');
        }
        $menu = Menu::all();
        $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
        $ticket = $lastTicket->ticket + 1;
        $change = $request->input('cash') - $request->input('pay');
        $quantities = [];
        $items = Ticket::select('food_name')
               ->where('ticket', $ticketNumber)
               ->groupBy('food_name')
               ->selectRaw('food_name, COUNT(*) as count')
               ->get();

        foreach ($items as $item){
            $item_sold = $item->count;
            $quantity = Stocks::where('item', $item->food_name)->first();
            $current_stock = $quantity->quantity;
            $quantities[$item->food_name] = $quantity;
            $new_stock = $current_stock - $item_sold;
            $quantity->quantity = $new_stock;
            $quantity->save();
        }

        $data = [
            'ticket' => $request->input('ticket'),
            'cashier' => $cashier_name,
            'customer' => $request->input('customer'),
            'type' => 'SALE',
            'sub_total' => $request->input('sub_total'),
            'tax' => $request->input('tax'),
            'cash' => $request->input('cash'),
            'total' => $request->input('pay'),
            'change' => $change,
        ];

        $insert = History::create($data);
        
        $shift_sales_total = History::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today()) //created at must be the date today formatted by 2024-05-17. Get only the date part of the created_at
                    ->sum('total');

        $shift = Shift::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->first();
        $starting_cash = $shift->starting_cash ?? 0;
        $sales_today = $starting_cash + $shift_sales_total;
        $shift->closing_cash = $sales_today;
        $shift->save();

        if($insert){
            return redirect()->intended(route('dashboard', ['menus' => $menu, 'ticket' => $ticket]));
        }
    }

    public function purchasedDate(Request $request){
        $date = $request->input('date');
        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $date)->paginate(10);

        return view('history', ['history' => $history]);
    }

    public function cashier(){
        $pos_number = session('pos_number');
        $cashier_name = session('cashier_name');
        $stocks_alert = Stocks::where('quantity', '<=', 20)->get();
        $shift = Shift::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->first();
        
        $shift_start = $shift->created_at;

        $date_today = Carbon::today();
        $sales_today = History::whereDate('created_at', $date_today)->get();

        $formatted_shift_start = Carbon::parse($shift_start)->format('F d, Y \- h:ia');

        $shift_sales_total = History::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today()) //created at must be the date today formatted by 2024-05-17. Get only the date part of the created_at
                    ->sum('total');
        
        $net_sales = History::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today())
                    ->sum('sub_total');

        if(is_null($shift)){
            // If no shift is found, render the cashier view
            return view('cashier');
        } else {
            // If at least one shift is found, render the cashier_menu view
            return view('cashier_menu',[
                'shift' => $shift,
                'net_sales' => $net_sales,
                'cash' => $shift_sales_total,
                'pos' => $pos_number,
                'alerts' => $stocks_alert,
                'time' => $formatted_shift_start,
                'sales' => $sales_today
            ]);
        }
    }

    public function startShift(Request $request){
        $cashier_name = session('cashier_name');
        $pos = session('pos_number');
        $stocks_alert = Stocks::where('quantity', '<=', 20)->get();
        
        $starting_cash = $request->input('starting_cash');

        $data = [
            'cashier' => $cashier_name,
            'POS_number' => $pos,
            'starting_cash' => $starting_cash,
        ];

        $data['closing_cash'] = 0;

        Shift::create($data);

        $items = Stocks::all();
        $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
        $ticket = $lastTicket->ticket + 1;
        $cashier_name = session('cashier_name');
        return view('dashboard', ['menus' => $items, 'ticket' => $ticket, 'alerts' => $stocks_alert]);
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

    public function cashManagement(Request $request){
        $cashier_name = session('cashier_name');
        $shift = Shift::where('cashier', $cashier_name)
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->first();
        $mode = $request->input('reason');
        $amount = $request->input('amount');

        if($mode == 'paid_in'){
            $paid_in = $shift->cash_in += $amount;
            $shift->cash_in = $paid_in;
            if($shift->save()){
                return redirect()->back()->with('status', 'Cash in successfully updated.'); // Redirect back with success message
            } else {
                return redirect()->back()->withErrors(['Unable to save shift data.']);
            }
        } else {
            $paid_out = $shift->cash_out += $amount;
            $shift->cash_out = $paid_out;
            if($shift->save()){
                return redirect()->back()->with('status', 'Cash out successfully updated.'); // Redirect back with success message
            } else {
                return redirect()->back()->withErrors(['Unable to save shift data.']);
            }
        }
    }

    public function inventory(){
        $data = Stocks::paginate(8);
        return view('pos_inventory', ['item' => $data]);
    }

    public function itemSearch($key = null)
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

    public function orders(){
        $distinctSuppliers = SupplierOrder::distinct()->where('status', 'Pending')->pluck('supplier');
        $suppliers = [];

        foreach ($distinctSuppliers as $supplier) {
            $supplierOrders = SupplierOrder::where('supplier', $supplier)->where('status', 'Pending')->get();
            $supplierCount = $supplierOrders->count();
            $supplierQuantitySum = $supplierOrders->sum('quantity');
            $suppliers[$supplier] = [
                'orders' => $supplierOrders,
                'count' => $supplierCount,
                'quantity_sum' => $supplierQuantitySum
            ];
        }

        // dd($suppliers);

        return view('orders', [
            'suppliers' => $suppliers
        ]);

    }

    public function ordersFromSupplier($name)
    {
        $orders = SupplierOrder::where('supplier', $name)
                    ->where('status', 'Pending')
                    ->get();

        // Format the created_at attribute
        $formattedOrders = $orders->map(function ($order) {
            $order->created_at = $order->created_at->format('F d, Y');
            return $order;
        });

        return response()->json(['orders' => $formattedOrders]);
    }

    public function completeOrder(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:supplier_orders,id', // Ensure each ID exists in the supplier_orders table
        ]);

        $ids = $validated['ids'];

        // Get all the orders for the provided IDs
        $orders = SupplierOrder::whereIn('id', $ids)->get();

        foreach ($orders as $order) {
            // Retrieve the item name and quantity from the order
            $itemName = $order->item; // Adjust this if your column name is different
            $quantity = $order->quantity; // Adjust this if your column name is different

            // Find the corresponding item in the Stocks model
            $stock = Stocks::where('item', $itemName)->first();

            if ($stock) {
                // Update the quantity in the Stocks model
                $stock->quantity += $quantity;
                $stock->update_reason = 'New stocks';
                $stock->save();
            }
        }

        // Update the status to 'Delivered' for the selected IDs
        SupplierOrder::whereIn('id', $ids)->update(['status' => 'Delivered']);

        return response()->json(['status' => 'success', 'message' => 'Orders updated successfully.']);
    }



}
