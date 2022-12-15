<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\User;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;

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
         if($ticketdata['ticket_status'] == 1){                      
           array_push($array_openTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Open"]);
         }else{
             array_push($array_closeTicket,["serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"Close"]);
         }   
    
         }return response()->json(
          collect([
         'response' => 'success',          
          'username'=>$userName,
          'Closeticket'=>$array_closeTicket,
          'Openticket'=>$array_openTicket    
    ]));
    }
    
}
    
