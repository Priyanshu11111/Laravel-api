<?php

namespace App\Http\Controllers;

use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this line to import the Auth class

class PermissionsController extends Controller
{
    public function Index(){
        
    }
    public function store(Request $request){

        if (!Auth::user()->hasRole('1')) {
            return response()->json(['error' => 'You do not have permission to perform this action.'], 403);
        }
        
        $rules = [
            'name' => 'required',
            'role' => 'required',
            'permissions' => 'required|array', // make sure the "permissions" field is an array
           // make sure the "write" field is an array
        ];

        $request->validate($rules);
        $data = [];

        // an array to hold the records to be inserted
        $permissions = $request['permissions'];
    
        for ($i = 0; $i < count($permissions); $i++) {
            $data[] = [
                'name' => $request['name'],
                'role' => $request['role'],
                'permissions' => $permissions[$i]['permission'],
                'read' => $permissions[$i]['read'],
                'write' => $permissions[$i]['write'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    
        Permissions::insert($data); // bulk insert the records
    
        return response()->json($data);
        
    }
  
}
