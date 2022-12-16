<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
use Carbon\Carbon;

class ManageTicketController extends Controller
{
    function allTickets($sid,$uid){ 
         $user = User::where('id',$uid)->first();  
         $userName =$user->name;
         $data = Ticket::where('school_id',$sid)->get();
         $array_openTicket = array();
         $array_closeTicket = array();
         
         foreach($data as $ticketdata){    
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y');                
         if($ticketdata['ticket_status'] == 1){                      
           array_push($array_openTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Open","Date"=>$ticketCreateDate]);
         }else{
             array_push($array_closeTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Close","Date"=>$ticketCreateDate]);
         }   
    
         }return response()->json(
          collect([
         'response' => 'success',          
          'username'=>$userName,
          'Closeticket'=>$array_closeTicket,
          'Openticket'=>$array_openTicket,          
    ]));
    }
    
    function changeticketStatus(Request $request){
        $currentStatus = $request->input('status');
        $ticketID = $request->input('ticketID');
        $updateTicketDetail=Ticket::where('ID', $ticketID)->update(['ticket_status'=>$currentStatus]);  
        return response()->json(
                collect([
                'response' => 'success',
                'msg' => $updateTicketDetail,
                 ]));
        
    }     
    
    function getTicketStatusforManageTicket(Request $request){
        $status = TicketStatus::whereIn('ID', [2,3,4,5])->get();
        return $status;
    }
    
    function OpenTickets($sid){
        try{
        $data = Ticket::where('school_id',$sid)->get();
         $array_openTicket = array();        
         foreach($data as $ticketdata){  
         $ticketUserId = $ticketdata['user_id'];
         $user = User::where('id',$ticketUserId)->first(); 
         $ticketCreatedBy = $user->name;
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y');         
         if($ticketdata['ticket_status'] == 1){                      
         array_push($array_openTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Open","Date"=>$ticketCreateDate,"ticketCreatedBy"=>$ticketCreatedBy]);       
        }    
        }                
    return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array_openTicket,          
    ]));
         }catch (\Throwable $th) {    
        return "something went wrong.";
    }
}
    function CloseTickets($sid){
        try{
        $data = Ticket::where('school_id',$sid)->get();
         $array_openTicket = array();                
         foreach($data as $ticketdata){  
         $ticketUserId = $ticketdata['user_id'];
         $user = User::where('id',$ticketUserId)->first(); 
         $ticketCreatedBy = $user->name;
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y');
         if($ticketdata['ticket_status'] == 2){                      
         array_push($array_openTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Close","Date"=>$ticketCreateDate,"ticketCreatedBy"=>$ticketCreatedBy]);       
        }    
    }
    return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array_openTicket,          
    ]));
         }catch (\Throwable $th) {    
        return "something went wrong.";
    }
}    
 }
