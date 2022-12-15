<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;

class TicketController extends Controller
{
    
    public function allIssue(){     
        $issues = DeviceIssue::all();
         return response()->json(
        collect([
        'response' => 'success',
        'msg' => $issues,
    ]));        
}
     public function generateIssue(Request $request){
         
try{       
        $msg = $request->input('msg');
        $devicearray = $request->input('DeviceIssueArray');       
        foreach($devicearray as $devicearraydata){
        $issues = new Ticket();    
        $issues->device_issue_id = $devicearraydata['ID']; 
        $issues->school_id = $msg['schoolId'];
        $issues->user_id  = $msg['userId'];      
        $issues->inventory_id = $msg['inventoryId'];
        $issues->ticket_status =$msg['status'];
        $issues->notes =$msg['Notes'];       
        $issues->save();
           
        }   
     return "success";
        
        
    }catch (\Throwable $th){
      return "Error";  
    }
}
}
    
