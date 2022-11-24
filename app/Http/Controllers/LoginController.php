<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;



class LoginController extends Controller
{
    function Register(Request $request) {
     $user = new User;
     $user->name = $request->input('name');
      $user->email = $request->input('email');
      $user->google_id = $request->input('googleId');
      $user->remember_token = $request->input('accessToken');
       $user->save();
       return $user;
    }
 
}

