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

class InventoryController extends Controller
{
    public function uploadInventory(Request $request)
    {
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
            $inventory->save();
                    
         }
      return 'success' ;                                       
}

   public function getInventories(){
        $inventory = InventoryManagement::all();
         return response()->json(
        collect([
        'response' => 'success',
        'msg' => $inventory,
    ]));
   }
}
