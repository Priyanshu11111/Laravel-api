<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer; 

class CustomerController extends     Controller
{
    public function index()
    {
        return Customer::select('customers_id','firstname','lastname','email','password','password_confirmation')->get();
    }
    public function store(Request $request)
    {
        
        $request->validate(
            [
                'firstname'=>'required',
                'lastname'=>'required',
                'email'=>'required|email',
                'password'=>'required|confirmed',
                'password_confirmation'=>'required',
            ]
            );
            $yo=Customer::create($request->all());
     return response()->json([
        'status'=>true,
        'message'=>"Record Created Successfully",
        'customer'=>$yo
     ],200);
        echo"<pre>";
        print_r($request->all());
        $customer=new Customer;
        $customer->firstname=$request['firstname'];
        $customer->lastname=$request['lastname'];
        $customer->email=$request['email'];
        $customer->password=sha1($request['password']);
        $customer->password_confirmation=$request['password_confirmation'];
        $customer->save();
    }
}

