<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserActivitylog;


use App\Notifications\MessageRead;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Facades\Auth; // Add this line to import the Auth class

class CustomerController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        \Log::debug($user);
        $customers = Customer::all();
        return response()->json($customers);
    /*     $url=url('/customer');
        $title="Registration Form";
        $data=compact('url');
        return view('/customer')->with($data); */
    }
    public function destroy($id)
    {   
        $customer=Customer::find($id);   
        $customerId = $customer->id;
        $customer->delete();
         
        $log = new UserActivitylog();
        $log->email = $customer->email;
        $log->modifyuser = 'Deleted'.$customerId;
        $log->date_time =Carbon::now();
        $log->save();
        return response()->json([
            'status' => true,
            'message' => "Record deleted successfully",
        ], 200);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $customer=Customer::find($id);
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
public function role(Request $request){
    $rules = [
    'name' => 'required',
    ];
    $request->validate($rules);
    $role= new Roles();
    $role->name = $request['name'];
    $role->save();
    return response()->json([
        'status' => true,
        'message' => 'New Role added',
        'customer' => $role
    ], 200);
}
public function getUserRole(){
    $roles = Roles::with('permissions')->get();
    return response()->json($roles);
}
    public function view()
    {
     $customers = Customer::paginate(10);
    return view('customer-view', compact('customers'));
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
    public function login(Request $request)
{
    $user = Customer::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response([
            'message' => "invalid credentials"
        ], 401);
    }
    $role = Roles::with('permissions')->findOrFail($user->role);
        
    $hasReadPermissions = $role->permissions;
   \Log::debug($hasReadPermissions);
    if (!$hasReadPermissions) {
    return response()->json(['error' => 'You do not have permission to read data.'], 403);
    }

    $log = new UserActivitylog();
    $log->email = $user->email;
    $log->modifyuser = 'User Login';
    $log->date_time = Carbon::now();
    $log->save();

    $token = $user->createToken('my-app-token');

    // set the token expiration time to 30 minutes from now
    $expiresAt = Carbon::now()->addMinutes(30);
    $token->expires_at = $expiresAt;
    return response([
        'message' => "success",
        'token' => $token->plainTextToken,
        'permissions' =>$hasReadPermissions,
        'expires_at' => $expiresAt->toDateTimeString(),
    ]);
}
public function refreshToken(Request $request)
{
    $user = $request->user();
    $token = $user->tokens()->where('name', 'refresh-token')->first();

    if (!$token || !$token->isValid()) {
     $newToken = $user->createToken('refresh-token', ['expires_at' => Carbon::now()->addMinutes(5)]);
    }
    // Create a new refresh token with a new expiration time
    return response([
        'message' => 'success',
        'token' => $newToken->plainTextToken,
        'expires_at' => $newToken->accessToken->expires_at,
    ]);
}
    public function getNotifications($id=null) {
        // Get the authenticated user
        $user = auth()->user();
        
        // Get the notifications for the user
        $notifications = $user->unreadNotifications;
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

        
        // Return the notifications as a response
        return response()->json([
            'notifications' => $notifications,
        /*     'types' => $notifications->pluck('type')->unique()->values(),
            'id' => $notifications->pluck('id')->unique()->values(), */
            'unread_count' => $unreadCount,
        ]);
       
    }
    public function getallnotifications() {
        // Get the authenticated user
        $user = auth()->user();
        
        // Get the notifications for the user
        $notifications = $user->notifications;

    
        $notificationsData = $notifications->map(function ($notification) {
            return  [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at'=>$notification->read_at,
            ];
        });
        
        // Return the notifications as a response
        return response()->json([
            'notifications' => $notificationsData,
        ]);
       
    }
    public function markNotificationsAsRead($id)
{
    // Get the authenticated user
    $user = auth()->user();
    
    \Log::debug($id);

    $notification = $user->notifications()->findOrFail($id);
    $notification->markAsRead();
/* 
    $admin = Customer::where('role', '1')->first();
    $notificationsData = $notifications->map(function ($notification) {
        return  [
            'data' => $notification->data,
        ];
    });
    
    $admin->notify(new MessageRead($user, $notificationsData)); */
    // Return a success response
    return response()->json([
        'success' => true,
        'notifications' => $notification
    ]);
}
public function markAsReadall(){
        // Get the authenticated user
        $user = auth()->user();

        // Mark all unread notifications as read
        $user->unreadNotifications->markAsRead();
    
        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'All notifications have been marked as read.'
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