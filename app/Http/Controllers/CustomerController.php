<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserActivitylog;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotificationCollection;

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
        $log = new UserActivitylog();
        $log->email = $customer->email;
        $log->modifyuser = 'Deleted';
        $log->date_time =Carbon::now();
        $log->save();
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

        
        $log = new UserActivitylog();
        $log->email = $customer->email;
        $log->modifyuser = 'Updated';
        $log->date_time = Carbon::now();
        $log->save();
        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully',
            'customer' => $customer
        ], 200);
    
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
      
        $log = new UserActivitylog();
        $log->email = $customer->email;
        $log->modifyuser = 'New User Created';
        $log->date_time = Carbon::now();
        $log->save();

    $customer->notify(new WelcomeNotification($customer));

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
    
        $log = new UserActivitylog();
        $log->email = $user->email;
        $log->modifyuser = 'User Login';
        $log->date_time = Carbon::now();
        $log->save();

        $expiresAt = Carbon::now()->addMinutes(1);
        $token = $user->createToken('my-app-token',['expires_at' => $expiresAt])->plainTextToken;


        return response([
            'message' => "success",
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

    } 
    public function refreshToken(Request $request)
{
    $user = $request->user();
    $user->tokens()->delete();

    $expiresAt = Carbon::now()->addMinutes(1);
    $token = $user->createToken('refresh-token',['expires_at' => $expiresAt])->plainTextToken;

    return response([
        'message' => "success",
        'token' => $token,
        'expires_at' => $expiresAt->toDateTimeString(),
    ]);
}
    public function getNotifications($id=null) {
        // Get the authenticated user
        $user = auth()->user();
        
        // Get the notifications for the user
        $notifications = $user->notifications;

        $unreadCount = $user->unreadNotifications->count();

        if ($id !== null) {
            $notification = $notifications->where('id', $id)->first();
        }
    
        
        if ($notifications->isEmpty()) {
            return response()->json([
                'notifications' => [],
                'unread_count' => $unreadCount
            ]);
        }
        $notificationsData = $notifications->map(function ($notification) {
            return  [
                'id' => $notification->id,
                'data' => $notification->data,
            ];
        });
        
        // Return the notifications as a response
        return response()->json([
            'notifications' => $notificationsData,
            'unread_count' => $unreadCount
        ]);
       
    }
    public function markNotificationsAsRead($id)
{
    // Get the authenticated user
    $user = auth()->user();
    \Log::debug($id);
    $notification = $user->notifications()->findOrFail($id);
    $notification->markAsRead();

    
    // Return a success response
    return response()->json([
        'success' => true,
        'notifications' => $notification
    ]);
}
    public function getAuthorizedUserInfo() {
        $user = auth()->user();
        if (!$user) {
            return response([
                'message' => 'User not authenticated'
            ], 401);
        }
        
        return response([
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'password' => $user->password,
            'email' => $user->email,
            'password_confirmation' => $user->password_confirmation,
        ]);
    }

    public function logout(Request $request)
{
    $user = auth()->user();
    if (!$user) {
        return response([
            'message' => 'User not authenticated'
        ], 401);  
    }
    $tokens = $user->tokens;
    foreach ($tokens as $token) {
        if ($token->expires_at && Carbon::now()->gt($token->expires_at)) {
            continue;
        }
        $token->delete();
    }
    $request->user()->tokens()->delete();
        $log = new UserActivitylog();
        $log->email = $user->email;
        $log->modifyuser = 'User Logout';
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();

    return response([
        'message' => 'Logged out successfully'
    ]);
}
    public function updateProfile(Request $request)
{ 
    $user = auth()->user();
    if (!($user instanceof Customer)) {
        return response([
            'message' => 'Invalid user'
        ], 400);
    }
    $previous = clone $user;
    $user->firstname = $request->input('firstname');
    $user->lastname = $request->input('lastname');
    $user->password = Hash::make($request->input('password'), ['rounds' => 10]);
    $user->password_confirmation = Hash::make($request->input('password_confimation'), ['rounds' => 10]);
    $user->save();
    $changes = [];
    if ($user->firstname != $previous->firstname) {
        $changes[] = 'First name: ' . $previous->firstname . ' -> ' . $user->firstname;
    }

    if ($user->lastname != $previous->lastname) {
        $changes[] = 'Last name: ' . $previous->lastname . ' -> ' . $user->lastname;
    }

    if ($user->password != $previous->password) {
        $changes[] = 'Password changed';
    }

    if ($user->password_confirmation != $previous->password_confirmation) {
        $changes[] = 'Password confirmation changed';
    }

    $log = new UserActivitylog();
    $log->email = $user->email;
    $log->modifyuser = 'User updated profile: ' . implode(', ', $changes);
    $log->date_time = Carbon::now();
    $log->save();

    return response([
        'message' => 'Profile updated successfully',
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
    public function showactivity(){
        $customers = UserActivitylog::select('id','email','modifyuser','created_at','updated_at')->get();
    return response()->json($customers);
    }
}