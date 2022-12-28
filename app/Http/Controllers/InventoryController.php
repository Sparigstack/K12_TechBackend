<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketIssue;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\QueryBuilder;
use Exception;
use App\Models\DeviceIssue;

class InventoryController extends Controller {

    public function uploadInventory(Request $request) {
        try {
            $userId = $request->input('ID');
            $schId = $request->input('schId');

            $result = $request->file('file');
            $file = fopen($result, 'r');
            $header = fgetcsv($file);
            $escapedheader = [];
            foreach ($header as $key => $value) {
                $lheader = strtolower($value);
                $escapedItem = preg_replace('/[^a-z]/', '', $lheader);
                array_push($escapedheader, $escapedItem);
            }

            while ($columns = fgetcsv($file)) {
                if ($columns[0] == "") {
                    continue;
                }
                foreach ($columns as $key => &$value) {
                    $value;
                }
              
                $data = array_combine($escapedheader, $columns);
                $Device_manufacturer = $data['devicemanufacturer'];
                $Device_Type = $data['devicetype'];
                $Device_model = $data['devicemodel'];
                $Device_os = $data['deviceos'];
                $Manufacturer_warranty_until = $data['manufacturerwarrantyuntil'];
                $Manufacturer_ADP_until = $data['manufactureradpuntil'];
                $Third_party_extended_warranty_until = $data['thirdpartyextendedwarrantyuntil'];
                $Third_party_ADP_until = $data['thirdpartyadpuntil'];
                $Expected_retirement = $data['expectedretirement'];
                $Loaner_device = $data['loanerdevice'];
                $Device_user_first_name = $data['deviceuserfirstname'];
                $Device_user_last_name = $data['deviceuserlastname'];
                $Student_ID = $data['studentid'];
                $Grade = $data['gradedepartment'];
                $Device_MPN = $data['devicempn'];
                $Serial_number = $data['serialnumber'];
                $Asset_tag = $data['assettag'];
                $Purchase_date = $data['purchasedate'];
                $Building = $data['building'];
                $User_type = $data['usertype'];
                $Parent_guardian_name = $data['parentguardianname'];
                $Parent_Guardian_Email = $data['parentguardianemail'];
                $Parent_phone_number = $data['parentphonenumber'];
                $Parental_coverage = $data['parentalcoverage'];
                $Repair_cap = $data['repaircap'];
                $inventory_status = $data['inventorystatus'];

                $savedInventory = InventoryManagement::where('Serial_number', $data['serialnumber'])->first();

                if (isset($savedInventory)) {
                    $SerialNum = $savedInventory->Serial_number;
                    $CsvSerialNum = $data['serialnumber'];
                    $updatedDetail = InventoryManagement::where('Serial_number', $CsvSerialNum)
                            ->update(['Purchase_date' => $Purchase_date ? $Purchase_date : $savedInventory->Purchase_date,
                        'Device_manufacturer' => $Device_manufacturer ? $Device_manufacturer : $savedInventory->Device_manufacturer,
                        'Device_Type' => $Device_Type ? $Device_Type : $savedInventory->Device_Type,
                        'Device_model' => $Device_model ? $Device_model : $savedInventory->Device_model,
                        'Device_os' => $Device_os ? $Device_os : $savedInventory->Device_os,
                        'Manufacturer_warranty_until' => $Manufacturer_warranty_until ? $Manufacturer_warranty_until : $savedInventory->Manufacturer_warranty_until,
                        'Manufacturer_ADP_until' => $Manufacturer_ADP_until ? $Manufacturer_ADP_until : $savedInventory->Manufacturer_ADP_until,
                        'Third_party_extended_warranty_until' => $Third_party_extended_warranty_until ? $Third_party_extended_warranty_until : $savedInventory->Third_party_extended_warranty_until,
                        'Third_party_ADP_until' => $Third_party_ADP_until ? $Third_party_ADP_until : $savedInventory->Third_party_ADP_until,
                        'Expected_retirement' => $Expected_retirement ? $Expected_retirement : $savedInventory->Expected_retirement,
                        'Loaner_device' => $Loaner_device ? $Loaner_device : $savedInventory->Loaner_device,
                        'Device_user_first_name' => $Device_user_first_name ? $Device_user_first_name : $savedInventory->Device_user_first_name,
                        'Device_user_last_name' => $Device_user_last_name ? $Device_user_last_name : $savedInventory->Device_user_last_name,
                        'Student_ID' => $Student_ID ? $Student_ID : $savedInventory->Student_ID,
                        'Grade' => $Grade ? $Grade : $savedInventory->Grade,
                        'Device_MPN' => $Device_MPN ? $Device_MPN : $savedInventory->Device_MPN,
                        'Asset_tag' => $Asset_tag ? $Asset_tag : $savedInventory->Asset_tag,
                        'Building' => $Building ? $Building : $savedInventory->Building,
                        'Parent_guardian_name' => $Parent_guardian_name ? $Parent_guardian_name : $savedInventory->Parent_guardian_name,
                        'User_type' => $User_type ? $User_type : $savedInventory->User_type,
                        'Parent_Guardian_Email' => $Parent_Guardian_Email ? $Parent_Guardian_Email : $savedInventory->Parent_Guardian_Email,
                        'Parent_phone_number' => $Parent_phone_number ? $Parent_phone_number : $savedInventory->Parent_phone_number,
                        'Parental_coverage' => $Parental_coverage ? $Parental_coverage : $savedInventory->Parental_coverage,
                        'Repair_cap' => $Repair_cap ? $Repair_cap : $savedInventory->Repair_cap]);
                } else {
                    $inventory = new InventoryManagement;
                    $inventory->Purchase_date = $Purchase_date;
                    $inventory->Device_manufacturer = $Device_manufacturer;
                    $inventory->Device_Type = $Device_Type;
                    $inventory->Device_model = $Device_model;
                    $inventory->Device_os = $Device_os;
                    $inventory->Manufacturer_warranty_until = $Manufacturer_warranty_until;
                    $inventory->Manufacturer_ADP_until = $Manufacturer_ADP_until;
                    $inventory->Serial_number = $Serial_number;
                    $inventory->Third_party_extended_warranty_until = $Third_party_extended_warranty_until;
                    $inventory->Third_party_ADP_until = $Third_party_ADP_until;
                    $inventory->Expected_retirement = $Expected_retirement;
                    $inventory->Loaner_device = $Loaner_device;
                    $inventory->Device_user_first_name = $Device_user_first_name;
                    $inventory->Device_user_last_name = $Device_user_last_name;
                    $inventory->Student_ID = $Student_ID;
                    $inventory->Grade = $Grade;
                    $inventory->Device_MPN = $Device_MPN;
                    $inventory->Asset_tag = $Asset_tag;
                    $inventory->Building = $Building;
                    $inventory->Parent_guardian_name = $Parent_guardian_name;
                    $inventory->User_type = $User_type;
                    $inventory->Parent_Guardian_Email = $Parent_Guardian_Email;
                    $inventory->Parent_phone_number = $Parent_phone_number;
                    $inventory->Parental_coverage = $Parental_coverage;
                    $inventory->Repair_cap = $Repair_cap;
                    $inventory->user_id = $userId;
                    $inventory->school_id = $schId;
                    $inventory->inventory_status = $inventory_status;
                    $inventory->save();
                }
            }
            return 'success';
        } catch (\Throwable $th) {
            return "Invalid CSV";
        }
    }

    public function getInventories($sid) {
//        if ($key == "null") {
            $inventory = InventoryManagement::where('school_id', $sid)->where('inventory_status', 1)->orderby('id', 'asc')->paginate(8);
            $decommission = InventoryManagement::where('school_id', $sid)->where('inventory_status', 2)->orderby('id', 'asc')->paginate(8);
            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,
                        'decommisionInvenoty' => $decommission
            ]));
//        } else {
//            $get = InventoryManagement::where('Device_user_first_name', 'LIKE', "%$key%")
//                    ->orWhere('Device_user_last_name', 'LIKE', "%$key%")
//                    ->orWhere('Device_model', 'like', '%' . $key . '%')
//                    ->orWhere('Serial_number', 'like', '%' . $key . '%')
//                    ->paginate(8);
//            return response()->json(
//                            collect([
//                        'response' => 'success',
//                        'msg' => $get
//            ]));
//        }
    }

    public function getallInventories($sid, $flag) {  
            $inventory = InventoryManagement::where('school_id', $sid)->where("inventory_status", $flag)->orderby('id', 'asc')->get();

            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,
            ]));
        
    }

//    function addDecommission(Request $request) {
//        $inventoryId = $request->input('ID');
//        $updateUser = InventoryManagement::where('ID', $inventoryId)->update(['inventory_status' => 2]);
//        return 'success';
//    }

    function getallDecommission($sid, $key) {
        if ($key == "null") {
            $inventory = InventoryManagement::where('school_id', $sid)->where("inventory_status", 2)->orderby('id', 'asc')->get();

            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,
            ]));
        } else {
            $get = InventoryManagement::where('Device_user_first_name', 'LIKE', "%$key%")
                    ->orWhere('Device_user_last_name', 'like', '%' . $key . '%')
                    ->orWhere('Device_model', 'like', '%' . $key . '%')
                    ->orWhere('Serial_number', 'like', '%' . $key . '%')
                    ->get();
            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $get
            ]));
        }
    }

    public function fetchDeviceDetail($id) {
        $inventorydata = InventoryManagement::where('ID', $id)->first();
        $userid = $inventorydata->user_id;
        $user = User::where('id', $userid)->first();
        $username = $user->name; 
        $ticketalldata = Ticket::where('inventory_id', $id)->get();
        $deviceHistory = array();    
        if (isset($ticketalldata)) {
            foreach($ticketalldata as $ticketdata){

            $notes = $ticketdata['notes'];
            $created_user = $ticketdata['user_id'];
            $user_data =  User::where('id', $created_user)->first();
            $created_by_user = $user->name;
            $created_at = $ticketdata['created_at']->format('m-d-Y');
            $statusID = $ticketdata['ticket_status'];
            $StatusallData = TicketStatus::where('ID', $statusID)->first();
            $status = $StatusallData->status;
            $ticketIssueData = TicketIssue::where('ticket_Id', $ticketdata['ID'])->get();
            $array_issue = array();
            foreach ($ticketIssueData as $data) {
                $deviceIssue = $data['issue_Id'];
                $issuedata = DeviceIssue::where('ID', $deviceIssue)->first();
                $issue = $issuedata->issue;
                array_push($array_issue, [$issue]);
            }
             array_push($deviceHistory, ["Created_by_user"=>$created_by_user,"Issue" => $array_issue, "Notes" => $notes, "Status" => $status, "Issue_createdDate" => $created_at]);
            }
           
        }

        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventorydata,
                    'deviceHistory' => $deviceHistory,
                    'userName' => $username,
        ]));
    }
    public function fetchDeviceDetailforTicket($id,$tid) {
        $inventorydata = InventoryManagement::where('ID', $id)->first();
        $userid = $inventorydata->user_id;
        $user = User::where('id', $userid)->first();
        $username = $user->name;
        $ticketdata = Ticket::where('ID', $tid)->first();
        $deviceHistory = array();
        if(isset($ticketdata)){
            $created_user = $ticketdata['user_id'];
            $user_data =  User::where('id', $created_user)->first();
            $created_by_user = $user->name;
            $statusID = $ticketdata['ticket_status'];        
            $StatusallData = TicketStatus::where('ID',$statusID)->first();
            $status = $StatusallData->status;   
        $ticketID = $ticketdata->ID;
        $ticketIssueData = TicketIssue::where('ticket_Id',$ticketID)->get();   
          $array_issue = array(); 
        foreach ($ticketIssueData as $data) {
            $notes = $ticketdata['notes'];
            // $ticketStatusId=$data['ticket_status'];
            // $statusdata = TicketStatus::where('ID', $ticketStatusId)->first();
            // $status = $statusdata->status;
            $deviceIssue = $data['issue_Id'];
            $issuedata = DeviceIssue::where('ID', $deviceIssue)->first();
            $issue = $issuedata->issue;
            array_push($array_issue,[$issue]); 
            $created_at = $data['created_at']->format('m-d-Y');
           
        }
         array_push($deviceHistory, ["Created_by_user"=>$created_by_user,"Issue" => $array_issue, "Notes" => $notes, "Status" => $status, "Issue_createdDate" => $created_at]);
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventorydata,
                    'deviceHistory' => $deviceHistory,
                    'userName' => $username,
        ]));
    }

    public function manualAddEditInventoy(Request $request) {
        $inventory = new InventoryManagement;
        $inventory->Purchase_date = $request->input('PurchaseDate');
        $inventory->Device_manufacturer = $request->input('Devicemanufacturer');
        $inventory->Device_Type = $request->input('DeviceType');
        $inventory->Device_model = $request->input('Devicemodel');
        $inventory->Device_os = $request->input('Deviceos');
        $inventory->Manufacturer_warranty_until = $request->input('Manufacturerwarrantyuntil');
        $inventory->Manufacturer_ADP_until = $request->input('ManufacturerADPuntil');
        $inventory->Serial_number = $request->input('Serialnumber');
        $inventory->Third_party_extended_warranty_until = $request->input('Thirdpartyextendedwarrantyuntil');
        $inventory->Third_party_ADP_until = $request->input('ThirdpartyADPuntil');
        $inventory->Expected_retirement = $request->input('Expectedretirement');
        $inventory->Loaner_device = $request->input('Loanerdevice');
        $inventory->Device_user_first_name = $request->input('Deviceuserfirstname');
        $inventory->Device_user_last_name = $request->input('Deviceuserlastname');
        $inventory->Student_ID = $request->input('StudentID');
        $inventory->Grade = $request->input('Grade');
        $inventory->Device_MPN = $request->input('DeviceMPN');
        $inventory->Asset_tag = $request->input('Assettag');
        $inventory->Building = $request->input('Building');
        $inventory->Parent_guardian_name = $request->input('Parentguardianname');
        $inventory->User_type = $request->input('Usertype');
        $inventory->Parent_guardian_Email = $request->input('ParentguardianEmail');
        $inventory->Parent_phone_number = $request->input('Parentphonenumber');
        $inventory->Parental_coverage = $request->input('Parentalcoverage');
        $inventory->Repair_cap = $request->input('Repaircap');
        $inventory->user_id = $request->input('userid');
        $inventory->school_id = $request->input('schoolid');
        $inventory->inventory_status = 1;
      
        $checkinventory = InventoryManagement::where('ID', $request->input('ID'))->first();
        if (isset($checkinventory)) {
            $deviceIDfromDB = $checkinventory->ID;
            $deviceId = $request->input('ID');
            if ($deviceIDfromDB == $deviceId) {
                $updatedInventory = InventoryManagement::where('ID', $deviceId)->update(['Purchase_date' => $request->input('PurchaseDate'),
                    'Device_manufacturer' => $request->input('Devicemanufacturer'),
                    'Device_Type' => $request->input('DeviceType'),
                    'Device_model' => $request->input('Devicemodel'),
                    'Device_os' => $request->input('Deviceos'),
                    'Manufacturer_warranty_until' => $request->input('Manufacturerwarrantyuntil'),
                    'Manufacturer_ADP_until' => $request->input('ManufacturerADPuntil'),
                    'Serial_number' => $request->input('Serialnumber'),
                    'Third_party_extended_warranty_until' => $request->input('Thirdpartyextendedwarrantyuntil'),
                    'Third_party_ADP_until' => $request->input('ThirdpartyADPuntil'),
                    'Expected_retirement' => $request->input('Expectedretirement'),
                    'Loaner_device' => $request->input('Loanerdevice'),
                    'Device_user_first_name' => $request->input('Deviceuserfirstname'),
                    'Device_user_last_name' => $request->input('Deviceuserlastname'),
                    'Student_ID' => $request->input('StudentID'),
                    'Grade' => $request->input('Grade'),
                    'Device_MPN' => $request->input('DeviceMPN'),
                    'Asset_tag' => $request->input('Assettag'),
                    'Building' => $request->input('Building'),
                    'Parent_guardian_name' => $request->input('Parentguardianname'),
                    'User_type' => $request->input('Usertype'),
                    'Parent_guardian_Email' => $request->input('ParentguardianEmail'),
                    'Parent_phone_number' => $request->input('Parentphonenumber'),
                    'Parental_coverage' => $request->input('Parentalcoverage'),
                    'Repair_cap' => $request->input('Repaircap'),
                    'user_id' => $request->input('userid'),
                    'school_id' => $request->input('schoolid')                    
                ]);
                return "success";
            }
        } else {
            $inventory->save();
            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,
            ]));
        }
    }

    public function sortbyInventory($sid, $key, $skey) {
        if ($key == 1) {
            $inventory = InventoryManagement::orderBy("Device_user_first_name", "asc")->where("school_id", $sid)->where("inventory_status", $skey)->get();
        } elseif ($key == 2) {
            $inventory = InventoryManagement::orderBy("Device_model", "asc")->where("school_id", $sid)->where("inventory_status", $skey)->get();
        } elseif ($key == 3) {
            $inventory = InventoryManagement::orderBy("Grade", "asc")->where("school_id", $sid)->where("inventory_status", $skey)->get();
        } elseif ($key == 4) {
            $inventory = InventoryManagement::orderBy("Building", "asc")->where("school_id", $sid)->where("inventory_status", $skey)->get();
        } elseif ($key == 5) {
            $inventory = InventoryManagement::orderBy("Purchase_date", "asc")->where("school_id", $sid)->where("inventory_status", $skey)->get();
        }  else {
            return "error";
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventory,
        ]));
    }

    public function searchInventory($sid,$key,$flag) {  
        if($key !='null'){
       $get= InventoryManagement::where('school_id', $sid)->where("inventory_status", $flag)->where(function ($query) use ($key) {
                        $query->where('Device_model', 'LIKE', "%$key%");
                        $query->orWhere('Device_user_last_name', 'LIKE', "%$key%");
                        $query->orWhere('Device_user_first_name', 'LIKE', "%$key%");
                        $query->orWhere('Serial_number', 'LIKE', "%$key%");
                    })->get();
        }else{
           
          $get =  InventoryManagement::where('school_id', $sid)->where("inventory_status", $flag)->get();
        }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $get
        ]));
    
    }
    function manageInventoryAction(Request $request) {
        $idArray = $request->input('IDArray');
        $actionId = $request->input('actionid');
        foreach ($idArray as $id) {
            if ($actionId == 2) {
                $updatedInventory = InventoryManagement::where('ID', $id)->update(['inventory_status' => 2]);
            } elseif ($actionId == 3) {
                $updatedInventory = InventoryManagement::where('ID', $id)->update(['inventory_status' => 1]);
            } else {
                return "select any action";
            }
        }
        return "success";
    }

}
