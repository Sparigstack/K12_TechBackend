<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ManageTicketController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\OperatingSystemController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
Route::post('/addUsers',[LoginController::class,'addUsers']);
//inventory
Route::post('/upload', [InventoryController::class, 'uploadInventory']); 
Route::get('/getInventories/{sid?}', [InventoryController::class, 'getInventories']); 
Route::get('/getexport', [InventoryController::class, 'getexport']);
Route::post('/addeditmanualInventoy', [InventoryController::class, 'manualAddEditInventoy']);
Route::get('/getallInventories/{sid?}&{flag}', [InventoryController::class, 'getallInventories']);
Route::get('/getallDecommission/{sid?}', [InventoryController::class, 'getallDecommission']);
Route::post('/manageInventoryAction', [InventoryController::class, 'manageInventoryAction']);
//Route::post('/adddecommission', [InventoryController::class, 'addDecommission']);
//device
Route::post('/addNdUpdateDevice', [DeviceTypeController::class, 'addDevice']);
Route::get('/allDevice', [DeviceTypeController::class, 'allDevice']);
Route::get('/fetchDevice/{id?}', [DeviceTypeController::class, 'fetchDevice']);
Route::delete('/deleteDevice', [DeviceTypeController::class, 'DeleteDevice']);
Route::get('/fetchDeviceDetails/{id?}', [InventoryController::class, 'fetchDeviceDetail']);
Route::get('/fetchDeviceDetailforTicket/{id}&{tid}',[InventoryController::class, 'fetchDeviceDetailforTicket']);

//os
Route::post('/addNdUpdateOs', [OperatingSystemController::class, 'addOs']);
Route::get('/allOs', [OperatingSystemController::class, 'allOs']);
Route::get('/fetchOs/{id?}', [OperatingSystemController::class, 'fetchOs']);
Route::delete('/deleteOs', [OperatingSystemController::class, 'DeleteOs']);
//school
Route::post('/addSchool', [SchoolController::class, 'addSchool']);
//search
Route::get('/sortby/{sid?}&{key}&{skey}', [InventoryController::class, 'sortbyInventory']);
Route::get('/searchInventory/{sid}&{key}&{flag}', [InventoryController::class, 'searchInventory']);
//issue

Route::get('/searchInventoryCT/{sid}&{key}', [ManageTicketController::class, 'searchInventoryCT']);
Route::get('/allDeviceIssue', [TicketController::class, 'allIssue']);
Route::post('/generateIssue', [TicketController::class, 'generateIssue']);
Route::get('/allTickets/{sid?}&{uid}', [ManageTicketController::class, 'allTickets']);
Route::get('/getTicketStatus', [ManageTicketController::class, 'getTicketStatusforManageTicket']);
Route::post('/changeticketStatus', [ManageTicketController::class, 'changeticketStatus']);
Route::get('/openTickets/{sid?}&{key}&{flag}', [ManageTicketController::class, 'OpenTickets']);
Route::get('/closeTickets/{sid?}&{key}&{flag}', [ManageTicketController::class, 'CloseTickets']);
Route::get('/getTicketNotes/{sid?}&{id}', [ManageTicketController::class, 'getTicketNotes']);
Route::get('/searchOpenTicket/{sid?}&{key}&{flag}', [ManageTicketController::class, 'searchOpenTicket']);
Route::get('/allLonerDevice/{sid}&{key}', [ManageTicketController::class, 'allLonerDevice']);
Route::get('/lonerdeviceHistory/{id}', [ManageTicketController::class, 'lonerdeviceHistory']);

//Route::get('/filterTickets/{sid?}&{fid}', [ManageTicketController::class, 'filterTickets']);
//Route::get('/searchTicket/{sid?}&{skey}', [ManageTicketController::class, 'searchTicket']);
//user
Route::post('/addUser',[UserController::class,'addUser']);
Route::get('/allUser',[UserController::class,'allUser']);
Route::post('/updateUser',[UserController::class,'updateUser']);
Route::get('/allAccess',[UserController::class,'allAccess']);
Route::get('/getUserById/{uid?}',[UserController::class,'updateUserData']);
Route::delete('/deleteUser', [UserController::class, 'deleteUser']);
