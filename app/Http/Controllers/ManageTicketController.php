<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketIssue;
use App\Models\TicketStatusLog;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentInventory;
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
use Illuminate\Support\Facades\DB;
use App\Models\LonerDeviceLog;
use App\Exceptions\InvalidOrderException;
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
         $ticketCreatedByUserName = $ticketdata['user_id'];
         $userdata =User::where('id',$ticketCreatedByUserName)->first();
         $ticketCreatedBy = $userdata->name;
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();        
                foreach($Issuealldata as $Issuedata)
                {                     
                 $issueId =  $Issuedata->issue_Id;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();
                 $student_data = Student::where('Inventory_ID',$inventory_student->ID)->first();
                 $firstName = $student_data->Device_user_first_name;
                 $lastName = $student_data->Device_user_last_name;
                 $Device_model =$inventory_student->Device_model;               
                }
                if($statusID == 2){                      
                 array_push($array_closeTicket,["Device_model"=>$Device_model,"studentname"=>$firstName.' '.$lastName,"TicketCreatedBy"=>$ticketCreatedBy,"username"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","ticket_status"=>$status,"Date"=>$ticketCreateDate]);
                }else{            
                 array_push($array_openTicket,["Device_model"=>$Device_model,"studentname"=>$firstName.' '.$lastName,"TicketCreatedBy"=>$ticketCreatedBy,"username"=>$userName,"serialNum"=>$serialNum,"ticketid"=>"$ticketID","ticket_status"=>$status,"Date"=>$ticketCreateDate]);
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
            $ticketupdateduserId = $request->input('UserId');
            $idArray = $request->input('IssueIDArray');
            $flag = $request->input('Flag');
            $closestatus = $request->input('closestatus');                
            foreach ($idArray as $ids) { 
                 $ticketlog = new TicketStatusLog();                
                 $ticketlog->Ticket_id = $ids['TicketID'];
                 $ticketdata = Ticket::where('ID',$ids['TicketID'])->first();
                 $ticketlog->Status_from = $ticketdata->ticket_status;
                 $ticketlog->Status_to = $ticketStatusID;
                 $ticketlog->updated_by_user_id = $ticketupdateduserId;
                 $ticketlog->save();
                 
                 $inventoryID = $ticketdata->inventory_id;
                 $studentinentorydata = StudentInventory::where('Inventory_Id',$inventoryID)->first();
                 
                if($studentinentorydata == ""){
                    $updatedTicketStatus = Ticket::where('ID',$ids['TicketID'])->update(['ticket_status'=>$ticketStatusID]);
                }else{
                    $lonerID = $studentinentorydata->Loner_ID; 
                if($flag ==1){
                    if($closestatus == 1){                       
                            $updateStudentInventory = StudentInventory::where('Inventory_Id',$inventoryID)->update(['Loner_ID'=>null,'Inventory_Id'=>$lonerID]);
                            $updateInventory = InventoryManagement::where('id',$lonerID)->update(['Loaner_device'=>0]);
                            $updatedTicketStatus = Ticket::where('ID',$ids['TicketID'])->update(['ticket_status'=>$ticketStatusID]);
                    }                 
                    else{
                        $updateStudentInventory = StudentInventory::where('Inventory_Id',$inventoryID)->update(['Loner_ID'=>null]);
                        $updatedTicketStatus = Ticket::where('ID',$ids['TicketID'])->update(['ticket_status'=>$ticketStatusID]);
                    }
                   $date= now()->format('Y-m-d');
                   LonerDeviceLog::where('Loner_ID',$lonerID)->update(['End_date'=>$date]);     
              }else{ 
                        $updatedTicketStatus = Ticket::where('ID',$ids['TicketID'])->update(['ticket_status'=>$ticketStatusID]);
                }  
              }             
        }
        return "success";
        }catch (\Throwable $th) {
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
         $ticketInventoryID = $ticketdata['inventory_id'];//inventoryid          
         $statusID = $ticketdata['ticket_status'];        
         $StatusallData = TicketStatus::where('ID',$statusID)->first();
         $status = $StatusallData->status;
         $notes = $ticketdata['notes'];
         $Inventory = InventoryManagement::with('student')->where('ID',$ticketInventoryID)->first();      
         $serialNum = $Inventory->Serial_number;     
         $Device_model = $Inventory->Device_model;
         $firstName = $Inventory->student->Device_user_first_name ?? "";
         $lastName = $Inventory->student->Device_user_last_name ??'' ;
         $grade =$Inventory->student->Grade ?? '' ;
         $building =$Inventory->student->Building ??'' ;
         $student_Id = $Inventory->student->ID ??'' ;  
         $userdId = $Inventory['user_id'];        
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;   
         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y'); 
         $ticketCreatedByUserName = $ticketdata['user_id'];
         $userdata =User::where('id',$ticketCreatedByUserName)->first();
         $ticketCreatedBy = $userdata->name;
         
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();      
       
         $array_issue = array(); 
                foreach($Issuealldata as $Issuedata)
                {                             
                 $ID =$Issuedata->ID;          
                 $device_issue_id =  $Issuedata->issue_Id;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();                                
                 $device_issue_data = DeviceIssue::where('ID',$device_issue_id)->first();
                 $device_issue =$device_issue_data->issue;              
                 array_push($array_issue,[$device_issue]);                           
                }
                if($statusID != 2){                                         
                 array_push($array_openTicket,["student_Id"=>$student_Id,"Inventory_ID"=>$ticketInventoryID,"Device_isuue"=>$array_issue,"Device_model"=>$Device_model,"studentname"=>$firstName.' '.$lastName,"IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"TicketCreatedBy"=>$ticketCreatedBy,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
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
         $ticketInventoryID = $ticketdata['inventory_id'];//inventoryid           
         $statusID = $ticketdata['ticket_status'];        
         $StatusallData = TicketStatus::where('ID',$statusID)->first();
         $status = $StatusallData->status;
         $notes = $ticketdata['notes'];
         $Inventory = InventoryManagement::with('student')->where('ID',$ticketInventoryID)->first();      
         $serialNum = $Inventory->Serial_number;     
         $Device_model = $Inventory->Device_model;
         $firstName = $Inventory->student->Device_user_first_name ?? "";
         $lastName = $Inventory->student->Device_user_last_name ??'' ;
         $grade =$Inventory->student->Grade ?? '' ;
         $building =$Inventory->student->Building ??'' ;
         $student_Id = $Inventory->student->ID ??'' ;  
         $userdId = $Inventory['user_id'];        
         $user = User::where('id',$userdId)->first();          
         $userName =$user->name;   
         
         $ticketID =$ticketdata['ID'];
         $ticketCreateDate =$ticketdata['created_at']->format('d-m-Y'); 
         $ticketCreatedByUserName = $ticketdata['user_id'];
         $userdata =User::where('id',$ticketCreatedByUserName)->first();
         $ticketCreatedBy = $userdata->name;
         
         $Issuealldata =TicketIssue::where('ticket_Id',$ticketdata['ID'])->get();      
       
         $array_issue = array(); 
                foreach($Issuealldata as $Issuedata)
                {                             
                 $ID =$Issuedata->ID;          
                 $device_issue_id =  $Issuedata->issue_Id;
                 $issue_inventory_id = $Issuedata->inventory_id;
                 $inventory_student = InventoryManagement::where('id',$issue_inventory_id)->first();                                
                 $device_issue_data = DeviceIssue::where('ID',$device_issue_id)->first();
                 $device_issue =$device_issue_data->issue;              
                 array_push($array_issue,[$device_issue]);                           
                }
                if($statusID == 2){                                         
                 array_push($array_closeTicket,["student_Id"=>$student_Id,"Inventory_ID"=>$ticketInventoryID,"Device_model"=>$Device_model,"studentname"=>$firstName.' '.$lastName,"IssuedbID"=>$ID,"Building"=>$building,"Grade"=>$grade,"notes"=>$notes,"TicketCreatedBy"=>$ticketCreatedBy,"userName"=>$userName,"serialNum"=>$serialNum,"ticketid"=>$ticketID,"ticket_status"=>$status,"Date"=>$ticketCreateDate]);
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
                  
       $get= DB::table('inventory_management')->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_management.school_id', $sid)->where('inventory_management.Loaner_device',0)
               ->where(function ($query) use ($key) {
                        $query->where('inventory_management.Device_model', 'LIKE', "%$key%");
                        $query->orWhere('students.Device_user_last_name', 'LIKE', "%$key%");
                        $query->orWhere('students.Device_user_first_name', 'LIKE', "%$key%");
                        $query->orWhere('inventory_management.Serial_number', 'LIKE', "%$key%");
                    })->get();
        }else{           
          $get = DB::table('inventory_management')->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_management.school_id', $sid)->where('inventory_management.Loaner_device',0)->get();
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $get
        ]));
 }
function allLonerDevice($sid,$key){
    if($key !='null'){
         $lonerdata = DB::table('student_inventories')->pluck('Loner_ID')->all();  
  
       $get= InventoryManagement::with('student')->where('school_id', $sid)->where('Loaner_device',1)
               ->where(function ($query) use ($key) {
                        $query->where('Device_model', 'LIKE', "%$key%");                        
                        $query->orWhere('Serial_number', 'LIKE', "%$key%");
                    })->whereNotIn('ID', $lonerdata)->get();
        }else{    
            $get = InventoryManagement::with('student')->where('school_id',$sid)->where('inventory_management.Loaner_device',1)->get();
//          $get = DB::table('inventory_management')->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_management.school_id', $sid)->where('inventory_management.Loaner_device',1)->get();
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $get
        ]));
}
function lonerdeviceHistory($id){
     $lonerdevicelogdata = LonerDeviceLog::where('Loner_ID',$id)->first();   
     if(isset($lonerdevicelogdata)){
     $startDate = $lonerdevicelogdata->Start_date;
     $endDate = $lonerdevicelogdata->End_date ;
     $array_lonerdevice = array();
     $lonerdata = InventoryManagement::where('id',$id)->first();
     $lonermodel =  $lonerdata->Device_model;
     $studentinventories = StudentInventory::where('Loner_ID',$id)->first();      
     $lonerstudentdata = Student::where('ID',$studentinventories->Student_ID)->first();    
     $lonername  =$lonerstudentdata->Device_user_first_name.' '.$lonerstudentdata->Device_user_last_name;   
     $studentwhouselonerdevice = $lonerdevicelogdata->Student_ID;
     $studentwhouselonerdevicedata = Student::where('ID',$studentwhouselonerdevice)->first();               
      array_push($array_lonerdevice,["lonerdevicemodel"=>$lonermodel,"startDate"=>$startDate,"endDate"=>$endDate,"whoUseLonerDevice"=>$lonername]);
     return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $array_lonerdevice
        ]));
     
 }else{
     return response()->json(
                        collect([
                    'response' => 'Error',
                   
        ]));

 }
}
}

