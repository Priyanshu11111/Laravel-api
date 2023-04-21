<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Types;

use App\Models\Models;

use Illuminate\Http\Request;
use App\Models\UserActivitylog;
use App\Notifications\ModelsNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ModelsController extends Controller
{
    public function index(){
        $models = $this->getAllModels()->original['models'];
        return response()->json($models); 
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

    
        $customers=Customer::all();
        foreach($customers as $customer){
            $customer->notify(new ModelsNotification($model));
        }

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
    
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }
    
        $type = $model->type; // assuming you have a "model" relationship defined in the Types model

        if (!$type) {
            return response()->json(['error' => 'Type not found'], 404);
        }
        return response()->json(['model' => $model], 200);        
        if (is_null($model)) {
            return redirect('/customer');
        } else {
            $data = compact('model');
            return $data;
        }
    }
    public function viewmodels($id){
        $model = Models::with(['type','supplier'])->find($id);
    
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }
        $type = $model->type; // assuming you have a "model" relationship defined in the Types model

        if (!$type) {
            return response()->json(['error' => 'Type not found'], 404);
        }
        return response()->json(['model' => $model], 200);        if (is_null($model)) {
            return redirect('/customer');
        } else {
            $data = compact('model');
            return $data;
        }
    }
    public function getModelName($id)
    {
        $model = Models::with(['type','supplier'])->find($id);
     
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }
        $type = $model->type; // assuming you have a "model" relationship defined in the Types model

        $supplier = $model->supplier;
        if (!$supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }
        if (!$type) {
            return response()->json(['error' => 'Type not found'], 404);
        }
        return response()->json(['model' => $model], 200);
    }
    public function getAllModels(){
        $models = Models::with(['type','supplier'])->get();

    if (!$models->count()) {
        return response()->json(['error' => 'No models found'], 404);
    }
    foreach ($models as $model) {
        $type = $model->type; // assuming you have a "model" relationship defined in the Types model

        if (!$type) {
            return response()->json(['error' => 'Type not found'], 404);
        }
    }
    return response()->json(['models' => $models], 200);
}
}   
