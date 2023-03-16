<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
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
        $token = $user->createToken('my-app-token')->plainTextToken;
        
        return response([
            'message' => "success",
            'token' => $token,
        ]);
        
    }   
    public function store(Request $request){
    $request->validate([
            'email' =>'required',
            'password' =>'required',
        ]);
        $user=User::create([
            'email' => $request->username,
            'password' => $request->password,
        ]);
        return $user;
    }   
    public function user(){
        $user = auth()->user();
        if ($user) {
            return response()->json([
                'user' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}