<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function addUser(Request $request){
      $user = new User;
      $user->name = $request->input('UserName');
      $user->email = $request->input('email');
      $user->access_type = $request->input('access');            
      $user->school_id = $request->input('schoolId'); 
      $user->save();
       
      return Response::json(array(
                'status' => "success",
                'msg' => $user
                )); 
    }
    
    function allUser(){
        $user = User::all();
        return $user;
    }
    
    function updateUser(Request $request){
        $userID = $request->input('id');
        $userName = $request->input('name');
        $userAccessType = $request->input('accessId');       
       $updateUser =User::where('ID', $userID)->update(['name'=>$userName,'access_type'=>$userAccessType]);
       return "success";
    }
    
    function deleteUser(){
        
    }
    function allAccess(){
         $Access = Access::all();
         return $Access;
    }    
}