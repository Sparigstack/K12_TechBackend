<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Models\ErrorLog;

class ErrorLogController extends Controller
{
    function ErrorLog($errorfrom,$errormsg,$uid){
        $error = new ErrorLog();
        $error->user_id = $uid;
        $error->error_from = $errorfrom;
        $error->error_msg = $errormsg;
        $error->save();
        
    }
}