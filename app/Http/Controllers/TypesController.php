<?php

namespace App\Http\Controllers;

use App\Models\Types;
use Illuminate\Http\Request;
use App\Models\UserActivitylog;
use Carbon\Carbon;
use App\Models\Customer;
use App\Notifications\TypesNotification;

class TypesController extends Controller
{
   public function index(){
    $types = Types::select('id','name', 'comment','created_at','updated_at')->get();
    return response()->json($types);
   }

   public function store(Request $request){
    $rules = [
        'name' => 'required',
        'comment' => 'required',
    ];
    $request->validate($rules);
    $types=new Types;
    $oldAttributes = $types->getAttributes();
    $types->name=$request['name'];
    $types->comment=$request['comment'];
    $types->save();

    $newAttributes = $types->getAttributes(); // Get the updated attributes
    $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

    $log = new UserActivitylog();
    $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
    $log->modifyuser = 'Created Type: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
    $log->date_time = Carbon::now()->utc();
    $log->created_at = Carbon::now()->utc();
    $log->save();   

    $customers=Customer::all();
    foreach($customers as $customer){
        $customer->notify(new TypesNotification($types));
    }
    
    return response()->json([
        'status' => true,
        'message' => 'Type added successfully',
        'customer' => $types
    ], 200);
   }

   public function destroy($id)
   {
    $types = Types::find($id);
    $typeId = $types->id;
    $types->delete();     
    
    $log = new UserActivitylog();
    $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
    $log->modifyuser = 'Deleted Type: ' .$typeId; ; // modify this to reflect the specific action being logged
    $log->date_time = Carbon::now()->utc();
    $log->created_at = Carbon::now()->utc();
    $log->save();   

    return response()->json([
           'status' => true,
           'message' => "Record deleted successfully",
       ], 200);
   }
   public function show($id){
    $types = Types::find($id);
    if (is_null($types)) {
        return redirect('/customer');
    } else {    
        $data = compact('types');
        return $data;
    }
}
    public function update($id ,Request $request){
        $rules = [
            'name' => 'required',
            'comment' => 'required',
        ];
        $request->validate($rules);
        $types = Types::find($id);
        $oldAttributes = $types->getAttributes();
        $types->name = $request['name'];
        $types->comment = $request['comment'];
        $types->save();

        $newAttributes = $types->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Changed types: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

        return response()->json([
            'status' => true,
            'message' => 'Types updated successfully',
            'model' => $types,  
        ], 200);
    }
  public function getTypeName($id)
  {
      $type = Types::find($id);
  
      if (!$type) {
          return response()->json(['error' => 'Type not found'], 404);
      }
  
      $models = $type->models; // retrieve all models associated with this type
  
      if (!$models) {
          return response()->json(['error' => 'No models found for this type'], 404);
      }
  
      return response()->json(['type' => $type], 200);
  }
}
