<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
use App\Models\Ticket;
use App\Models\TicketIssue;
use App\Models\User;
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
        $msg = $request->input('msg');
        $devicearray = $request->input('DeviceIssueArray');
     
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
            foreach ($devicearray as $devicearraydata) {

                 $Issue = new TicketIssue();
                 $Issue->ticket_Id = $data->ID;
                 $Issue->issue_Id = $devicearraydata['ID'];
                 $Issue->user_id = $data->user_id;
                 $Issue->inventory_id = $msg['inventoryId']; 
                 $Issue->save();
             }
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
        return "success";

}
}
    
