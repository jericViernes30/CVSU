<?php

namespace App\Http\Controllers;

use App\Models\Authentication;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function authLogin(Request $request)
    {
        $pos_number = $request->input('POS');
        $request->validate([
            'POS' => 'required',
            'name' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('name', 'password');

        if(Auth::attempt($credentials)){
            $cashier_name = Auth::user()->name; // Assuming the user model has a 'name' attribute
            session(['cashier_name' => $cashier_name]);
            session(['pos_number' => $pos_number]);
            return redirect()->intended(route('dashboard'));
        }
        return redirect(route('welcome'));
    }

    public function addCashier(){
        $name = 'Jeric';
        $password = 'jeric';

        $hashedPassword = Hash::make($password);

        $data = ([
            'name' => $name,
            'password' => $hashedPassword
        ]);

        $create = Authentication::create($data);

        if(!$create){
            return view('welcome');
        }
    }

    public function logout(){

    }
}
