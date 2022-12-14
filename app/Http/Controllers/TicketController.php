<?php

namespace App\Http\Controllers;
use App\Models\OperatingSystem;
use App\Models\DeviceIssue;
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
    
    
}