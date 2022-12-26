<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketIssue;
use App\Models\User;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
class ManageTicketController extends Controller
{
    function allTickets($sid,$uid){ 
         $data = Ticket::where('school_id',$sid)->get();
         $array_openTicket = array();
         $array_closeTicket = array();
       
         foreach($data as $ticketdata){    
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $userdId = $Inventory['user_id'];
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y');  
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();        
          foreach($Issuealldata as $Issuedata)
          {       
              
                 $issueId =  $Issuedata->issue_Id;
                 $StatusID = $Issuedata->ticket_status;
                        
                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
                 $status = $StatusallData->status;
                 $statusID =$StatusallData->ID;
                 
                 if($statusID == 2){                     
                     array_push($array_closeTicket,["userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>"close","Date"=>$ticketCreateDate]);
                 }else{
                     
                     array_push($array_openTicket,["userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","studentName"=>$studentName,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
                 }
          }
         }
          $openTicket = collect($array_openTicket)->unique('ticketid')->values();
           $closeTicket = collect($array_closeTicket)->unique('ticketid')->values();
          
          return response()->json(
          collect([
         'response' => 'success',                  
          'Openticket'=>$openTicket,
          'Closeticket'=>$array_closeTicket,          
    ]));
      
    }
    
    function changeticketStatus(Request $request) {
        try {
//            $idArray = $request->input('IDArray');
            $ticketStatusID = $request->input('Status');
            $idArray = $request->input('IssueIDArray');
            foreach ($idArray as $ids) {
//                return $ids['IssueID'];
                $updatedTicketStatus = TicketIssue::where('ticket_Id', $ids['TicketID'])->where('ID',$ids['IssueID'])->update(['ticket_status' => $ticketStatusID]);
//                return $updatedTicketStatus;
//                $TicketUserId =
            }
            return "success";
        } catch (\Throwable $th) {
            return "something went wrong.";
        }
    }

    function getTicketStatusforManageTicket(Request $request){
        $status = TicketStatus::all();
        return $status;
    }
    
    function OpenTickets($sid,$key){
         try{
         $data = Ticket::where('school_id',$sid)->get();
         $array_openTicket = array();             
         foreach($data as $ticketdata){    
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $grade =$Inventory['Grade'];
         $building =$Inventory['Building'];
         $userdId = $Inventory['user_id'];
         $notes = $ticketdata['notes'];
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y'); 
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();        
          foreach($Issuealldata as $Issuedata)
          {         
//              return  $Issuedata;
                 $ID =$Issuedata->ID;          
                 $issueId =  $Issuedata->issue_Id;
                 $StatusID = $Issuedata->ticket_status;
                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
                 $status = $StatusallData->status;
                 $statusID =$StatusallData->ID;
                 
                 if($statusID != 2){                                         
                     array_push($array_openTicket,["IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"studentName"=>$studentName,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
                 }
          }
         }          
        if($key =="null"){
            return response()->json(
          collect([
         'response' => 'success',                           
         'Openticket'=>$array_openTicket,          
    ]));                   
        }elseif($key == 1){
         $array = collect($array_openTicket)->sortBy('Grade')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array,          
            ]));  
        
         }elseif($key == 2){
         $array = collect($array_openTicket)->sortBy('Building')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array,          
            ]));  
        
         }elseif($key == 3){
         $array = collect($array_openTicket)->sortByDesc('ticket_status')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array,          
            ]));  
        
         }else{            
          return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array_openTicket,          
            ]));
         }        
        } catch (\Throwable $th) {    
        return "something went wrong.";
    }
}
    function CloseTickets($sid,$key){
       try{
         $data = Ticket::where('school_id',$sid)->get();
         $array_closeTicket = array();             
        foreach($data as $ticketdata){    
         $ticketInventoryID = $ticketdata['inventory_id'];
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Student_name'];
         $grade =$Inventory['Grade'];
         $building =$Inventory['Building'];
         $userdId = $Inventory['user_id'];
         $notes = $ticketdata['notes'];
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y'); 
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();        
          foreach($Issuealldata as $Issuedata)
          {        
                 $ID =$Issuedata->ID;
                 $issueId =  $Issuedata->issue_Id;
                 $StatusID = $Issuedata->ticket_status;
                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
                 $status = $StatusallData->status;
                 $statusID =$StatusallData->ID;
                 
                 if($statusID == 2){                                         
                     array_push($array_closeTicket,["IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"studentName"=>$studentName,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
                 }
          }
         } 
                   

          
        if($key =="null"){
            return response()->json(
          collect([
         'response' => 'success',                           
         'Closeticket'=>$array_closeTicket,          
    ]));                   
        }elseif($key == 1){
         $array = collect($array_closeTicket)->sortBy('Grade')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Closeticket'=>$array,          
            ]));  
        
         }elseif($key == 2){
         $array = collect($array_closeTicket)->sortBy('Building')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Closeticket'=>$array,          
            ]));  
        
         }elseif($key == 3){
         $array = collect($array_closeTicket)->sortByDesc('ticket_status')->values();
         return response()->json(
          collect([
         'response' => 'success',                            
         'Closeticket'=>$array,          
            ]));  
        
         }else{            
          return response()->json(
          collect([
         'response' => 'success',                            
         'Closeticket'=>$array_closeTicket,          
            ]));
         }        
        } catch (\Throwable $th) {    
        return "something went wrong.";
    }  
  
}
}

