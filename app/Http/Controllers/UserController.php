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
   try{   $user = new User;
      $user->name = $request->input('username');
      $user->email = $request->input('email');
      $user->access_type = $request->input('access');            
      $user->school_id = $request->input('schoolId'); 
      $user->save();
       
      return Response::json(array(
                'status' => "success",
                'msg' => $user
                )); 
    }catch (\Throwable $th) {
            return "something went wrong.".$th;
        }
    }
    function allUser(){
        $user = User::all();
        $array_allUser = array();
        foreach($user as $userdata){
         $useraccesstype = $userdata['access_type'];        
         $Access = Access::where('id',$useraccesstype)->first();
         $AccessType = $Access['access_type'];        
         $Schoolid =$userdata['school_id'];
         $name =$userdata['name'];
         $email =$userdata['email'];
         $id =$userdata['id'];        
          array_push($array_allUser,["Acess"=>$AccessType,"id"=>$id,"school_id"=>$Schoolid,"name"=>$name,"email"=>$email]);
             } 
        return $array_allUser;
    }
    
    function updateUser(Request $request){
     try{   $userID = $request->input('id');
        $userName = $request->input('name');
        $userAccessType = $request->input('accessId');       
       $updateUser =User::where('ID', $userID)->update(['name'=>$userName,'access_type'=>$userAccessType]);
       return "success";
    }catch (\Throwable $th){
        return "something went wrong.";
    }
    
    function deleteUser(){
        
    }
    function allAccess(){
         $Access = Access::all();
         return $Access;
    }    
}
}