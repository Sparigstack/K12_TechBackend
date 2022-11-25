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
      
      $useremail=$request->input('email');             
      $usersavedemail= User::where('email', $useremail)->first();
      
        if(isset($useremail)){
            $email = $usersavedemail->email;
         
           if($email == $useremail){   
          $userAccessToken =$request->input('accessToken');
          $updatedLoginDetail=User::where('email', $useremail)->update(['remember_token'=>$userAccessToken]);
                return Response::json(array(
                'status' => "success",
                'msg' => $user
                ));      
        }
        }
        else{
       $user->save();
           return Response::json(array(
           'status' => "success",
           'msg' => $user
       ));
    }
    }
    
    function loginValidation(Request $request,){
         
        $useremail=$request->input('email');                
        $user= User::where('email', $useremail)->first();
        
        if(isset($user)){
         $email = $user->email;
         
           if($email == $useremail){       
                $userAccessToken =$request->input('accessToken');
                $updatedLoginDetail=User::where('email', $useremail)->update(['remember_token'=>$userAccessToken]);
                return Response::json(array(
                'status' => "success",
                'msg' => $user
                ));      
          }        
        }
        else{      
         return Response::json(array(
          'status' => "Error",     
       ));          
    } 
}
}

