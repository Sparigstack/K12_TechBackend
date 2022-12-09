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
      $user->microsoft_id = $request->input('microsoftId');
      $user->remember_token = $request->input('accessToken'); 
      $user->school_id = $request->input('schoolId'); 
     

       $usersavedemail= User::where('email', $request->input('email'))->first();
        if(isset($usersavedemail)){       
             
               $email = $usersavedemail->email;
                     $useremail=$request->input('email');
           if($email == $useremail){
                     
                
          $userAccessToken =$request->input('accessToken');   
          $userMicrosoftId =$request->input('microsoftId');
          $userGoogleId =$request->input('googleId');
          $flag = $request->input('flag');
          if($flag == 1){
               $updatedLoginDetail=User::where('email', $useremail)->update(['remember_token'=>$userAccessToken,'google_id'=>$userGoogleId]);
          }else{
               $updatedLoginDetail=User::where('email', $useremail)->update(['remember_token'=>$userAccessToken,'microsoft_id'=>$userMicrosoftId]);
          }
         
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
    
    
    function loginValidation(Request $request){
         
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

