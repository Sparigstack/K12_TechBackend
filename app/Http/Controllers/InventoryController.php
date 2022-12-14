<?php

namespace App\Http\Controllers;
use App\Models\Personal;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\InventoryManagement;
use App\Models\TicketStatusLog;
use App\Models\Student;
use App\Models\TicketIssue;
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
use Illuminate\Support\Facades\DB;
use App\Models\ErrorLog;
use App\Helpers\Helper;
use App\Exceptions\InvalidOrderException;

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
                $Grade = $data['gradedepartment'];
                $Device_MPN = $data['devicempn'];
                $Serial_number = $data['serialnumber'];
                $Asset_tag = $data['assettag'];
                $Purchase_date = $data['purchasedate'];
                $Building = $data['building'];
                $User_type = $data['usertype'];
                $Parent_guardian_name = $data['parentguardianname'];
                $Parent_guardian_Email = $data['parentguardianemail'];
                $Parent_phone_number = $data['parentphonenumber'];
                $Parental_coverage = $data['parentalcoverage'];
                $Repair_cap = $data['repaircap'];
                $inventory_status = $data['inventorystatus'];

                $savedInventory = InventoryManagement::where('Serial_number', $data['serialnumber'])->first();

                if (isset($savedInventory)) {
                    $SerialNum = $savedInventory->Serial_number;
                    $InventoryID = $savedInventory->ID;
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
                        'Device_MPN' => $Device_MPN ? $Device_MPN : $savedInventory->Device_MPN,
                        'Asset_tag' => $Asset_tag ? $Asset_tag : $savedInventory->Asset_tag,
                        'User_type' => $User_type ? $User_type : $savedInventory->User_type,
                        'Repair_cap' => $Repair_cap ? $Repair_cap : $savedInventory->Repair_cap]);

                    $savedStudent = Student::where('Inventory_Id', $InventoryID)->first();
                    $updatedstudentDetail = Student::where('Inventory_Id', $InventoryID)
                        ->update(['Device_user_first_name' => $Device_user_first_name ? $Device_user_first_name : $savedStudent->Device_user_first_name,
                        'Device_user_last_name' => $Device_user_last_name ? $Device_user_last_name : $savedStudent->Device_user_last_name,
                        'Grade' => $Grade ? $Grade : $savedStudent->Grade,
                        'Building' => $Building ? $Building : $savedStudent->Building,
                        'Parent_guardian_name' => $Parent_guardian_name ? $Parent_guardian_name : $savedStudent->Parent_guardian_name,
                        'Parent_guardian_Email' => $Parent_guardian_Email ? $Parent_guardian_Email : $savedStudent->Parent_guardian_Email,
                        'Parent_phone_number' => $Parent_phone_number ? $Parent_phone_number : $savedStudent->Parent_phone_number,
                        'Parental_coverage' => $Parental_coverage ? $Parental_coverage : $savedStudent->Parental_coverage]);
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
                    $inventory->Device_MPN = $Device_MPN;
                    $inventory->Asset_tag = $Asset_tag;
                    $inventory->User_type = $User_type;
                    $inventory->Repair_cap = $Repair_cap;
                    $inventory->user_id = $userId;
                    $inventory->school_id = $schId;
                    $inventory->inventory_status = $inventory_status;
                    $inventory->save();

                    $Student = new Student;
                    $Student->Device_user_first_name = $Device_user_first_name;
                    $Student->Device_user_last_name = $Device_user_last_name;
                    $Student->Grade = $Grade;
                    $Student->Building = $Building;
                    $Student->Parent_guardian_name = $Parent_guardian_name;
                    $Student->Parent_guardian_Email = $Parent_guardian_Email;
                    $Student->Parent_phone_number = $Parent_phone_number;
                    $Student->Parental_coverage = $Parental_coverage;
                    $Student->Inventory_ID = $inventory->id;
                    $Student->School_ID = $schId;
                    $Student->save();
                }
            }
            return 'success';
        }catch (\Throwable $th) {               
            Helper::Parseerror('uploadInventory',$th->getMessage(),$request->input('ID'),'high');
            return "Invalid CSV";                    
           
        }
    }

    public function getInventories($sid) {
        $inventory = DB::table('inventory_management')
                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_status', 1)
                ->get();

        $decommission = DB::table('inventory_management')
                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_status', 2)
                ->get();

        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventory,
                    'decommisionInvenoty' => $decommission
        ]));
    }

    public function getallInventories($sid, $flag) {

        $inventory = DB::table('inventory_management')
                        ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_status', $flag)
                        ->orderby('inventory_management.ID', 'asc')->get();

        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventory,
        ]));
    }

    function getallDecommission($sid) {

        $inventory = DB::table('inventory_management')
                        ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')->where('inventory_management.inventory_status', 2)
                        ->orderby('inventory_management.ID', 'asc')->get();

        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventory,
        ]));
    }

    public function fetchDeviceDetail($id) {
        $inventorydata = DB::table('inventory_management')
                        ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                        ->where('inventory_management.ID', $id)->first();
        
        $userid = $inventorydata->user_id;
        $user = User::where('id', $userid)->first();
        $username = $user->name;
        
        $ticketalldata = Ticket::where('inventory_id', $id)->get();
        $deviceHistory = array();

        if (isset($ticketalldata)) {
            foreach ($ticketalldata as $ticketdata) {
                $ticketalllog = TicketStatusLog::where('Ticket_id', $ticketdata['ID'])->get();
                $ticketID = $ticketdata['ID'];
                $ticketlog = array();
                foreach ($ticketalllog as $logdata) {

                    $ID = $logdata['ID'];
                    $Ticket_Id = $logdata['Ticket_id'];
                    $old_status = $logdata['Status_from'];
                    $StatusallData = TicketStatus::where('ID', $old_status)->first();
                    $previous_status = $StatusallData->status;
                    $new_status = $logdata['Status_to'];
                    $StatusData = TicketStatus::where('ID', $new_status)->first();
                    $updated_status = $StatusData->status;
                    $date = $logdata['created_at']->format('m-d-Y');
                    $updated_by = $logdata['updated_by_user_id'];
                    $user = User::where('id', $updated_by)->first();
                    $updated_by_user = $user->name;
                    array_push($ticketlog, ["Ticket_id" => $Ticket_Id, "ID" => $ID, "update_by_user" => $updated_by_user, "date" => $date, "updated_status" => $updated_status, "previous_status" => $previous_status]);
                }
                $notes = $ticketdata['notes'];
                $created_user = $ticketdata['user_id'];
                $user_data = User::where('id', $created_user)->first();
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

                array_push($deviceHistory, ["ticketHistory" => $ticketlog, "Ticket_ID" => $ticketID, "Created_by_user" => $created_by_user, "Issue" => $array_issue, "Notes" => $notes, "Status" => $status, "Issue_createdDate" => $created_at]);
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

    public function fetchDeviceDetailforTicket($id, $tid) {
        $inventorydata = DB::table('inventory_management')
                        ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                        ->where('inventory_management.ID', $id)->first();
       
        $userid = $inventorydata->user_id;
        $user = User::where('id', $userid)->first();
        $username = $user->name;
        
        $ticketdata = Ticket::where('ID', $tid)->first();
        $ticketalllog = TicketStatusLog::where('Ticket_id', $tid)->get();
        $ticketlog = array();
        foreach ($ticketalllog as $logdata) {
            $ID = $logdata['ID'];
            $old_status = $logdata['Status_from'];
            $StatusallData = TicketStatus::where('ID', $old_status)->first();
            $previous_status = $StatusallData->status;
            $new_status = $logdata['Status_to'];
            $StatusData = TicketStatus::where('ID', $new_status)->first();
            $updated_status = $StatusData->status;
            $date = $logdata['created_at']->format('m-d-Y');
            $updated_by = $logdata['updated_by_user_id'];
            $user = User::where('id', $updated_by)->first();
            $updated_by_user = $user->name;
            array_push($ticketlog, ["ID" => $ID, "update_by_user" => $updated_by_user, "date" => $date, "updated_status" => $updated_status, "previous_status" => $previous_status]);
        }
        $deviceHistory = array();
        if (isset($ticketdata)) {
            $created_user = $ticketdata['user_id'];
            $user_data = User::where('id', $created_user)->first();
            $created_by_user = $user->name;
            $statusID = $ticketdata['ticket_status'];
            $StatusallData = TicketStatus::where('ID', $statusID)->first();
            $status = $StatusallData->status;
            $ticketID = $ticketdata->ID;
            $ticketIssueData = TicketIssue::where('ticket_Id', $ticketID)->get();
            $array_issue = array();
            foreach ($ticketIssueData as $data) {
                $notes = $ticketdata['notes'];
                $deviceIssue = $data['issue_Id'];
                $issuedata = DeviceIssue::where('ID', $deviceIssue)->first();
                $issue = $issuedata->issue;
                array_push($array_issue, [$issue]);
                $created_at = $data['created_at']->format('m-d-Y');
            }
            array_push($deviceHistory, ["Ticket_history" => $ticketlog, "Created_by_user" => $created_by_user, "Issue" => $array_issue, "Notes" => $notes, "Status" => $status, "Issue_createdDate" => $created_at]);
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
        $inventory->Loaner_device = $request->input('Loanerdevice');   //01     
        $inventory->Device_MPN = $request->input('DeviceMPN');
        $inventory->Asset_tag = $request->input('Assettag');
        $inventory->User_type = $request->input('Usertype');
        $inventory->Repair_cap = $request->input('Repaircap');
        $inventory->user_id = $request->input('userid');
        $inventory->school_id = $request->input('schoolid');
        
        if($request->input('Loanerdevice')== 1){
             $inventory->inventory_status = 3;
        }else{
         $inventory->inventory_status = 1;
        }
        
       

        $checkinventory = InventoryManagement::where('ID', $request->input('ID'))->first();
        if (isset($checkinventory)) {
            $deviceIDfromDB = $checkinventory->ID;
            $deviceId = $request->input('ID');
            if ($deviceIDfromDB == $deviceId) {
                $updatedInventory = InventoryManagement::where('ID', $deviceId)
                    ->update(['Purchase_date' => $request->input('PurchaseDate'),
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
                    'Device_MPN' => $request->input('DeviceMPN'),
                    'Asset_tag' => $request->input('Assettag'),
                    'User_type' => $request->input('Usertype'),
                    'Repair_cap' => $request->input('Repaircap'),
                    'user_id' => $request->input('userid'),
                    'school_id' => $request->input('schoolid')
                ]);

                $updatedStudent = Student::where('Inventory_Id', $deviceId)->update([
                    'Device_user_first_name' => $request->input('Deviceuserfirstname'),
                    'Device_user_last_name' => $request->input('Deviceuserlastname'),
                    'Grade' => $request->input('Grade'),
                    'Building' => $request->input('Building'),
                    'Parent_guardian_name' => $request->input('Parentguardianname'),
                    'Parent_guardian_Email' => $request->input('ParentguardianEmail'),
                    'Parent_phone_number' => $request->input('Parentphonenumber'),
                    'Parental_coverage' => $request->input('Parentalcoverage'),
                ]);

                return "success";
            }
        } else {
            
            $inventory->save();
        if($request->input('flag')== 1){
        $Student = new Student;
        $Student->Device_user_first_name = $request->input('Deviceuserfirstname');
        $Student->Device_user_last_name = $request->input('Deviceuserlastname');
        $Student->Grade = $request->input('Grade');
        $Student->Building = $request->input('Building');
        $Student->Parent_guardian_name = $request->input('Parentguardianname');
        $Student->Parent_guardian_Email = $request->input('ParentguardianEmail');
        $Student->Parent_phone_number = $request->input('Parentphonenumber');
        $Student->Parental_coverage = $request->input('Parentalcoverage');
        $Student->Inventory_Id = $inventory->id;
        $Student->School_ID = $request->input('schoolid');
        $Student->save();
        Student::where('ID', $Student->id)->update(['Inventory_ID' => $inventory->id]);
        }
            
            }
     if($request->input('flag')== 1){       
            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,
                        'student' => $Student,
            ]));
        }
        else{
            return response()->json(
                            collect([
                        'response' => 'success',
                        'msg' => $inventory,                       
            ]));
        }
    
    }
    public function sortbyInventory($sid, $key, $skey) {
    if ($skey == 3) {
                if ($key == 1) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Device_user_first_name", "asc")->where("Loaner_device", 1)->get();
                } elseif ($key == 2) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Device_model", "asc")->where("Loaner_device", 1)->get();
                } elseif ($key == 3) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Grade", "asc")->where("Loaner_device", 1)->get();
                } elseif ($key == 4) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Building", "asc")->where("Loaner_device", 1)->get();
                } elseif ($key == 5) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Serial_number", "asc")->where("Loaner_device", 1)->get();
                } elseif ($key == 6) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Purchase_date", "asc")->where("Loaner_device", 1)->get();
                } else {
                return "error";
                }
    } else {
                if ($key == 1) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Device_user_first_name", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } elseif ($key == 2) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Device_model", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } elseif ($key == 3) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Grade", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } elseif ($key == 4) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("students.Building", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } elseif ($key == 5) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Serial_number", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } elseif ($key == 6) {
                $inventory = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->orderBy("inventory_management.Purchase_date", "asc")->where("inventory_management.inventory_status", $skey)->get();
                } else {
                return "error";
                }
     }
        return response()->json(
                        collect([
                    'response' => 'success',
                    'msg' => $inventory,
        ]));
    }

    public function searchInventory($sid, $key, $flag) {
     if ($key != 'null') {
                  if ($flag == 3) {
                                 $get = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)
                                ->where("inventory_management.Loaner_device", 1)
                                ->where(function ($query) use ($key) {
                                    $query->where('inventory_management.Device_model', 'LIKE', "%$key%");
                                    $query->orWhere('students.Device_user_last_name', 'LIKE', "%$key%");
                                    $query->orWhere('students.Device_user_first_name', 'LIKE', "%$key%");
                                    $query->orWhere('inventory_management.Serial_number', 'LIKE', "%$key%");
                                })->get();
                    } else {
                                 $get = DB::table('inventory_management')
                                ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                                ->where('inventory_management.school_id', $sid)->where("inventory_management.inventory_status", $flag)
                                ->where(function ($query) use ($key) {
                                    $query->where('inventory_management.Device_model', 'LIKE', "%$key%");
                                    $query->orWhere('students.Device_user_last_name', 'LIKE', "%$key%");
                                    $query->orWhere('students.Device_user_first_name', 'LIKE', "%$key%");
                                    $query->orWhere('inventory_management.Serial_number', 'LIKE', "%$key%");
                                })->get();
                            }
     } else {
                    if ($flag == 3) {
                              $get = DB::table('inventory_management')
                              ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                              ->where('inventory_management.school_id', $sid)
                              ->where("inventory_management.Loaner_device", 1)
                              ->get();
                    } else { 
                              $get = DB::table('inventory_management')
                              ->leftJoin('students', 'students.Inventory_ID', '=', 'inventory_management.ID')
                              ->where('inventory_management.school_id', $sid)
                              ->where("inventory_management.inventory_status", $flag)
                              ->get();
                    }
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
            if($actionId == 2){
                     $updatedInventory = InventoryManagement::where('ID', $id)->update(['inventory_status' => 2]);
                     }elseif($actionId == 3) {
                       $updatedInventory = InventoryManagement::where('ID', $id)->update(['inventory_status' => 1]);
                     }else{
                         return 'select active or decommission';
                     }
        }
        return "success";
    }

}
