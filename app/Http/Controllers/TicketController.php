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
        $statusid = "";         
        $data = Ticket::where('user_id', $msg['userId'])->first();
        if (isset($data)) {
            foreach ($devicearray as $devicearraydata) {
             $statusid .= "," . $devicearraydata['ID'];
            }
            $IdStatus = ltrim($statusid, ',');
            $TicketIssuedata = TicketIssue::where('ticket_Id', $data->ID)->first();
            if (isset($TicketIssuedata)) {              
                $alreadyExcistId = $TicketIssuedata->issue_Id;
                $IdStatus = ltrim($statusid, ',');
                TicketIssue::where('id', $TicketIssuedata->ID)->update(['issue_Id' =>$IdStatus .','.$alreadyExcistId]);
            }else {
                foreach ($devicearray as $devicearraydata) {
                    $statusid .= "," . $devicearraydata['ID'];
                } $IdStatus = ltrim($statusid, ',');
                $Issue = new TicketIssue();
                $Issue->ticket_Id = $data->ID;
                $Issue->issue_Id = $IdStatus;
                $Issue->user_id = $data->user_id;
                $Issue->save();
            }
        } else {
            $ticket = new Ticket();
            $ticket->school_id = $msg['schoolId'];
            $ticket->user_id = $msg['userId'];
            $ticket->inventory_id = $msg['inventoryId'];          
            $ticket->notes = $msg['Notes'];
            $ticket->save();

            $Issue = new TicketIssue();
            foreach ($devicearray as $devicearraydata) {
                $statusid .= "," . $devicearraydata['ID'];
            }
            $IdStatus = ltrim($statusid, ',');
            $Issue->ticket_Id = $ticket->id;
            $Issue->issue_Id = $IdStatus;
            $Issue->user_id = $msg['userId'];
            $Issue->save();
            Ticket::where('id', $ticket->id)->update(['ticket_issue_Id' => $Issue->id]);
        }
        return "success";

}
}
    
