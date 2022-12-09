<?php

namespace App\Http\Controllers;
use App\Models\School;
use App\Models\DeviceType;
use App\Models\InventoryManagement;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;

class SchoolController extends Controller
{
    public function addSchool(Request $request)
    {
       $school = new School; 
       $school->name = $request->input('name');   
             
          $school->save();
           return "success";      
    }                           
}

        