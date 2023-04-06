<?php

namespace App\Http\Controllers;

use App\Models\Models;
use Illuminate\Http\Request;
use App\Models\UserActivitylog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ModelsController extends Controller
{
    public function index(){
        $model= Models::get();
        return response()->json($model); 
    }
    public function store(Request $request){
        $rules = [
            'types' => 'required',
            'supplier' => 'required',
            'name' => 'required',
            'alias' => 'required',
        ];
        $request->validate($rules);
        $model = new Models();
        $oldAttributes = $model->getAttributes();
        $model->types = $request['types'];
        $model->supplier = $request['supplier'];
        $model->name = $request['name'];
        $model->alias = $request['alias'];
        $model->save();

        $newAttributes = $model->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Created Model: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

    
        return response()->json([
            'status' => true,
            'message' => 'Model saved successfully',
            'supplier' => $model,
        ], 200);

    }
    public function destroy($id)
    {
        $model=Models::find($id);   
        $modelId = $model->id;
        $model->delete();
        
        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Deleted Model: ' .$modelId ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

        return response()->json([
            'status' => true,
            'message' => "Record deleted successfully",
        ], 200);
    }
    public function update($id,Request $request){
        $rules = [
            'types' => 'required',
            'supplier' => 'required',
            'name' => 'required',
            'alias' => 'required',
        ];
        $request->validate($rules);
        $model=Models::find($id);
        $oldAttributes = $model->getAttributes(); // Get the original attributes before they're updated
        $model->types = $request['types'];
        $model->supplier = $request['supplier'];
        $model->name = $request['name'];    
        $model->alias = $request['alias'];
        $model->save();

        $newAttributes = $model->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Changed In Models: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

        return response()->json([
            'status' => true,
            'message' => 'Model updated successfully',
            'model' => $model,  
        ], 200);
    }
    public function show($id){
        $model = Models::find($id);
        if (is_null($model)) {
            return redirect('/customer');
        } else {
            $data = compact('model');
            return $data;
        }
    }
}
