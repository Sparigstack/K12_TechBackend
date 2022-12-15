<?php

namespace App\Http\Controllers;
use App\Models\Personal;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\QueryBuilder;
use Exception;

class InventoryController extends Controller
{
    public function uploadInventory(Request $request)
    {
       try{
        $userId =$request->input('ID');    
        $schId =$request->input('schId');
        
        $result =$request->file('file');
        $file = fopen($result,'r');
        $header = fgetcsv($file);
        $escapedheader=[];       
        foreach($header as $key =>$value){          
        $lheader = strtolower($value);           
         $escapedItem=preg_replace('/[^a-z]/','',$lheader);        
         array_push($escapedheader,$escapedItem);
        }   
    
          while($columns=fgetcsv($file))
         {                
            if($columns[0]=="") 
            {
                continue;
            }
            foreach($columns as $key=> &$value)
            {                   
               $value;               
            }              
            $data = array_combine($escapedheader,$columns);   
            $Purchase_date=$data['purchasedate']; 
            $OEM_warranty_until=$data['oemwarrantyuntil'];
            $Extended_warranty_until=$data['extendedwarrantyuntil'];
            $ADP_coverage=$data['adpcoverage'];
            $OEM=$data['oem'];
            $Device_model=$data['devicemodel'];
            $OS=$data['os'];
            $Serial_number=$data['serialnumber'];
            $Asset_tag=$data['assettag'];
            $Building=$data['building'];
            $Grade=$data['grade'];
            $Student_name=$data['studentname'];
            $Student_ID=$data['studentid'];
            $Parent_email=$data['parentemail'];
            $Parent_phone_number=$data['parentphonenumber'];
            $Parental_coverage=$data['parentalcoverage'];
            $Repair_cap=$data['repaircap'];
            $inventory = new InventoryManagement;           
            $inventory->Purchase_date = $Purchase_date;
            $inventory->OEM_warranty_until = $OEM_warranty_until;
            $inventory->Extended_warranty_until = $Extended_warranty_until;
            $inventory->ADP_coverage = $ADP_coverage;
            $inventory->OEM = $OEM;
            $inventory->Device_model = $Device_model;
            $inventory->OS = $OS;
            $inventory->Serial_number = $Serial_number;
            $inventory->Asset_tag = $Asset_tag;
            $inventory->Building = $Building;
            $inventory->Grade = $Grade;
            $inventory->Student_name = $Student_name;
            $inventory->Student_ID = $Student_ID;
            $inventory->Parent_email = $Parent_email;
            $inventory->Parent_phone_number = $Parent_phone_number;
            $inventory->Parental_coverage = $Parental_coverage;
            $inventory->Repair_cap = $Repair_cap;      
            $inventory->user_id = $userId;
            $inventory->school_id =$schId;
            $inventory->save();                            
         }
      return 'success' ;                                       
}
catch (\Throwable $th) {    
        return "Invalid CSV";
    }
    }

   public function getInventories($sid,$key){
       if($key == "null"){
        $inventory = InventoryManagement::where('school_id',$sid)->orderby('id','asc')->paginate(8); 
        return response()->json(
        collect([
        'response' => 'success',
        'msg' => $inventory,        
         ]));
       }else{
        $get = InventoryManagement::where('Student_name','LIKE',"%$key%")
                ->orWhere('Device_model', 'like', '%' . $key . '%')
                ->orWhere('Serial_number', 'like', '%' . $key . '%')
                ->paginate(8);  
        return response()->json(
         collect([
        'response' => 'success',
        'msg' => $get       
         ]));
       }
       
        
   }
     public function fetchDeviceDetail($id){        
      $os= InventoryManagement::where('ID',$id)->first(); 
      
       return response()->json(
      collect([
      'response' => 'success',
      'msg' => $os,
  ]));
  }
 public function manualAddEditInventoy(Request $request){
            $inventory = new InventoryManagement;
            $inventory->Purchase_date = $request->input('PurchaseDate');
            $inventory->OEM_warranty_until = $request->input('OemWarrantyUntil');
            $inventory->Extended_warranty_until = $request->input('ExtendedWarrantyUntil');
            $inventory->ADP_coverage =$request->input('ADPCoverage');
            $inventory->OEM = $request->input('OEM');
            $inventory->Device_model = $request->input('DeviceModel');
            $inventory->OS = $request->input('OS');
            $inventory->Serial_number = $request->input('SerialNumber');
            $inventory->Asset_tag = $request->input('AssetTag');
            $inventory->Building =$request->input('Building');
            $inventory->Grade = $request->input('Grade');
            $inventory->Student_name = $request->input('StudentName');
            $inventory->Student_ID = $request->input('StudentID');
            $inventory->Parent_email = $request->input('ParentEmail');
            $inventory->Parent_phone_number = $request->input('ParentPhoneNumber');
            $inventory->Parental_coverage = $request->input('ParentalCoverage');
            $inventory->Repair_cap = $request->input('Repaircap');
            $inventory->user_csv_num = $request->input('usercsvnum');
            $inventory->user_id = $request->input('userId');
            $inventory->school_id = $request->input('schoolId');            
            $checkinventory= InventoryManagement::where('ID', $request->input('ID'))->first();  
               if(isset($checkinventory)){
                $deviceIDfromDB = $checkinventory->ID;          
                $deviceId= $request->input('ID');                                    
                if($deviceIDfromDB == $deviceId){
                $updatedInventory=InventoryManagement::where('ID', $deviceId)
                        ->update(['Purchase_date'=>$request->input('PurchaseDate'),
                            'OEM_warranty_until'=>$request->input('OemWarrantyUntil'),
                            'Extended_warranty_until'=>$request->input('ExtendedWarrantyUntil'),
                            'ADP_coverage'=>$request->input('ADPCoverage'),
                            'OEM'=>$request->input('OEM'),
                            'Device_model'=>$request->input('DeviceModel'),
                            'OS'=>$request->input('OS'),
                            'Serial_number'=>$request->input('SerialNumber'),
                            'Asset_tag'=>$request->input('AssetTag'),
                            'Building'=>$request->input('Building'),
                            'Grade'=>$request->input('Grade'),
                            'Student_name'=>$request->input('StudentName'),
                            'Student_ID'=>$request->input('StudentID'),
                            'Parent_email'=>$request->input('ParentEmail'),
                            'Parent_phone_number'=>$request->input('ParentPhoneNumber'),
                            'Parental_coverage'=>$request->input('ParentalCoverage'),
                            'Repair_cap'=>$request->input('Repaircap'),
                            'user_id'=>$request->input('userId'),
                            'school_id'=>$request->input('schoolId')                            
                            ]);
                return "success";
               }
               } else{
                 $inventory->save();   
                return response()->json(
                collect([
                'response' => 'success',
                'msg' => $inventory,
                 ]));
            }
 }
  public function sortbyInventory($sid,$key){
      if($key =="name"){
    $inventory = QueryBuilder::for(InventoryManagement::class)
    ->defaultSort('Student_name')
    ->allowedSorts('Student_name')
    ->get(); 
      return response()->json(
                collect([
                'response' => 'success',
                'msg' => $inventory,
                 ]));
}else{
    return "error";
}
  }

 public function searchInventory($key){
     $get = InventoryManagement::where('Student_name','LIKE',"%$key%")
             ->orWhere('Device_model', 'like', '%' . $key . '%')
             ->orWhere('Serial_number', 'like', '%' . $key . '%')
             ->get();
     return response()->json(
                collect([
                'response' => 'success',
                'msg' => $get,
                 ]));
            }
 }
 
