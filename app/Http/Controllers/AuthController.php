<?php

namespace App\Http\Controllers;

use App\Models\Authentication;
use App\Models\Admin;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;

class AuthController extends Controller
{
    public function authLoginCashier(Request $request)
    {
        // dd($request);
        $pos_number = $request->input('POS');
        $request->validate([
            'POS' => 'required',
            'name' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if($user->role === 'Cashier'){
                $cashier_name = Auth::user()->name; // Assuming the user model has a 'name' attribute
                session(['cashier_name' => $cashier_name]);
                session(['pos_number' => $pos_number]);
                return redirect()->intended(route('dashboard'));
            }
        }

        return redirect()
                ->route('welcome')
                ->with('error', 'Authentication failed. Please check your credentials.');
    }

    public function authLoginAdmin(Request $request){
        // Validation
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);
    
        // Extract credentials
        $credentials = $request->only('name', 'password');
    
        // Authentication Attempt
        if (Auth::guard('admin')->attempt($credentials)) {
            // dd(Auth::check());
    
            // If authenticated, get the user
            $user = Auth::guard('admin')->user();
            // dd($user)
    
            if($user->role === 'Admin'){
                session(['admin_name' => $user->name]); // Use $user directly
                return redirect()->intended(route('office.dashboard'));
            }
        }
        
        return redirect()
               ->back()
               ->with('error', 'Authentication failed. Please check your credentials.');
    }

    public function adminLogout(Request $request){
        Auth::guard('admin')->logout();
        return redirect()->route('office.login');
    }
    


    public function addCashier(Request $request){
        $name = $request->input('cashier_name');
        $password = $request->input('password');
        $role = "Cashier";

        $hashedPassword = Hash::make($password);

        $data = ([
            'name' => $name,
            'password' => $hashedPassword,
            'role' => $role
        ]);

        $create = Authentication::create($data);

        if(!$create){
            return view('welcome');
        }
        
        $cashiers = Authentication::all();
        return view('backoffice/cashiers/cashiers', ['cashiers' => $cashiers]);
    }

    public function addAdmin(){
        $name = "Admin";
        $password = "admin1";
        $role = "Admin";

        $hashedPassword = Hash::make($password);

        $data = ([
            'name' => $name,
            'password' => $hashedPassword,
            'role' => $role
        ]);

        $create = Admin::create($data);

        if(!$create){
            return view('welcome');
        }
        return redirect()->route('office.login');
    }

    public function endShift(Request $request) // Add Request type hinting
    {
        $cashier_name = session('cashier_name');
        $shift = Shift::where('cashier', $cashier_name)->delete();
        // Log out the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the session token to prevent CSRF attacks
        $request->session()->regenerateToken();

        // Redirect to the login page
        return redirect()->route('welcome'); // Adjust the redirect path as needed
    }
}
