<?php

namespace App\Http\Controllers;
use App\Models\Personal;
use App\Models\Device;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;

class DeviceController extends Controller
{
    public function addDevice(Request $request)
    {
       $device = new Device; 
       $device->name = $request->input('name');
       $device->type = $request->input('type');
       $device->device_num = $request->input('device_num');
       
       $checkdevice= Device::where('ID', $request->input('ID'))->first();      
        if(isset($checkdevice)){ 
            $deviceIDfromDB = $checkdevice->ID;          
             $deviceId= $request->input('ID');
             $deviceName= $request->input('name');
             $deviceType= $request->input('type');
             $deviceNum= $request->input('device_num');  
             if($deviceIDfromDB == $deviceId){
            $updatedLoginDetail=Device::where('ID', $deviceId)->update(['name'=>$deviceName,'type'=>$deviceType,'device_num'=>$deviceNum]);
        }  
         return   "success";    
        }
        else{
          $device->save();
           return "success";      
    }                           
    }
    public function allDevice(){
        $devices = Device::all();
         return response()->json(
        collect([
        'response' => 'success',
        'msg' => $devices,
    ]));
    }
     public function fetchDevice(Request $request){     
        $device= Device::where('ID', $request->input('ID'))->first(); 
         return response()->json(
        collect([
        'response' => 'success',
        'msg' => $device,
    ]));
    }
    
    public function DeleteDevice(Request $request){
        $device= Device::where('ID', $request->input('ID'))->delete(); 
         return 'success';
    }
    
}