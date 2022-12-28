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
         $statusID = $ticketdata['ticket_status'];        
         $StatusallData = TicketStatus::where('ID',$statusID)->first();
         $status = $StatusallData->status;
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $serialNum = $Inventory['Serial_number'];     
         $userdId = $Inventory['user_id'];
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y');  
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();        
          foreach($Issuealldata as $Issuedata)
          {       
              
                 $issueId =  $Issuedata->issue_Id;
////                 $StatusID = $Issuedata->ticket_status;
//                        
//                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
//                 $status = $StatusallData->status;
//                 $statusID =$StatusallData->ID;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();
                 $firstName = $inventory_student->Device_user_first_name;
                 $lastName = $inventory_student->Device_user_last_name;
                 $Device_model =$inventory_student->Device_model;

                
          }
          if($statusID == 2){                     
            array_push($array_closeTicket,["Device_model"=>$Device_model,"firstName"=>$firstName,"lastName"=>$lastName,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","ticket_status"=>$status,"Date"=>$ticketCreateDate]);
        }else{
            
            array_push($array_openTicket,["Device_model"=>$Device_model,"firstName"=>$firstName,"lastName"=>$lastName,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","ticket_status"=>$status,"Date"=>$ticketCreateDate]);
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
            $ticketStatusID = $request->input('Status');
            $idArray = $request->input('IssueIDArray');
          
            foreach ($idArray as $ids) {             
                $updatedTicketStatus = Ticket::where('ID',$ids['TicketID'])->update(['ticket_status'=>$ticketStatusID]);
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
    
    function OpenTickets($sid,$key,$flag){
         try{
         $data = Ticket::where('school_id',$sid)->get();
       
         $array_openTicket = array();             
         foreach($data as $ticketdata){  
         
         $ticketInventoryID = $ticketdata['inventory_id'];
         $statusID = $ticketdata['ticket_status'];        
         $StatusallData = TicketStatus::where('ID',$statusID)->first();
         $status = $StatusallData->status;
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();
         $InventoryID = $Inventory['ID'];        
         $serialNum = $Inventory['Serial_number'];     
         $grade =$Inventory['Grade'];
         $building =$Inventory['Building'];
         $userdId = $Inventory['user_id'];
         $notes = $ticketdata['notes'];
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y'); 
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();       
         $array_issue = array(); 
          foreach($Issuealldata as $Issuedata)
          {                             
                 $ID =$Issuedata->ID;          
                 $device_issue_id =  $Issuedata->issue_Id;
//                 $StatusID = $Issuedata->ticket_status;
//                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
//                 $status = $StatusallData->status;
//                 $statusID =$StatusallData->ID;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();
                 $firstName = $inventory_student->Device_user_first_name;
                 $lastName = $inventory_student->Device_user_last_name;
                 $Device_model =$inventory_student->Device_model;
                 $device_issue_data = DeviceIssue::where('ID',$device_issue_id)->first();
                 $device_issue =$device_issue_data->issue;              
                 array_push($array_issue,[$device_issue]);          
                 
          }
          if($statusID != 2){                                         
            array_push($array_openTicket,["Inventory_ID"=>$InventoryID,"Device_isuue"=>$array_issue,"Device_model"=>$Device_model,"firstName"=>$firstName,"lastName"=>$lastName,"IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
        }
         }          
        if($key =="null"){
            return response()->json(
          collect([
         'response' => 'success',                           
         'Openticket'=>$array_openTicket,          
    ]));                   
        }elseif($key == 1){
         if($flag == 'as'){
                $array = collect($array_openTicket)->sortBy('Grade')->values();
               
            }else{
                $array = collect($array_openTicket)->sortByDesc('Grade')->values();
            }
         return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array,          
            ]));  
        
         }elseif($key == 2){       
          if($flag == 'as'){
                $array = collect($array_openTicket)->sortBy('Building')->values();
               
            }else{
                $array = collect($array_openTicket)->sortByDesc('Building')->values();
            }
         return response()->json(
          collect([
         'response' => 'success',                            
         'Openticket'=>$array,          
            ]));  
        
         }elseif($key == 3){       
          if($flag == 'as'){
                $array = collect($array_openTicket)->sortBy('ticket_status')->values();
               
            }else{
                $array = collect($array_openTicket)->sortByDesc('ticket_status')->values();
            }
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
    function CloseTickets($sid,$key,$flag){
       try{
         $data = Ticket::where('school_id',$sid)->get();
         $array_closeTicket = array();             
        foreach($data as $ticketdata){    
         $ticketInventoryID = $ticketdata['inventory_id'];
         $statusID = $ticketdata['ticket_status'];        
         $StatusallData = TicketStatus::where('ID',$statusID)->first();
         $status = $StatusallData->status;
         $Inventory = InventoryManagement::where('id',$ticketInventoryID)->first();        
         $InventoryID = $Inventory['ID'];
         $serialNum = $Inventory['Serial_number'];
         $studentName = $Inventory['Device_user_last_name'] . $Inventory['Device_user_first_name'];
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
                 $device_issue_id =  $Issuedata->issue_Id;
//                 $StatusID = $Issuedata->ticket_status;
//                 $StatusallData = TicketStatus::where('ID',$StatusID)->first();
//                 $status = $StatusallData->status;
//                 $statusID =$StatusallData->ID;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();
                 $firstName = $inventory_student->Device_user_first_name;
                 $lastName = $inventory_student->Device_user_last_name;
                 $Device_model =$inventory_student->Device_model;
//                 $device_issue_data = DeviceIssue::where('ID',$device_issue_id)->first();
//                 $device_issue =$device_issue_data->issue;              
//                 array_push($array_issue,$device_issue);          
                 
          }
          if($statusID == 2){                                         
            array_push($array_closeTicket,["Inventory_ID"=>$InventoryID,"Device_model"=>$Device_model,"firstName"=>$firstName,"lastName"=>$lastName,"IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
        }
          
         } 
                   

          
        if($key =="null"){
            return response()->json(
          collect([
         'response' => 'success',                           
         'Closeticket'=>$array_closeTicket,          
    ]));                   
        }elseif($key == 1){       
          if($flag == 'as'){
                $array = collect($array_closeTicket)->sortBy('Grade')->values();
               
            }else{
                $array = collect($array_closeTicket)->sortByDesc('Grade')->values();
            }
         return response()->json(
          collect([
         'response' => 'success',                            
         'Closeticket'=>$array,          
            ]));  
        
         }elseif($key == 2){      
          if($flag == 'as'){
                $array = collect($array_closeTicket)->sortBy('Building')->values();
               
            }else{
                $array = collect($array_closeTicket)->sortByDesc('Building')->values();
            }
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

 function searchInventoryCT($sid,$key){
        if($key !='null'){
       $get= InventoryManagement::where('school_id', $sid)->where(function ($query) use ($key) {
                        $query->where('Device_model', 'LIKE', "%$key%");
                        $query->orWhere('Device_user_last_name', 'LIKE', "%$key%");
                        $query->orWhere('Device_user_first_name', 'LIKE', "%$key%");
                        $query->orWhere('Serial_number', 'LIKE', "%$key%");
                    })->get();
        }else{
           
          $get =  InventoryManagement::where('school_id', $sid)->get();
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $get
        ]));
 }

}
       
