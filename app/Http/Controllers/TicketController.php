<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\TicketIssue;
use App\Models\User;
use App\Models\StudentInventory;
use App\Models\Student;
use App\Models\InventoryManagement;
use App\Models\LonerDeviceLog;
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
        $msg = $request->input('msg');
        $devicearray = $request->input('DeviceIssueArray');     
        $studentdata = Student::where('Inventory_ID',$msg['inventoryId'])->first();        
        $studentId=$studentdata->ID;
        $data = Ticket::where('inventory_id', $msg['inventoryId'])->first();
        if (isset($data)) {
            
           if($data->ticket_status == 2){
            $ticket = new Ticket();
            $ticket->school_id = $msg['schoolId'];
            $ticket->user_id = $msg['userId'];
            $ticket->inventory_id = $msg['inventoryId'];          
            $ticket->notes = $msg['Notes'];
            $ticket->ticket_status = 1;
            $ticket->save();
            foreach ($devicearray as $devicearraydata) {        

            $Issue = new TicketIssue();
            $Issue->ticket_Id = $ticket->id;
            $Issue->issue_Id = $devicearraydata['ID'];
            $Issue->user_id = $msg['userId'];
            $Issue->inventory_id = $msg['inventoryId']; 
            $Issue->save();
            }
           
            Ticket::where('id', $ticket->id)->update(['ticket_issue_Id' => $Issue->id]);
           }else{ 
               return "ticket already generated";
//            foreach ($devicearray as $devicearraydata) {
//
//                 $Issue = new TicketIssue();
//                 $Issue->ticket_Id = $data->ID;
//                 $Issue->issue_Id = $devicearraydata['ID'];
//                 $Issue->user_id = $data->user_id;
//                 $Issue->inventory_id = $msg['inventoryId']; 
//                 $Issue->save();
//             }
           }                       
            }
         else {
            $ticket = new Ticket();
            $ticket->school_id = $msg['schoolId'];
            $ticket->user_id = $msg['userId'];
            $ticket->inventory_id = $msg['inventoryId'];          
            $ticket->notes = $msg['Notes'];
            $ticket->ticket_status = 1;
            $ticket->save();
            foreach ($devicearray as $devicearraydata) {
        
            $Issue = new TicketIssue();
            $Issue->ticket_Id = $ticket->id;
            $Issue->issue_Id = $devicearraydata['ID'];
            $Issue->user_id = $msg['userId'];
            $Issue->inventory_id = $msg['inventoryId']; 
            $Issue->save();
            }           
            Ticket::where('id', $ticket->id)->update(['ticket_issue_Id' => $Issue->id]);
        }
        if( $msg['lonerDeviceStatus'] == 1){        
        $studentInventory = new StudentInventory();
        $studentInventory->Student_ID = $studentId;
        $studentInventory->Inventory_Id = $msg['inventoryId'];
        $studentInventory->Loner_ID = $msg['lonerId'];
        $studentInventory->save();        
//             
        
        $lonerdevicelog = new LonerDeviceLog();
        $lonerdevicelog->Student_ID = $studentId;
        $lonerdevicelog->Loner_ID = $msg['lonerId'];
        $lonerdevicelog->Start_date = now()->format('Y-m-d');
        $lonerdevicelog->save();
       return "success";  

       }else{
            return "success";
      }
 }
}
    
