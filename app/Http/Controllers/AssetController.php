<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\UserActivitylog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $asset=Asset::with('model','ownername')->get();
       return response()->json($asset);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'model' => 'required',
            'ownername' => 'required',
            'status' => 'required',
        ]);
        $asset=new Asset;
        $oldAttributes = $asset->getAttributes();
        $asset->name = $request->input('name');
        $asset->model = $request->input('model');
        $asset->ownername = $request->input('ownername');
        $asset->status =$request->input('status');
        $asset->save();

        $newAttributes = $asset->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Changed Asset: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully',
            'customer' => $asset
        ], 200);
 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $asset = Asset::find($id);
        if (is_null($asset)) {
            return redirect('/customer');
        } else {
            $data = compact('asset');
            return $data;
        } 
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'model' => 'required',
            'ownername' => 'required',
            'status' => 'required',
         
        ]);

        $asset=Asset::find($id);
        $oldAttributes = $asset->getAttributes();
        $asset->name = $request->input('name');
        $asset->model = $request->input('model');
        $asset->ownername = $request->input('ownername');
        $asset->status =$request->input('status');
        $asset->save();

        $newAttributes = $asset->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed

        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Changed Asset: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();   

        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully',
            'customer' => $asset
        ], 200);
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset=Asset::find($id);   
        $assetId = $asset->id;
        $asset->delete();

        $log = new UserActivitylog();
        $log->email =auth()->user()->email;
        $log->modifyuser = 'Deleted Asset'.$assetId;
        $log->date_time =Carbon::now();
        $log->save();
        \Log::debug($log);

        return response()->json([
            'status' => true,
            'message' => "Record deleted successfully",
        ], 200);
    }
    public function authasset(){
        $user = auth()->user();
        $assets = Asset::with(['model', 'ownername'])
        ->where('ownername', $user->id)
        ->get();
        if (!$assets->count()) {
            return response()->json(['error' => 'No request found'], 404);
        }
        foreach ($assets as $asset) {
            $model = $asset->model;
            $ownername = $asset->ownername;
            \Log::debug($model);
            \Log::debug($ownername);
            if (!$model || !$ownername) {
                return response()->json(['error' => 'User or model not found'], 404);
            } 
        }
        return response()->json(['Asset' => $assets], 200);
    }
    public function showuserasset(){
        $requests = $this->authasset()->original;
        if (isset($requests['Asset'])) {
            return response()->json($requests['Asset']); 
        } else {
            return response()->json(['message' => 'No requests found']);
        }
    }
}
