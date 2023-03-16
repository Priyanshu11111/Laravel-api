<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;




class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::select('id','firstname', 'lastname','email')->get();
        return response()->json($customers);
    /*     $url=url('/customer');
        $title="Registration Form";
        $data=compact('url');
        return view('/customer')->with($data); */
    }
    public function destroy(Customer $customer)
    {
        $customer->delete();    
        return response()->json([
            'status' => true,
            'message' => "Record deleted successfully",
        ], 200);
    }

    public function update(Customer $customer, Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:customers,email,'.$customer->id,
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $customer->firstname = $request->input('firstname');
        $customer->lastname = $request->input('lastname');
        $customer->email = $request->input('email');
        $customer->password =Hash::make($request->input('password'), ['rounds' => 10]);
        $customer->password_confirmation =Hash::make($request['password_confirmation'],['rounds' => 10]);
        $customer->save();

        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully',
            'customer' => $customer
        ], 200);

        $activitylog=[
            'email' => $customer->email,
            ''

        ];
        DB::table('user_activitylogs')->insert($activitylog);
    }


    public function store(Request $request)
    {
        
        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ];
        $request->validate($rules);

        $customer = new Customer;
        $customer->firstname = $request['firstname'];
        $customer->lastname = $request['lastname'];
        $customer->email = $request['email'];
        $customer->password =Hash::make($request->input('password'), ['rounds' => 10]);
        $customer->password_confirmation = Hash::make($request['password_confirmation'],['rounds' => 10]);
        $customer->save();

    return response()->json([
        'status' => true,
        'message' => 'Record added successfully',
        'customer' => $customer
    ], 200);
}

    public function view()
    {
           $customers = Customer::paginate(10);
    return view('customer-view', compact('customers'));
    }
    public function delete($id){
        Customer::find($id)->delete();
        return redirect()->back();
    }
   
    public function edit($id){
        $customer=Customer::find($id);
        if(is_null($customer)){
            return redirect('/customer-view');
        }else{
            $title='Update Customer';
            $url=url('/customer/update')."/".$id;
            $data=compact('customer','url','title');
            return view('customer')->with($data);
        }
    }
    public function login(Request $request){
        $user=Customer::where('email',$request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => "invalid credentials"
            ], 401);
        }
        
    
        $token = $user->createToken('my-app-token')->plainTextToken;
    
        return response([
            'message' => "success",
            'token' => $token,
        ]);

    } 
    public function show($id){
    $customer = Customer::find($id);
    if (is_null($customer)) {
        return redirect('/customer');
    } else {
        $data = compact('customer');
        return $data;
    }
}
}