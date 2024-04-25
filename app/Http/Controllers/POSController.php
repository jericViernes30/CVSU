<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\History;
use App\Models\Shift;
use Carbon\Carbon;

use Illuminate\Http\Request;

class POSController extends Controller
{

    public function dashboard(){
        $started_shift = false;
        $cashier_name = session('cashier_name');
        $today = Carbon::today();
        $shift = Shift::where('cashier', $cashier_name)->get();
        // $shift_date = $shift->created_at;
        $menu = Menu::all();
        $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
        $ticket = $lastTicket->ticket + 1;
        $cashier_name = session('cashier_name');
        $shift = Shift::where('cashier', $cashier_name)->get();
        
        if($shift->isEmpty()){
            return view('cashier');
        } else {
            return view('dashboard', ['menus' => $menu, 'ticket' => $ticket]);
        }
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

        return view('order_details', [
            'ticket' => $ticket,
            'customer' => $customer,
            'foods' => $foods
        ]);
    }

    public function history(){
        // Get today's date
        $today = Carbon::today();

        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $today)->get();

        return view('history', ['history' => $history]);
    }

    public function sale(Request $request){
        $menu = Menu::all();
        $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
        $ticket = $lastTicket->ticket + 1;
        $cashier_name = session('cashier_name');
        $change = $request->input('cash') - $request->input('pay');

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

        if($insert){
            return redirect()->intended(route('dashboard', ['menus' => $menu, 'ticket' => $ticket]));
        }
    }

    public function purchasedDate(Request $request){
        $date = $request->input('date');
        // Query to fetch records with matching date
        $history = History::whereDate('created_at', $date)->get();

        return view('history', ['history' => $history]);
    }

    public function cashier(){
        $cashier_name = session('cashier_name');
        $shift = Shift::where('cashier', $cashier_name)->get();
    
        if(!$shift->isEmpty()){
            // If no shift is found, render the cashier view
            return view('cashier_menu');
        } else {
            // If at least one shift is found, render the cashier_menu view
            return view('cashier');
        }
    }

    public function startShift(Request $request){
        $cashier_name = session('cashier_name');
        $pos = session('pos_number');
        
        $starting_cash = $request->input('starting_cash');

        $data = [
            'cashier' => $cashier_name,
            'POS_number' => $pos,
            'starting_cash' => $starting_cash
        ];

        $data['closing_cash'] = 0;

        Shift::create($data);

        $menu = Menu::all();
        $lastTicket = Customer::latest('ticket')->first(); // Retrieve the latest ticket
        $ticket = $lastTicket->ticket + 1;
        $cashier_name = session('cashier_name');
        return view('dashboard', ['menus' => $menu, 'ticket' => $ticket]);
    }
}
