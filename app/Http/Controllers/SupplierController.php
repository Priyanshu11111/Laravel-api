<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\SupplierContact;
use App\Models\UserActivitylog;
use App\Notifications\SuppliersNotification;
use Carbon\Carbon;

class SupplierController extends Controller
{
    public function index(){
        $suppliers = Supplier::select('id','name', 'comment','created_at','updated_at')->get();

        foreach($suppliers as $supplier){
            $supplierContacts = SupplierContact::where('supplier_id', $supplier->id)->get();
            $supplier->contacts = $supplierContacts;
        }
        
        return response()->json($suppliers);
    }
    public function store(Request $request){
        $rules = [
            'name' => 'required',
            'comment' => 'required',
        ];
        $request->validate($rules);
    
        $supplier = new Supplier();
        $oldAttributes = $supplier->getAttributes();
        $supplier->name = $request['name'];
        $supplier->comment = $request['comment'];
        $supplier->save();

        $newAttributes = $supplier->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed
    
        $contacts = $request->input('fields');
    if(isset($contacts) && !empty($contacts)){
        foreach ($contacts as $contact) {
            $supplierContact = new SupplierContact();
            $supplierContact->supplier_name = $contact['supplier_name'];
            $supplierContact->address = $contact['address'];
            $supplierContact->number = $contact['number'];
            $supplierContact->supplier_id = $supplier->id; // Set the foreign key reference
            $supplierContact->save();
        }
    }
    $log = new UserActivitylog();
    $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
    $log->modifyuser = 'Created Supplier: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
    $log->date_time = Carbon::now()->utc();
    $log->created_at = Carbon::now()->utc();
    $log->save();   
    
    $customers=Customer::all();
    foreach($customers as $customer){
        $customer->notify(new SuppliersNotification($supplier,$contacts));
    }
        return response()->json([
            'status' => true,
            'message' => 'Supplier and SupplierContacts added successfully',
            'supplier' => $supplier,
            'supplier_contacts' => $contacts,
        ], 200);
    }
    public function destroy($id)
   {
    $supplier=Supplier::find($id);
    $supplierId = $supplier->id;

     
    $log = new UserActivitylog();
    $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
    $log->modifyuser = 'Deleted Supllier: ' .$supplierId; ; // modify this to reflect the specific action being logged
    $log->date_time = Carbon::now()->utc();
    $log->created_at = Carbon::now()->utc();
    $log->save();   

       $supplier->delete();    
       return response()->json([
           'status' => true,
           'message' => "Record deleted successfully",
       ], 200);
   }
   public function show($id){
    $supplier = Supplier::find($id);
    if (is_null($supplier)) {
        return redirect('/customer');
    } else {    
        $supplierContacts = SupplierContact::where('supplier_id', $id)->get();
        $data = compact('supplier', 'supplierContacts');
        return $data;
    }
}
    public function update($id,Request $request){
       
        $rules = [
            'name' => 'required',
            'comment' => 'required',
        ];
        $request->validate($rules);
        $supplier = Supplier::find($id);
        $oldAttributes = $supplier->getAttributes();
        $supplier->name = $request['name'];
        $supplier->comment = $request['comment'];
        $supplier->save();

        $newAttributes = $supplier->getAttributes(); // Get the updated attributes
        $changes = array_diff_assoc($newAttributes, $oldAttributes); // Get the attributes that were changed */
 
        $log = new UserActivitylog();
        $log->email = auth()->user()->email; // assuming the currently logged in user is the one making the modification
        $log->modifyuser = 'Supplier updated: ' . json_encode($changes); ; // modify this to reflect the specific action being logged
        $log->date_time = Carbon::now()->utc();
        $log->created_at = Carbon::now()->utc();
        $log->save();    

        $contacts = $request->input('fields');
        if (isset($contacts) && !empty($contacts)) {
            $contactData = [];
            foreach ($contacts as $contact) {
                $data = [
                    'supplier_name' => $contact['supplier_name'],
                    'address' => $contact['address'],
                    'number' => $contact['number'],
                    'supplier_id' => $supplier->id,
                    'updated_at' => \Carbon\Carbon::now(),
                ];
                if (isset($contact['id'])) {
                    $contactRecord = SupplierContact::find($contact['id']);
                    if ($contactRecord) {
                        $oldAttributes = $contactRecord->getAttributes();
                        $contactRecord->update($data);
                        $newAttributes = $contactRecord->getAttributes();
                        $changes = array_diff_assoc($newAttributes, $oldAttributes);
                        $log = new UserActivitylog();
                        $log->email = auth()->user()->email;
                        $log->modifyuser = 'Supplier contact updated: ' . json_encode($changes);
                        $log->date_time = Carbon::now()->utc();
                        $log->created_at = Carbon::now()->utc();
                        $log->save();
                    }
                } else {
                    $contactData[] = $data;
                }
            }
            if ($contactData) {
                 SupplierContact::insert($contactData);
                 foreach ($contactData as $data) {
                    $log = new UserActivitylog();
                    $log->email = auth()->user()->email;
                    $log->modifyuser = 'Supplier contact created: ' . json_encode($data);
                    $log->date_time = Carbon::now()->utc();
                    $log->created_at = Carbon::now()->utc();
                    $log->save();
                }
             }
        }
        return response()->json([
            'status' => true,
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier,
        ], 200);
    }
}

