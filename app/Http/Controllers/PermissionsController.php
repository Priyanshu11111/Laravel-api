<?php

namespace App\Http\Controllers;

use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this line to import the Auth class
use Carbon\Carbon;
use App\Models\UserActivitylog;

class PermissionsController extends Controller
{
    public function Index(){
        $roles = Permissions::with('roles')->get();
    return response()->json($roles);
    }
    public function store(Request $request){        
        $rules = [
            'name' => 'required',
            'role' => 'required',
            'module' => 'required|array', // make sure the "permissions" field is an array
           // make sure the "write" field is an array
        ];

        $request->validate($rules);
        $data = [];

        // an array to hold the records to be inserted
        $permissions = $request['module'];
    
        for ($i = 0; $i < count($permissions); $i++) {
            $data[] = [
                'name' => $request['name'],
                'role' => $request['role'],
                'module' => $permissions[$i]['module'],
                'read' => $permissions[$i]['read'],
                'write' => $permissions[$i]['write'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Permissions::insert($data); // bulk insert the records
    
        return response()->json($data);
        
    }
    public function destroy($id)
    {   
        $permissions=Permissions::find($id);   
        $permmissionId = $permissions->id;
        $permissions->delete();
       
        $log = new UserActivitylog();
        $log->email = auth()->user()->email;
        $log->modifyuser = 'Deleted Permission'.$permmissionId;
        $log->date_time =Carbon::now();
        $log->save();

        return response()->json([
            'status' => true,
            'message' => "Record deleted successfully",
        ], 200);
    }
  
}
